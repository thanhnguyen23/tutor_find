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
use App\Enums\PaymentAction;
use App\Models\Payment;
use App\Models\PaymentMethod;
use App\Models\UserBookingComplaint;
use App\Repositories\Contracts\PaymentRepositoryInterface;
use App\Repositories\Contracts\UserSubjectLevelsRepositoryInterface;
use App\Jobs\ExpirePaymentJob;
use App\Events\PaymentStatusUpdated;
use App\Services\ConfigService;
use Illuminate\Support\Facades\Http;

class UserBookingController extends Controller
{
    const PER_PAGE = 8;
    protected $userBookingRepository;
    protected $notificationLogRepository;
    protected $userRepository;
    protected $paymentRepository;
    protected $userSubjectLevelsRepository;
    protected $configService;

    public function __construct(
        UserBookingRepositoryInterface $userBookingRepository,
        NotificationLogRepositoryInterface $notificationLogRepository,
        UserRepositoryInterface $userRepository,
        PaymentRepositoryInterface $paymentRepository,
        UserSubjectLevelsRepositoryInterface $userSubjectLevelsRepository,
        configService $configService
    )
    {
        $this->userBookingRepository = $userBookingRepository;
        $this->notificationLogRepository = $notificationLogRepository;
        $this->userRepository = $userRepository;
        $this->paymentRepository = $paymentRepository;
        $this->userSubjectLevelsRepository = $userSubjectLevelsRepository;
        $this->configService = $configService;
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
            'list_status_complaint' => UserBookingComplaint::$LIST_STATUS,
        ]);
    }

    public function getAvailableForClassroom(Request $request) {
        try {
            $uid = auth()->user()->uid;

            $allData = $this->userBookingRepository
            ->getAvailableForClassroom(
                $uid,
                $request->code
            );

            return UserBookingResource::collection($allData)
            ->additional([
                'success' => true,
                'list_status' => UserBooking::$LIST_STATUS,
                'list_status_complaint' => UserBookingComplaint::$LIST_STATUS,
            ]);
        } catch (\Throwable $e) {
            // Log lỗi để debug nếu cần
            Log::error('getAvailableForClassroom failed: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Lấy danh sách booking thấy bại',
            ], 500);
        }
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
            // payment
            'payment_method' => 'required|integer',
            'payment_method_code' => 'required|string',
            'bankCode' => 'nullable|string|max:32',
            'locale' => 'nullable|in:vn,vi,en',
        ]);

        // Nếu phương thức thanh toán là VNPAY, điều hướng sang quy trình tạo Payment
        if ($data['payment_method_code'] != PaymentMethod::$LIST_CODE['cash']) {
            return response()->json([
                'success' => false,
                'message' => 'Phương thức thanh toán không hợp lệ!'
            ], 422);
        }

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

            $timeSlots = collect($this->configService->getByKey('timeSlots'))->keyBy('id');

            $startTime = Carbon::parse("{$date} {$timeSlots[$data['start_time_id']]->time}");
            $endTime   = Carbon::parse("{$date} {$timeSlots[$data['end_time_id']]->time}");

            $booking = $this->userBookingRepository->create([
                'uid' => auth()->user()->uid,
                'tutor_uid' => $data['uid'],
                'subject_id' => $data['subject_id'],
                'education_level_id' => $data['education_level_id'],
                'date' => $date,
                'start_time'=> $startTime,
                'end_time'  => $endTime,
                'start_time_id' => $data['start_time_id'],
                'end_time_id' => $data['end_time_id'],
                'location' => $location,
                'note' => $data['note'] ?? null,
                'hourly_rate' => $data['hourly_rate'],
                'duration' => $data['duration'],
                'package_id' => $data['package_id'] ?? null,
                'num_sessions' => $data['num_sessions'] ?? null,
                'total_price' => $data['total_price'],
                'is_package' => $data['is_package'] ? 1 : 0,
                'study_location_id' => $data['study_location_id'] ?? null,
            ]);

            // Tạo payment cho phương thức thanh toán tiền mặt (cash)
            $this->paymentRepository->create([
                'booking_id' => $booking->id,
                'payment_method_id' => $data['payment_method'],
                'user_uid' => auth()->user()->uid,
                'transaction_id' => null,
                'reference_code' => (string) $booking->id,
                'amount' => (int) $data['total_price'],
                'fee' => 0,
                'refunded_amount' => 0,
                'currency' => 'VND',
                'gateway' => $data['payment_method_code'],
                'status' => PaymentAction::Pending->value,
                'paid_at' => null,
                'expired_at' => null,
                'note' => 'Thanh toán tiền mặt khi gặp gia sư',
                'raw' => null,
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

    public function storeSepay(Request $request)
    {
        $data = $request->validate([
            'uid' => 'required|string',
            'subject_id' => 'required|integer',
            'education_level_id' => 'required|integer',
            'date' => 'required|date',
            'time_slot_id' => 'required|integer',
            'tutor_session_id' => 'required|integer',
            'note' => 'nullable|string',
            'package_id' => 'nullable|integer',
            'num_sessions' => 'nullable|integer',
            'is_package' => 'nullable|boolean',
            'study_location_id' => 'nullable|integer',
            // payment
            'payment_method' => 'required|integer',
            'payment_method_code' => 'required|string', // should be 'sepay'
            'bankCode' => 'nullable|string|max:32',
            'locale' => 'nullable|in:vn,vi,en',
        ]);

        if ($data['payment_method_code'] !== 'sepay') {
            return response()->json([
                'success' => false,
                'message' => 'Phương thức thanh toán không hợp lệ!'
            ], 422);
        }

        if ($data['uid'] === auth()->user()->uid) {
            return response()->json([
                'success' => false,
                'message' => 'Bạn không thể đặt lịch với chính mình!'
            ], 422);
        }

        DB::beginTransaction();
        try {
            $date = Carbon::parse($data['date'])->format('Y-m-d');

            $user = $this->userRepository->findByUid($data['uid']);
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không tìm thấy thông tin gia sư',
                ], 404);
            }

            $location = $this->buildLocationFromUser($user);

            $timeSlots = collect($this->configService->getByKey('timeSlots'))->keyBy('id');
            $sessionOptions = collect($this->configService->getByKey('tutorSessions'))->keyBy('id');

            $startTime = Carbon::parse("{$date} {$timeSlots[$data['time_slot_id']]->time}");
            $durationHours = (float) ($sessionOptions[$data['tutor_session_id']]->duration_hours ?? 0);
            $endTime   = (clone $startTime)->addMinutes((int) round($durationHours * 60));

            // Lấy giá từ UserSubjectLevels dựa trên tutor, subject và education_level
            $userSubjectLevel = $this->userSubjectLevelsRepository->getPriceByUserSubjectAndLevel(
                $user->id,
                $data['subject_id'],
                $data['education_level_id']
            );

            if (!$userSubjectLevel) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không tìm thấy thông tin giá của gia sư cho môn học này',
                ], 404);
            }

            $hourlyRate = $userSubjectLevel->price;
            $calculatedTotalPrice = $hourlyRate * $durationHours;

            $booking = $this->userBookingRepository->create([
                'uid'               => auth()->user()->uid,
                'tutor_uid'         => $data['uid'],
                'subject_id'        => $data['subject_id'],
                'education_level_id'=> $data['education_level_id'],
                'date'              => $date,
                'start_time'        => $startTime,
                'end_time'          => $endTime,
                'time_slot_id'     => $data['time_slot_id'],
                'tutor_session_id'  => $data['tutor_session_id'],
                'location'          => $location,
                'note'              => $data['note'] ?? null,
                'hourly_rate'       => $hourlyRate,
                'duration'          => $durationHours,
                'package_id'        => $data['package_id'] ?? null,
                'num_sessions'      => $data['num_sessions'] ?? null,
                'total_price'       => $calculatedTotalPrice,
                'is_package'        => $data['is_package'] ? 1 : 0,
                'study_location_id' => $data['study_location_id'] ?? null,
            ]);

            // Set pending payment with 10 minutes expiration
            $expiredAt = now()->addMinutes(3);
            $referenceCode = 'BOOKING' . $booking->id;

            $payment = $this->paymentRepository->create([
                'booking_id' => $booking->id,
                'payment_method_id' => $data['payment_method'],
                'user_uid' => auth()->user()->uid,
                'transaction_id' => null,
                'reference_code' => $referenceCode,
                'amount' => (int) $calculatedTotalPrice,
                'fee' => 0,
                'refunded_amount' => 0,
                'currency' => 'VND',
                'gateway' => $data['payment_method_code'],
                'status' => PaymentAction::Pending->value,
                'paid_at' => null,
                'expired_at' => $expiredAt,
                'note' => 'Thanh toán chuyển khoản ngân hàng',
                'raw' => null,
            ]);

            // Build sePay QR
            $accountNumber = '19919397979';
            $bank = $request->input('bankCode', 'MB');
            $amount = (int) $calculatedTotalPrice;
            $description = $referenceCode;
            $qrUrl = $this->generateSepayQr($accountNumber, $bank, $amount, $description);

            // Dispatch expiration job to mark non_payment after 10 minutes if still pending
            ExpirePaymentJob::dispatch($payment->id)->delay($expiredAt);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Tạo đơn thanh toán sePay thành công',
                'data' => [
                    'booking_id' => $booking->id,
                    'payment_id' => $payment->id,
                    'qr_url' => $qrUrl,
                    'reference_code' => $referenceCode,
                    'expired_at' => $expiredAt->toIso8601String(),
                    'remaining_seconds' => max(0, $expiredAt->diffInSeconds(now())),
                    'account_number' => $accountNumber,
                    'bank' => $bank,
                    'amount' => $amount,
                ]
            ], 201);
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('storeSepay failed: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Tạo thanh toán sePay thất bại. Vui lòng thử lại.',
            ], 500);
        }
    }

    public function storeTrial(Request $request)
    {
        $data = $request->validate([
            'uid' => 'required|string',
            'subject_id' => 'required|integer',
            'education_level_id' => 'required|integer',
            'date' => 'required|date',
            'time_slot_id' => 'required|integer',
            'tutor_session_id' => 'required|integer',
            'note' => 'nullable|string',
            'hourly_rate' => 'required|numeric',
            'duration' => 'required|numeric',
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

            // Lấy config time slots và tutor sessions
            $timeSlots = collect($this->configService->getByKey('timeSlots'))->keyBy('id');
            $sessionOptions = collect($this->configService->getByKey('tutorSessions'))->keyBy('id');

            $selectedSession = $sessionOptions->get($data['tutor_session_id']);
            if (!$selectedSession || !$selectedSession?->allow_free) {
                return response()->json([
                    'success' => false,
                    'message' => 'Loại buổi học không hợp lệ',
                ], 422);
            }

            // Lấy tutor
            $tutor = $this->userRepository->findByUid($data['uid']);
            if (!$tutor) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không tìm thấy thông tin gia sư',
                ], 404);
            }

            // Tutor có bật học thử không
            if ($tutor->is_free_study != 1) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gia sư này không bật chế độ học thử',
                ], 422);
            }

            $student = $this->userRepository->findByUid(auth()->user()->uid);

            // KIỂM TRA FREE STUDY QUOTA - chỉ cho phép tiếp tục nếu > 0
            if ($student->free_study <= 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bạn đã hết lượt học thử miễn phí',
                ], 422);
            }

            $startTime = Carbon::parse("{$date} {$timeSlots[$data['time_slot_id']]->time}");
            $durationHours = (float) ($selectedSession->duration_hours ?? $data['duration']);
            $calculatedEndTime = (clone $startTime)->addMinutes((int) round($durationHours * 60));

            // Build location
            $location = $this->buildLocationFromUser($tutor);

            // Tạo booking - luôn miễn phí vì đã kiểm tra free_study > 0
            $booking = $this->userBookingRepository->create([
                'uid'               => $student->uid,
                'tutor_uid'         => $data['uid'],
                'subject_id'        => $data['subject_id'],
                'education_level_id'=> $data['education_level_id'],
                'date'              => $date,
                'start_time'        => $startTime,
                'end_time'          => $calculatedEndTime,
                'time_slot_id'      => $data['time_slot_id'],
                'tutor_session_id'  => $data['tutor_session_id'],
                'location'          => $location,
                'note'              => $data['note'] ?? null,
                'hourly_rate'       => 0, // Luôn miễn phí
                'duration'          => $durationHours,
                'package_id'        => null,
                'num_sessions'      => null,
                'total_price'       => 0, // Luôn miễn phí
                'is_package'        => 0,
                'is_free'           => 1, // Luôn miễn phí
                'type'              => 'trial',
                'study_location_id' => $data['study_location_id'] ?? null,
                'status'            => UserBookingAction::Pending->value,
            ]);

            // Tạo payment - luôn success vì miễn phí
            $this->paymentRepository->create([
                'booking_id'        => $booking->id,
                'payment_method_id' => null,
                'user_uid'          => $student->uid,
                'transaction_id'    => 'TRIAL_FREE_' . $booking->id,
                'reference_code'    => 'TRIAL_FREE_' . $booking->id,
                'amount'            => 0,
                'fee'               => 0,
                'refunded_amount'   => 0,
                'currency'          => 'VND',
                'gateway'           => 'trial',
                'status'            => PaymentAction::Success->value,
                'paid_at'           => now(),
                'expired_at'        => null,
                'note'              => 'Buổi học thử miễn phí',
                'raw'               => null,
            ]);

            // Gửi thông báo
            $studentName = $student->name ?? 'Học sinh';
            $description = "Bạn có lịch dạy thử mới từ {$studentName} vào ngày {$date}";

            $this->notificationLogRepository->createSchedule([
                'uid'               => $data['uid'],
                'user_uid'          => $student->uid,
                'name'              => 'Lịch dạy thử mới',
                'description'       => $description,
                'notification_type' => NotificationType::$TYPE_SCHEDULE
            ]);

            // Trừ lượt free_study
            $student->free_study = $student->free_study - 1;
            $student->save();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Đặt lịch học thử miễn phí thành công',
                'data' => [
                    'booking'       => $booking,
                    'is_free_trial' => true,
                    'hourly_rate'   => 0,
                    'total_price'   => 0,
                    'free_study'    => $student->free_study
                ]
            ], 201);

        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Trial booking failed: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Đặt lịch học thử thất bại. Vui lòng thử lại.',
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
        if (!$booking) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy booking!'
            ], 404);
        }

        $isTutor = $user->uid === $booking->tutor_uid;

        // Mapping: Tutor cancels -> Rejected, Student cancels -> Cancelled
        if ($statusEnum === UserBookingAction::Cancelled || $statusEnum === UserBookingAction::Rejected) {
            if ($booking->status !== UserBookingAction::Pending->value) {
                return response()->json([
                    'success' => false,
                    'message' => 'Chỉ có thể từ chối/hủy khi trạng thái là chờ xác nhận!'
                ], 422);
            }

            $targetStatus = null;
            if ($isTutor) {
                $targetStatus = UserBookingAction::Rejected;
            } else {
                $targetStatus = UserBookingAction::Cancelled;
            }

            // Nếu client gửi rejected nhưng không phải tutor, hoặc gửi cancelled nhưng là tutor -> vẫn áp quy tắc trên
            $booking->status = $targetStatus->value;
            $booking->save();

            UserBookingLog::create([
                'uid' => $user->uid,
                'user_booking_id' => $booking->id,
                'status' => $targetStatus,
                'note' => $note
            ]);
        } else {
            // Other transitions (confirmed, completed ...)
            $booking->status = $statusEnum->value;
            $booking->save();
            UserBookingLog::create([
                'uid' => $user->uid,
                'user_booking_id' => $booking->id,
                'status' => $statusEnum,
                'note' => $note
            ]);
        }

        // Gửi notification cho user khi hủy, xác nhận, hoàn thành booking
        $studentUid = $booking->uid;
        if ($booking->status === UserBookingAction::Cancelled->value) {
            $this->notificationLogRepository->create([
                'uid' => $studentUid,
                'name' => 'Lịch học bị hủy',
                'description' => 'Lịch học của bạn đã bị hủy.' . ($note ? (' Lý do: ' . $note) : ''),
                'notification_type' => $type_schedule,
            ]);
        } elseif ($booking->status === UserBookingAction::Rejected->value) {
            $this->notificationLogRepository->create([
                'uid' => $studentUid,
                'name' => 'Lịch học bị từ chối',
                'description' => 'Gia sư đã từ chối yêu cầu đặt lịch của bạn.' . ($note ? (' Lý do: ' . $note) : ''),
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

    public function webhookSendPayment(Request $request)
    {
        $data = $request->all();
        Log::info('webhookSendPayment', $data);

        try {
            $description = $data['description'] ?? ($data['content'] ?? '');
            $transferAmount = (int) ($data['transferAmount'] ?? 0);
            $referenceCode = $data['referenceCode'] ?? null;
            $accountNumber = $data['accountNumber'] ?? null;
            $transactionDate = $data['transactionDate'] ?? null;
            $transferType = $data['transferType'] ?? null;
            $gateway = $data['gateway'] ?? null;
            $subAccount = $data['subAccount'] ?? null;
            $accumulated = $data['accumulated'] ?? null;
            $code = $data['code'] ?? null;
            $transaction_id = $data['id'] ?? null;

            // Extract our system reference like BOOKING-<id>
            $matchedRef = null;
            if (preg_match('/BOOKING\d+/', $description, $matches)) {
                $matchedRef = $matches[0];
            }

            if (!$matchedRef) {
                Log::warning('webhookSendPayment: reference not found in description');
                return response()->json(['success' => false], 400);
            }

            $payment = $this->paymentRepository->whereReferenceCode($matchedRef);
            if (!$payment) {
                Log::warning('webhookSendPayment: payment not found for reference ' . $matchedRef);
                return response()->json(['success' => false], 404);
            }

            // Update payment as success
            $payment->update([
                'transaction_id' => $transaction_id,
                'reference_code' => $referenceCode,
                'account_number' => $accountNumber,
                'transaction_date' => $transactionDate,
                'payment_code' => $code,
                'transfer_type' => $transferType,
                'transfer_amount' => $transferAmount,
                'accumulated' => $accumulated,
                'sub_account' => $subAccount,
                'gateway' => $gateway ?? $payment->gateway,
                'status' => PaymentAction::Success->value,
                'paid_at' => now(),
                'raw' => json_encode($data),
            ]);

            // Broadcast status to FE
            try {
                broadcast(new PaymentStatusUpdated($payment));
            } catch (\Throwable $e) {
                Log::error('Broadcast payment status failed: ' . $e->getMessage());
            }

            return response()->json(['success' => true]);
        } catch (\Throwable $e) {
            Log::error('webhookSendPayment error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json(['success' => false], 500);
        }
    }

    function generateSepayQr($account, $bank, $amount, $description)
    {
        $base = "https://qr.sepay.vn/img";
        $query = http_build_query([
            'acc'      => $account,
            'bank'     => $bank,
            'amount'   => '2000',
            'des'      => $description,
        ]);
        return "{$base}?{$query}";
    }
}
