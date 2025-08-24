<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserBookingResource;
use App\Models\NotificationType;
use App\Models\UserBooking;
use App\Models\UserBookingLog;
use App\Repositories\Contracts\UserBookingRepositoryInterface;
use App\Repositories\Contracts\NotificationLogRepositoryInterface;
use App\Repositories\Contracts\UserRepositoryInterface;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Enums\UserBookingAction;

class UserBookingController extends Controller
{
    const PER_PAGE = 3;
    protected $userBookingRepository;
    protected $notificationLogRepository;
    protected $userRepository;

    public function __construct(
        UserBookingRepositoryInterface $userBookingRepository,
        NotificationLogRepositoryInterface $notificationLogRepository,
        UserRepositoryInterface $userRepository
    )
    {
        $this->userBookingRepository = $userBookingRepository;
        $this->notificationLogRepository = $notificationLogRepository;
        $this->userRepository = $userRepository;
    }

    public function getAll(Request $request)
    {
        $uid = auth()->user()->uid;
        $per_page = $request->per_page ?? self::PER_PAGE;

        $allData = $this->userBookingRepository
        ->getAllPagination(
            $uid,
            $per_page,
            $request->status,
            $request->code
        );

        return UserBookingResource::collection($allData)
        ->additional([
            'success' => true,
            'list_status' => UserBooking::$LIST_STATUS,
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'uid' => 'required|string',
            'subject_id' => 'required|integer',
            'education_level_id' => 'required|integer',
            'date' => 'required|date',
            'start_time_id' => 'required|integer',
            'end_time_id' => 'required|integer',
            'note' => 'nullable|string',
            'hourly_rate' => 'required|numeric',
            'duration' => 'required|numeric',
            'package_id' => 'nullable|integer',
            'num_sessions' => 'nullable|integer',
            'total_price' => 'required|numeric',
            'is_package' => 'nullable|boolean',
            'study_location_id' => 'nullable|integer',
        ]);

        // Không cho phép booking chính mình
        if ($data['uid'] === auth()->user()->uid) {
            return response()->json([
                'success' => false,
                'message' => 'Bạn không thể đặt lịch với chính mình!'
            ], 422);
        }

        DB::beginTransaction();

        try {
            $date = Carbon::parse($data['date'])->format('Y-m-d');

            // Lấy thông tin user để tạo location
            $user = $this->userRepository->findByUid($data['uid']);
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không tìm thấy thông tin gia sư',
                ], 404);
            }

            $location = $this->buildLocationFromUser($user);

            $booking = $this->userBookingRepository->create([
                'uid' => auth()->user()->uid,
                'tutor_uid' => $data['uid'],
                'subject_id' => $data['subject_id'],
                'education_level_id' => $data['education_level_id'],
                'date' => $date,
                'start_time_id' => $data['start_time_id'],
                'end_time_id' => $data['end_time_id'],
                'location' => $location,
                'note' => $data['note'],
                'hourly_rate' => $data['hourly_rate'],
                'duration' => $data['duration'],
                'package_id' => $data['package_id'],
                'num_sessions' => $data['num_sessions'],
                'total_price' => $data['total_price'],
                'is_package' => $data['is_package'] ? 1 : 0,
                'study_location_id' => $data['study_location_id'],
            ]);

            // Gửi thông báo cho tutor
            $studentName = auth()->user()->name ?? 'Học sinh';
            $description = "Bạn có lịch dạy mới từ {$studentName} vào ngày {$date}";
            $type_schedule = NotificationType::$TYPE_SCHEDULE;

            $this->notificationLogRepository->createSchedule([
                'uid' => $data['uid'], // tutor_uid
                'user_uid' => auth()->user()->uid,
                'name' => 'Lịch dạy mới',
                'description' => $description,
                'notification_type' => $type_schedule
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Đặt lịch thành công',
                'data' => $booking
            ], 201);
        } catch (\Throwable $e) {
            DB::rollBack();

            // Log lỗi để debug nếu cần
            Log::error('Booking failed: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Đặt lịch thất bại. Vui lòng thử lại.',
            ], 500);
        }
    }

    /**
     * Tạo location từ thông tin user
     */
    private function buildLocationFromUser($user)
    {
        // Load relationships cần thiết cho địa chỉ
        $user->load(['provinces', 'districts', 'wards']);

        $locationParts = [];

        // Thêm địa chỉ chi tiết nếu có
        if (!empty($user->address)) {
            $locationParts[] = $user->address;
        }

        // Thêm ward nếu có
        if ($user->wards && !empty($user->wards->name)) {
            $locationParts[] = $user->wards->name;
        }

        // Thêm district nếu có
        if ($user->districts && !empty($user->districts->name)) {
            $locationParts[] = $user->districts->name;
        }

        // Thêm province nếu có
        if ($user->provinces && !empty($user->provinces->name)) {
            $locationParts[] = $user->provinces->name;
        }

        return implode(', ', $locationParts);
    }


    public function show($id)
    {
        $uid = auth()->user()->uid;
        $booking = $this->userBookingRepository->find($id, $uid);
        if (!$booking) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy booking',
            ], 404);
        }
        return response()->json([
            'success' => true,
            'data' => new UserBookingResource($booking)
        ]);
    }

    public function changeStatus(Request $request)
    {
        $user = auth()->user();
        $id = $request->id;
        $status = $request->status;
        $note = $request->note; // note thay cho note_cancelled
        $type_schedule = NotificationType::$TYPE_SCHEDULE;

        // Validate status bằng enum
        try {
            $statusEnum = UserBookingAction::from($status);
        } catch (\ValueError $e) {
            return response()->json([
                'success' => false,
                'message' => 'Trạng thái không hợp lệ!'
            ], 422);
        }

        $booking = $this->userBookingRepository->find($id, $user->uid);
        if (!$booking || $booking->tutor_uid !== $user->uid) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy booking hoặc bạn không phải là gia sư của booking này!'
            ], 404);
        }

        // Chỉ cho phép hủy khi trạng thái là pending
        if ($statusEnum === UserBookingAction::Cancelled && $booking->status === UserBookingAction::Pending->value) {
            $booking->status = $statusEnum->value;
            $booking->save();
            // Lưu log
            UserBookingLog::create([
                'uid' => $user->uid,
                'user_booking_id' => $booking->id,
                'status' => $statusEnum,
                'note' => $note
            ]);
        } else if ($statusEnum !== UserBookingAction::Cancelled) {
            $booking->status = $statusEnum->value;
            $booking->save();
            // Lưu log
            UserBookingLog::create([
                'uid' => $user->uid,
                'user_booking_id' => $booking->id,
                'status' => $statusEnum,
                'note' => $note
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Chỉ có thể hủy khi trạng thái là chờ xác nhận!'
            ], 422);
        }

        // Gửi notification cho user khi hủy, xác nhận, hoàn thành booking
        $studentUid = $booking->uid;
        if ($statusEnum === UserBookingAction::Cancelled) {
            $this->notificationLogRepository->create([
                'uid' => $studentUid,
                'name' => 'Lịch học bị hủy',
                'description' => 'Lịch học của bạn đã bị hủy.' . ($note ? (' Lý do: ' . $note) : ''),
                'notification_type' => $type_schedule,
            ]);
        } elseif ($statusEnum === UserBookingAction::Confirmed) {
            $this->notificationLogRepository->create([
                'uid' => $studentUid,
                'name' => 'Lịch học đã được xác nhận',
                'description' => 'Lịch học của bạn đã được gia sư xác nhận.',
                'notification_type' => $type_schedule,
            ]);
        } elseif ($statusEnum === UserBookingAction::Completed) {
            $this->notificationLogRepository->create([
                'uid' => $studentUid,
                'name' => 'Lịch học đã hoàn thành',
                'description' => 'Lịch học của bạn đã hoàn thành. Cảm ơn bạn đã sử dụng dịch vụ.',
                'notification_type' => $type_schedule,
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Cập nhật trạng thái thành công!',
            'data' => new UserBookingResource($booking)
        ]);
    }

    /**
     * Lấy lịch học sắp tới
     * Tiêu chí: status == confirmed hoặc pending và date chưa hết hạn
     */
    public function getUpcomingLessons(Request $request)
    {
        $uid = auth()->user()->uid;
        $per_page = $request->per_page ?? self::PER_PAGE;
        $today = now()->format('Y-m-d');

        $upcomingLessons = $this->userBookingRepository
            ->getUpcomingLessons($uid, $per_page, $today);

        return UserBookingResource::collection($upcomingLessons)
            ->additional([
                'success' => true,
                'message' => 'Lấy lịch học sắp tới thành công',
            ]);
    }
}
