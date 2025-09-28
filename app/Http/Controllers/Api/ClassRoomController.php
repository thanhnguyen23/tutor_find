<?php

namespace App\Http\Controllers\Api;

use App\Enums\ClassRoomAction;
use App\Enums\UserBookingAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\CreateClassRoomRequest;
use App\Http\Resources\ClassRoomResource;
use App\Jobs\EndClassroomJob;
use App\Models\ClassRoom;
use App\Events\ClassRoomCreated;
use App\Events\ClassRoomEnded;
use App\Events\ClassRoomStarted;
use App\Events\ParticipantUpdated;
use App\Repositories\Contracts\ClassRoomUserJoinRepositoryInterface;
use App\Repositories\Contracts\ClassRoomRepositoryInterface;
use App\Repositories\Contracts\UserBookingRepositoryInterface;
use App\Repositories\Contracts\UserRepositoryInterface;
use App\Services\ZoomService;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Validator;

class ClassRoomController extends Controller
{
    protected $repo;
    protected $userBookingRepo;
    protected $zoomService;
    protected $classRoomUserJoinRepo;
    protected $userRepo;
    protected $classRoomRepo;

    const MIN_USER = 2;
    const max_participants = 2;
    const participants_count_default = 0;
    const endClassroomJobDelay = 1;

    public function __construct(
        ClassRoomRepositoryInterface $repo,
        UserBookingRepositoryInterface $userBookingRepo,
        ClassRoomUserJoinRepositoryInterface $classRoomUserJoinRepo,
        UserRepositoryInterface $userRepo)
    {
        $this->repo = $repo;
        $this->userBookingRepo = $userBookingRepo;
        $this->classRoomUserJoinRepo = $classRoomUserJoinRepo;
        $this->userRepo = $userRepo;
    }

    public function index(Request $request)
    {
        $user = auth()->user()->uid;
        $perPage = (int)($request->per_page ?? 10);
        $filters = [
            'status' => $request->status,
            'search' => $request->search,
        ];

        $paginator = $this->repo->paginate($filters, $perPage, $user);

        return ClassRoomResource::collection($paginator)
            ->additional([
                'success' => true,
                'list_status' => ClassRoom::$LIST_STATUS,
            ]);
    }

    public function show($id)
    {
        $classroom = $this->repo->find((int)$id);
        $timeInfo = (new ClassRoomResource($classroom))->toArray(request())['time_info'] ?? null;

        return (new ClassRoomResource($classroom))
            ->additional([
                'success' => true,
                'time_info' => $timeInfo
            ]);
    }

