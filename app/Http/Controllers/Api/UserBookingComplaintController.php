<?php

namespace App\Http\Controllers\Api;

use App\Enums\UserBookingAction;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Repositories\Contracts\UserBookingComplaintRepositoryInterface;
use App\Repositories\Contracts\UserBookingRepositoryInterface;
use App\Enums\UserBookingComplaintAction;
use App\Http\Resources\UserBookingComplaintResource;
use App\Models\UserBookingComplaint;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class UserBookingComplaintController extends Controller
{
    protected $complaintRepository;
    protected $bookingRepository;

    public function __construct(
        UserBookingComplaintRepositoryInterface $complaintRepository,
        UserBookingRepositoryInterface $bookingRepository
    ) {
        $this->complaintRepository = $complaintRepository;
        $this->bookingRepository = $bookingRepository;
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'booking_id' => 'required|integer',
            'complaint_type_id' => 'required|integer',
            'description' => 'required|string',
            'evidence' => 'nullable|array',
        ]);

        $uid = auth()->user()->uid;

        // Ensure the booking belongs to user or tutor
        $booking = $this->bookingRepository->find($data['booking_id'], $uid);
        if (!$booking) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy booking hoặc bạn không có quyền khiếu nại!'
            ], 404);
        }

        try {
            DB::beginTransaction();

            // chỉ cho phép gửi khiếu nại khi lịch đã hoàn thành hoặc hết hạn
            if (!in_array($booking->status, [UserBookingAction::Completed->value, UserBookingAction::Missed->value])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Chỉ cho phép gửi khiếu nại khi lịch đã hoàn thành hoặc bị lỡ',
                ], 422);
            }

            // Limit: one complaint per booking
            $existing = $this->complaintRepository->getByBookingId($data['booking_id']);
            if ($existing && $existing->count() > 0) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => 'Bạn đã gửi khiếu nại cho lịch này trước đó',
                    'data' => $existing->first(),
                ], 422);
            }

            $complaint = $this->complaintRepository->create([
                'booking_id' => $data['booking_id'],
                'complaint_type_id' => $data['complaint_type_id'],
                'uid' => $uid,
                'description' => $data['description'] ?? null,
                'status' => UserBookingComplaintAction::Pending->value,
                'evidence' => $data['evidence'] ?? [],
            ]);

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Gửi khiếu nại thành công',
                'data' => UserBookingComplaintResource::make($complaint),
            ], 201);

        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Create complaint failed: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return response()->json([
                'success' => false,
                'message' => 'Gửi khiếu nại thất bại. Vui lòng thử lại.'
            ], 500);
        }
    }

    public function showByBooking(int $bookingId)
    {
        $uid = auth()->user()->uid;
        $booking = $this->bookingRepository->find($bookingId, $uid);
        if (!$booking) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy booking hoặc bạn không có quyền truy cập!'
            ], 404);
        }

        $list = $this->complaintRepository->getByBookingId($bookingId);
        $complaint = $list->first();

        return response()->json([
            'success' => true,
            'data' => UserBookingComplaintResource::make($complaint),
            'list_status' => UserBookingComplaint::$LIST_STATUS,
        ]);
    }

    public function updateStatus(Request $request)
    {
        $data = $request->validate([
            'id' => 'required|integer',
            'status' => 'required|in:pending,resolved,rejected',
            'note' => 'nullable|string',
        ]);

        // Optional: Only admins or tutors related to the booking can resolve/reject
        try {
            $complaint = $this->complaintRepository->findById($data['id']);
            if (!$complaint) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không tìm thấy khiếu nại'
                ], 404);
            }

            // Basic permission: only tutor of the booking can change status for now
            $user = auth()->user();
            if ($complaint->booking && $complaint->booking->tutor_uid !== $user->uid) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bạn không có quyền cập nhật khiếu nại này'
                ], 403);
            }

            $updated = $this->complaintRepository->updateStatus($data['id'], $data['status'], $data['note'] ?? null);
            return response()->json([
                'success' => true,
                'message' => 'Cập nhật khiếu nại thành công',
                'data' => UserBookingComplaintResource::make($updated),
            ]);
        } catch (\Throwable $e) {
            Log::error('Update complaint failed: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return response()->json([
                'success' => false,
                'message' => 'Cập nhật khiếu nại thất bại. Vui lòng thử lại.'
            ], 500);
        }
    }
}
