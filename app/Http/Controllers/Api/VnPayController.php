<?php

namespace App\Http\Controllers\Api;

use App\Enums\PaymentAction;
use App\Enums\UserBookingAction;
use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Repositories\Contracts\NotificationLogRepositoryInterface;
use App\Repositories\Contracts\PaymentRepositoryInterface;
use App\Repositories\Contracts\UserBookingRepositoryInterface;
use App\Repositories\Contracts\UserRepositoryInterface;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class VnPayController extends Controller
{
    protected $userBookingRepository;
    protected $notificationLogRepository;
    protected $userRepository;
    protected $paymentRepository;

    public function __construct(
        UserBookingRepositoryInterface $userBookingRepository,
        NotificationLogRepositoryInterface $notificationLogRepository,
        UserRepositoryInterface $userRepository,
        PaymentRepositoryInterface $paymentRepository,
    ) {
        $this->userBookingRepository = $userBookingRepository;
        $this->notificationLogRepository = $notificationLogRepository;
        $this->userRepository = $userRepository;
        $this->paymentRepository = $paymentRepository;
    }

    /**
     * Create a VNPAY payment URL
     *
     * Expected JSON payload:
     * - amount: integer VND amount (e.g., 150000)
     * - orderInfo: string short description
     * - orderId: string unique order reference (optional; generated if missing)
     * - bankCode: string optional bank code
     * - locale: vi|en (optional)
     */

    public function createPayment(Request $request)
    {
        DB::beginTransaction();
        try {
            // Validation
            $validated = $request->validate([
                'amount' => 'required|integer|min:1',
                'orderInfo' => 'required|string|max:255',
                'orderId' => 'nullable|string|max:64',
                'bankCode' => 'nullable|string|max:32',
                'locale' => 'nullable|in:vn,vi,en',

                // booking fields
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
                'payment_method' => 'required|integer',
                'payment_method_code' => 'required|string',
            ]);

            // Lấy cấu hình từ config
            $vnpTmnCode = config('vnpay.vnp_TmnCode');
            $vnpHashSecret = config('vnpay.vnp_HashSecret');
            $vnpUrl = config('vnpay.vnp_Url');
            $vnpReturnUrl = config('vnpay.vnp_Returnurl');
            $expireMinutes = (int) config('vnpay.vnp_ExpireDate', 10); // Mặc định 10 phút nếu không cấu hình

            // Kiểm tra cấu hình
            if (empty($vnpTmnCode) || empty($vnpHashSecret) || empty($vnpUrl) || empty($vnpReturnUrl)) {
                return response()->json(['message' => 'VNPAY is not configured'], 500);
            }

            // Chuẩn bị dữ liệu
            $amount = (int) $validated['amount'];
            $orderInfo = $validated['orderInfo'];
            $bankCode = $validated['bankCode'] ?? null;
            $locale = $validated['locale'] ?? 'vn';

            // Lấy IP từ header X-Forwarded-For hoặc fallback
            $ipAddress = $request->getClientIp();

            // Tính thời gian
            $createDate = Carbon::now()->timezone('Asia/Ho_Chi_Minh')->format('YmdHis');
            $expireDate = Carbon::now()->timezone('Asia/Ho_Chi_Minh')->addMinutes($expireMinutes)->format('YmdHis');

            // 1) KHÔNG tạo booking trước. Tạo mã tham chiếu giao dịch và lưu Payment pending cùng payload
            try {
                // Sinh mã tham chiếu giao dịch (txnRef) dùng cho VNPAY và tra soát khi return
                $txnRef = (string) (Carbon::now()->timestamp) . (string) auth()->id();

                // Lưu payment pending và đính kèm payload để tạo booking khi return
                $this->paymentRepository->create([
                    'booking_id' => null,
                    'payment_method_id' => $validated['payment_method'],
                    'user_uid' => auth()->user()->uid,
                    'transaction_id' => null,
                    'reference_code' => $txnRef,
                    'amount' => $amount,
                    'fee' => 0,
                    'refunded_amount' => 0,
                    'currency' => 'VND',
                    'gateway' => $validated['payment_method_code'],
                    'status' =>  PaymentAction::Pending->value,
                    'paid_at' => null,
                    'expired_at' => Carbon::parse($expireDate, 'Asia/Ho_Chi_Minh'),
                    'note' => $orderInfo,
                    'raw' => json_encode([
                        'payload' => $validated,
                        'user_uid' => auth()->user()->uid,
                    ]),
                ]);
            } catch (\Throwable $e) {
                Log::error('Create VNPAY payment failed: ' . $e->getMessage(), [
                    'trace' => $e->getTraceAsString()
                ]);
                return response()->json([
                    'success' => false,
                    'message' => 'Không thể khởi tạo thanh toán. Vui lòng thử lại.'
                ], 500);
            }

            // Dữ liệu gửi sang VNPAY
            $inputData = [
                'vnp_Version' => '2.1.0',
                'vnp_TmnCode' => $vnpTmnCode,
                'vnp_Amount' => $amount * 100, // amount in VND x 100
                'vnp_Command' => 'pay',
                'vnp_CreateDate' => $createDate,
                'vnp_ExpireDate' => $expireDate,
                'vnp_CurrCode' => 'VND',
                'vnp_IpAddr' => $ipAddress,
                'vnp_Locale' => $locale,
                'vnp_OrderInfo' => $orderInfo,
                'vnp_OrderType' => 'billpayment',
                'vnp_ReturnUrl' => $vnpReturnUrl,
                'vnp_TxnRef' => $txnRef,
            ];

            Log::info('VNPAY input data: ' . json_encode($inputData));

            // Thêm vnp_BankCode nếu có
            if (!empty($bankCode)) {
                $inputData['vnp_BankCode'] = $bankCode;
            }

            // Sắp xếp và tạo query
            ksort($inputData);
            $query = http_build_query($inputData, '', '&', PHP_QUERY_RFC3986);

            // Tính Secure Hash
            $secureHash = hash_hmac('sha512', $query, $vnpHashSecret);
            $paymentUrl = $vnpUrl . '?' . $query . '&vnp_SecureHash=' . $secureHash;

            Log::info('VNPAY payment url: ' . $paymentUrl);

            DB::commit();

            // Trả về JSON với URL
            return response()->json([
                'paymentUrl' => $paymentUrl,
                'txnRef' => $txnRef,
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Create VNPAY payment failed: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Không thể khởi tạo thanh toán. Vui lòng thử lại.'
            ], 500);
        }
    }

    /**
     * Handle VNPAY return URL (user browser redirect)
     */
    public function return(Request $request)
    {
        $params = $request->query();
        $validated = $this->verifyVnpaySignature($params);

        if (!$validated['is_valid']) {
            return response()->json(['message' => 'Invalid signature'], 400);
        }

        // vnp_ResponseCode == '00' means success
        $isSuccess = ($params['vnp_ResponseCode'] ?? null) === '00';

        // Tạo booking khi thanh toán thành công và cập nhật Payment theo reference_code
        try {
            $referenceCode = $params['vnp_TxnRef'] ?? null;
            if ($referenceCode) {
                $payment = $this->paymentRepository->whereReferenceCode($referenceCode);
                if ($payment) {
                    if ($isSuccess) {
                        // Nếu chưa có booking thì tạo mới từ payload đã lưu
                        if (empty($payment->booking_id)) {
                            $raw = is_string($payment->raw) ? json_decode($payment->raw, true) : (array) $payment->raw;
                            $payload = $raw['payload'] ?? [];

                            // Không cho phép booking chính mình
                            if (($payload['uid'] ?? null) === ($payment->user_uid ?? null)) {
                                return response()->json([
                                    'success' => false,
                                    'message' => 'Bạn không thể đặt lịch với chính mình!'
                                ], 422);
                            }

                            // Lấy thông tin user để tạo location
                            $tutor = $this->userRepository->findByUid($payload['uid'] ?? null);
                            if (!$tutor) {
                                return response()->json([
                                    'success' => false,
                                    'message' => 'Không tìm thấy thông tin gia sư',
                                ], 404);
                            }

                            $location = $this->buildLocationFromUser($tutor);
                            $date = Carbon::parse($payload['date'])->format('Y-m-d');

                            $booking = $this->userBookingRepository->create([
                                'uid' => $payment->user_uid,
                                'tutor_uid' => $payload['uid'],
                                'subject_id' => $payload['subject_id'],
                                'education_level_id' => $payload['education_level_id'],
                                'date' => $date,
                                'start_time_id' => $payload['start_time_id'],
                                'end_time_id' => $payload['end_time_id'],
                                'location' => $location,
                                'note' => $payload['note'] ?? null,
                                'hourly_rate' => $payload['hourly_rate'],
                                'duration' => $payload['duration'],
                                'package_id' => $payload['package_id'] ?? null,
                                'num_sessions' => $payload['num_sessions'] ?? null,
                                'total_price' => $payload['total_price'],
                                'is_package' => !empty($payload['is_package']) ? 1 : 0,
                                'study_location_id' => $payload['study_location_id'] ?? null,
                                'status' => UserBookingAction::Pending->value,
                            ]);

                            // Gán booking vào payment
                            $payment->booking_id = $booking->id;
                        }

                        // Cập nhật trạng thái payment thành thành công
                        $payment->status = PaymentAction::Success->value;
                        $payment->transaction_id = $params['vnp_TransactionNo'] ?? null;
                        $payment->paid_at = now();
                    } else {
                        $payment->status = PaymentAction::Failed->value;
                    }

                    // Lưu thêm dữ liệu trả về của VNPAY
                    $mergedRaw = [
                        'gateway_return' => $params,
                    ];
                    if (!empty($payment->raw)) {
                        $existingRaw = is_string($payment->raw) ? json_decode($payment->raw, true) : (array) $payment->raw;
                        $mergedRaw = array_merge($existingRaw ?: [], $mergedRaw);
                    }
                    $payment->raw = json_encode($mergedRaw);
                    $payment->save();

                    // Điều hướng dựa theo booking_id nếu có
                    $redirectId = $payment->booking_id ?: $referenceCode;
                    return redirect()->route('booking-success', ['id' => $redirectId]);
                }
            }
        } catch (\Throwable $e) {
            Log::error('Update VNPAY payment/booking on return failed: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
        }

        return redirect()->route('booking-success', ['id' => $params['vnp_TxnRef'] ?? null]);
    }

    /**
     * Handle VNPAY IPN (server-to-server notify)
     * Must return JSON with RspCode and Message
     */
    public function ipn(Request $request)
    {
        $params = $request->all();

        $validated = $this->verifyVnpaySignature($params);
        if (!$validated['is_valid']) {
            return response()->json(['RspCode' => '97', 'Message' => 'Invalid signature']);
        }

        // Basic validation
        $txnRef = $params['vnp_TxnRef'] ?? null;
        $amount = isset($params['vnp_Amount']) ? (int) $params['vnp_Amount'] : null;
        $responseCode = $params['vnp_ResponseCode'] ?? null;

        if (empty($txnRef) || empty($amount)) {
            return response()->json(['RspCode' => '99', 'Message' => 'Invalid request']);
        }

        // TODO: Lookup your order by $txnRef and compare amount (amount/100)
        // For now, just log it and respond as processed
        Log::info('VNPAY IPN received', $params);

        // ResponseCode 00 means successful payment
        if ($responseCode === '00') {
            // Update order status to paid here
            return response()->json(['RspCode' => '00', 'Message' => 'Confirm Success']);
        }

        // Payment failed or pending
        return response()->json(['RspCode' => '00', 'Message' => 'Confirm Success']);
    }

    /**
     * Verify VNPAY signature for given params
     */
    private function verifyVnpaySignature(array $params): array
    {
        $vnpHashSecret = config('vnpay.vnp_HashSecret');
        $receivedSecureHash = $params['vnp_SecureHash'] ?? '';

        // Remove hash params before computing
        unset($params['vnp_SecureHash']);
        unset($params['vnp_SecureHashType']);

        // Only include parameters starting with vnp_
        $filtered = [];
        foreach ($params as $key => $value) {
            if (strpos($key, 'vnp_') === 0) {
                $filtered[$key] = $value;
            }
        }

        ksort($filtered);
        $data = http_build_query($filtered, '', '&', PHP_QUERY_RFC3986);
        $calculatedHash = hash_hmac('sha512', $data, $vnpHashSecret);

        return [
            'is_valid' => strtolower($calculatedHash) === strtolower((string) $receivedSecureHash),
            'calculated' => $calculatedHash,
        ];
    }

    /**
     * Tạo chuỗi địa chỉ location từ thông tin user (tutor)
     */
    private function buildLocationFromUser($user)
    {
        $user->load(['provinces', 'districts', 'wards']);

        $locationParts = [];
        if (!empty($user->address)) {
            $locationParts[] = $user->address;
        }
        if ($user->wards && !empty($user->wards->name)) {
            $locationParts[] = $user->wards->name;
        }
        if ($user->districts && !empty($user->districts->name)) {
            $locationParts[] = $user->districts->name;
        }
        if ($user->provinces && !empty($user->provinces->name)) {
            $locationParts[] = $user->provinces->name;
        }

        return implode(', ', $locationParts);
    }
}