    public function store(CreateClassRoomRequest $request)
    {
        try {
            $data = $request->all();
            $user = auth()->user();

            // Check classroom tồn tại
            if ($this->userBookingRepo->hasClassroom($data['booking_id'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Booking này đã có classroom rồi, mỗi booking chỉ được tạo 1 classroom'
                ], 400);
            }

            // Lấy booking
            $booking = $this->userBookingRepo->findForClassroom($data['booking_id']);

            // Kiểm tra quyền: chỉ gia sư của booking mới được tạo classroom
            if ($booking->tutor_uid !== $user->uid) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bạn chỉ có thể tạo lớp học cho booking của mình'
                ], 403);
            }

            // Kiểm tra trạng thái booking - chỉ cho phép tạo classroom khi booking đã confirmed
            if ($booking->status !== UserBookingAction::Confirmed->value) {
                return response()->json([
                    'success' => false,
                    'message' => 'Chỉ có thể tạo lớp học cho booking đã được xác nhận'
                ], 422);
            }

            // Validate thời gian
            [$scheduledStart, $scheduledEnd] = $this->resolveScheduleTimes(
                $booking,
                $data['scheduled_start_time'] ?? null,
                $data['scheduled_end_time'] ?? null
            );

            if (!$scheduledStart || !$scheduledEnd) {
                return response()->json([
                    'success' => false,
                    'message' => 'Thời gian không hợp lệ'
                ], 400);
            }

            $scheduled_duration = Carbon::parse($scheduledEnd)->diffInMinutes(Carbon::parse($scheduledStart));

            DB::beginTransaction();

            try {
                // Payload lưu DB (không dùng Zoom nữa)
                $payload = [
                    'booking_id'          => $data['booking_id'],
                    'tutor_uid'           => $data['tutor_uid'] ?? $booking->tutor_uid,
                    'student_uid'         => $data['student_uid'] ?? $booking->uid,
                    'topic'               => $data['topic'],
                    'agenda'              => $data['agenda'] ?? null,
                    'scheduled_start_time'=> Carbon::parse($scheduledStart)->format('Y-m-d H:i:s'),
                    'scheduled_end_time'  => Carbon::parse($scheduledEnd)->format('Y-m-d H:i:s'),
                    'scheduled_duration'  => $scheduled_duration,
                    'status'              => ClassRoomAction::Scheduled->value,
                    'participants_count'  => self::participants_count_default,
                    'max_participants'    => self::max_participants,
                ];

                $classroom = $this->repo->create($payload);

                // Cập nhật trạng thái booking sang awaiting_class
                $this->userBookingRepo->updateStatus(
                    $booking->id,
                    UserBookingAction::AwaitingClass->value
                );

                // Refresh booking để có trạng thái mới
                $booking->refresh();

                DB::commit();

                // Broadcast classroom created to both participants
                try {
                    $classroom->load(['booking.tutor', 'booking.user']);
                    broadcast(new ClassRoomCreated($classroom));
                } catch (\Throwable $e) {
                    Log::warning('Broadcast classroom.created failed', ['message' => $e->getMessage()]);
                }

                return (new ClassRoomResource($classroom))->additional([
                    'success' => true,
                    'message' => 'Tạo lớp học thành công và booking đã chuyển sang trạng thái chờ lớp học'
                ]);

            } catch (\Throwable $e) {
                DB::rollBack();
                throw $e;
            }

        } catch (\Throwable $e) {
            Log::error('Create classroom failed', [
                'message' => $e->getMessage(),
                'booking_id' => $data['booking_id'] ?? null,
                'user_uid' => $user->uid ?? null
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi tạo lớp học',
            ], 500);
        }
    }

    /**
     * So sánh & resolve thời gian.
     */
    protected function resolveScheduleTimes($booking, $scheduledStart, $scheduledEnd): array
    {
        // Sử dụng trực tiếp start_time và end_time của booking (đã là datetime)
        $dbStart = $booking->start_time ? Carbon::parse($booking->start_time) : null;
        $dbEnd   = $booking->end_time ? Carbon::parse($booking->end_time) : null;

        // Nếu FE không gửi -> lấy DB
        if (!$scheduledStart || !$scheduledEnd) {
            return [$dbStart, $dbEnd];
        }

        // Nếu gửi khác DB -> reject (so sánh theo định dạng chuẩn)
        $scheduledStartStd = Carbon::parse($scheduledStart);
        $scheduledEndStd = Carbon::parse($scheduledEnd);

        if ($scheduledStartStd != $dbStart || $scheduledEndStd != $dbEnd) {
            return [null, null];
        }

        return [$scheduledStartStd, $scheduledEndStd];
    }


    public function start($id)
    {
        $classroom = $this->repo->find((int)$id);

        if (!in_array($classroom->status, [ClassRoomAction::Scheduled->value, ClassRoomAction::Pending->value], true)) {
            return response()->json([
                'success' => false,
                'message' => 'Không thể bắt đầu lớp học với trạng thái hiện tại'
            ], 422);
        }

        // Kiểm tra thời gian - chỉ cho phép bắt đầu khi đến giờ học
        $now = Carbon::now();
        $scheduledStart = Carbon::parse($classroom->scheduled_start_time);

        // Cho phép bắt đầu sớm 5 phút trước giờ học
        $allowedStartTime = $scheduledStart->copy()->subMinutes(5);

        if ($now->lt($allowedStartTime)) {
            return response()->json([
                'success' => false,
                'message' => 'Chưa đến giờ học. Lớp học sẽ bắt đầu lúc ' . $scheduledStart->format('H:i d/m/Y'),
                'scheduled_start_time' => $scheduledStart->format('Y-m-d H:i:s'),
                'current_time' => $now->format('Y-m-d H:i:s')
            ], 422);
        }

        // Cập nhật database (status và actual_start_time)
        $payloadUpdate = [];

        if (in_array($classroom->status, [ClassRoomAction::Scheduled->value, ClassRoomAction::Pending->value], true)) {
            $payloadUpdate['status'] = ClassRoomAction::Started->value;
            if (empty($classroom->actual_start_time)) {
                $payloadUpdate['actual_start_time'] = $now;
            }
        }

        $classroom->update($payloadUpdate);

        // Dispatch EndClassroomJob với thời gian kết thúc dự kiến
        $scheduledEnd = Carbon::parse($classroom->scheduled_end_time);
        $delayMinutes = $now->diffInMinutes($scheduledEnd);

        // Đảm bảo delay không âm và không quá lớn
        $delayMinutes = max(1, min($delayMinutes, 480)); // Tối đa 8 tiếng

        dispatch(new EndClassroomJob($id))->delay(now()->addMinutes($delayMinutes));

        Broadcast(new ClassRoomStarted($classroom));

        return (new ClassRoomResource($classroom))
            ->additional([
                'success' => true,
                'message' => 'Bắt đầu lớp học thành công',
                'scheduled_end_time' => $scheduledEnd->format('Y-m-d H:i:s'),
                'auto_end_delay_minutes' => $delayMinutes
            ]);
    }

    public function end($id)
    {
        $classroom = $this->repo->find((int) $id);

        if ($classroom->status !== ClassRoomAction::Started->value) {
            return response()->json([
                'success' => false,
                'message' => 'Không thể kết thúc lớp học với trạng thái hiện tại'
            ], 422);
        }

        // Dispatch EndClassroomJob với thời gian kết thúc dự kiến
        $now = Carbon::now();
        $scheduledEnd = Carbon::parse($classroom->scheduled_end_time);
        $delayMinutes = $now->diffInMinutes($scheduledEnd);

        // Đảm bảo delay không âm và không quá lớn
        $delayMinutes = max(1, min($delayMinutes, 480)); // Tối đa 8 tiếng

        dispatch(new EndClassroomJob($id))->delay(now()->addMinutes($delayMinutes));

        return response()->json([
            'success' => true,
            'message' => 'Lớp học sẽ được kết thúc sau ' . $delayMinutes . ' phút nếu không có ai tham gia lại',
            'endClassroomJobDelay' => $delayMinutes,
            'scheduled_end_time' => $scheduledEnd->format('Y-m-d H:i:s')
        ]);
    }

    public function retry($id)
    {
        $classroom = $this->repo->find((int)$id);

        if ($classroom->status !== ClassRoomAction::Error->value) {
            return response()->json([
                'success' => false,
                'message' => 'Chỉ có thể thử lại với lớp học có trạng thái lỗi'
            ], 422);
        }

        $updated = $this->repo->update((int)$id, [
            'status' => ClassRoomAction::Scheduled->value,
            'error_message' => null,
        ]);

        return (new ClassRoomResource($updated))
            ->additional([
                'success' => true,
                'message' => 'Thử lại tạo lớp học thành công'
            ]);
    }

    public function pusherWebhook(Request $request)
    {
        $body = $request->getContent();
        $signature = $request->header('X-Pusher-Signature');

        // Xác thực signature
        $computedSignature = hash_hmac('sha256', $body, config('broadcasting.connections.pusher.secret'));
        if (!hash_equals($computedSignature, $signature)) {
            Log::warning('Invalid Pusher webhook signature');
            return response()->json(['error' => 'Invalid signature'], 401);
        }

        $payload = json_decode($body, true);
        if (!$payload || !isset($payload['events'])) {
            Log::warning('Invalid webhook payload');
            return response()->json(['error' => 'Invalid payload'], 400);
        }

        foreach ($payload['events'] as $event) {
            $channel = $event['channel'];
            $eventName = $event['name'];

            $classroomId = $this->extractClassroomIdFromChannel($channel);
            if (!$classroomId) {
                continue;
            }

            $classroom = $this->repo->find($classroomId);
            if (!$classroom) {
                continue;
            }

            $redisKey = "room:{$classroomId}:participants_count";
            $newCount = 0;

            // Cập nhật participants_count trong Redis
            if ($eventName === 'member_added') {
                $newCount = Redis::incr($redisKey);

                // Nếu đủ người thì start lớp học
                if ($newCount >= self::MIN_USER && $classroom->status !== ClassRoomAction::Started->value) {
                    Log::info("Auto-starting classroom {$classroomId}, participants: {$newCount}");
                    $this->start($classroomId); // Gọi lại hàm start
                }

            } elseif ($eventName === 'member_removed') {
                $newCount = Redis::decr($redisKey);
                if ($newCount < 0) {
                    Redis::set($redisKey, 0);
                    $newCount = 0;
                }

                // // Nếu hết người thì end lớp học
                // if ($newCount === 0 && $classroom->status === ClassRoomAction::Started->value) {
                //     Log::info("Auto-ending classroom {$classroomId}, participants: 0");
                //     $this->end($classroomId); // Gọi đến hàm end để cập nhật status
                // }
            }

            Log::info("Updated participants_count in Redis for room {$classroomId}: {$newCount}");

            // Broadcast lại cho FE biết số lượng thay đổi
            broadcast(new ParticipantUpdated($classroom, $newCount));
        }

        return response()->json(['status' => 'ok'], 200);
    }

    protected function extractClassroomIdFromChannel($channel)
    {
        if (preg_match('/presence-classroom\.(\d+)/', $channel, $matches)) {
            return $matches[1]; // Trả về classroom_id (ví dụ: 38)
        }
        return null;
    }
}
