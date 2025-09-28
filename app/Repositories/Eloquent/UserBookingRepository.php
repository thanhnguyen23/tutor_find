<?php

namespace App\Repositories\Eloquent;

use App\Enums\PaymentAction;
use App\Enums\UserBookingAction;
use App\Jobs\ExpireUserBookingJob;
use App\Models\UserBooking;
use App\Repositories\Contracts\UserBookingRepositoryInterface;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class UserBookingRepository implements UserBookingRepositoryInterface
{
    // Triển khai các hàm của Interface

    protected $model;

    public function __construct(UserBooking $model)
    {
        $this->model = $model;
    }

    public function create(array $data)
    {
        return $this->model->create($data);
    }

    public function updateStatus($id, $status) {
        return $this->model->where('id', $id)->update([
            'status' => $status
        ]);
    }

    public function getAll($uid, $perPage, $status, $code)
    {
        return $this->model
            ->ofUser($uid)
            ->when($status, fn($q) => $q->where('status', $status instanceof UserBookingAction ? $status->value : $status))
            ->when($code, fn($q) => $q->where('request_code', $code))
            ->paid()
            ->withRelations()
            ->get();
    }

    public function getAllPagination($uid, $perPage, $status, $code)
    {
        return $this->model
            ->ofUser($uid)
            ->when($status, fn($q) => $q->where('status', $status instanceof UserBookingAction ? $status->value : $status))
            ->when($code, fn($q) => $q->where('request_code', $code))
            ->paid()
            ->withRelations()
            ->paginate($perPage);
    }

    public function getAvailableForClassroom($uid, $code)
    {
        return $this->model
            ->ofUser($uid)
            ->where('status', UserBookingAction::Confirmed->value)
            ->where('end_time', '>', now())
            ->when($code, fn($q) => $q->where('request_code', $code))
            // ->paid()
            ->doesntHave('classRoom')
            ->withRelations()
            ->get();
    }

    public function find($id, $uid)
    {
        return $this->model
            ->ofUser($uid)
            ->where('id', $id)
            ->paid()
            ->withRelations()
            ->first();
    }

    public function getUpcomingLessons($uid, $perPage, $today)
    {
        return $this->model
            ->ofUser($uid)
            ->whereIn('status', [UserBookingAction::Confirmed->value, UserBookingAction::Pending->value])
            ->where('date', '>=', $today)
            ->paid()
            ->withRelations()
            ->orderBy('date')
            ->orderBy('start_time_id')
            ->paginate($perPage);
    }

    public function updateExpireUserBooking() {
        $this->model
        ->whereIn('status', [
            UserBookingAction::Pending->value,
            UserBookingAction::AwaitingClass->value,
            UserBookingAction::Confirmed->value,
        ])
        ->whereNotNull('end_time')
        ->where('end_time', '<', Carbon::now()->subMinutes(5))
        // ->where('end_time', '>=', $timeLimit)
        ->each(function ($booking) {
            ExpireUserBookingJob::dispatch($booking);
        });
    }

    /**
     * Lấy booking với time slots để tạo classroom
     */
    public function findForClassroom($bookingId)
    {
        return $this->model
            ->whereHas('payment', function ($query) {
                $query->where('status', PaymentAction::Success);
            })
            ->with(['timeSlotStart', 'timeSlotEnd'])
            ->findOrFail($bookingId);
    }

    /**
     * Kiểm tra booking đã có classroom chưa
     */
    public function hasClassroom($bookingId)
    {
        return $this->model
            ->whereHas('payment', function ($query) {
                $query->where('status', PaymentAction::Success);
            })
            ->where('id', $bookingId)
            ->whereHas('classRoom')
            ->exists();
    }

    /**
     * Kiểm tra user đã sử dụng free trial chưa
     */
    public function hasUsedFreeTrial($uid)
    {
        return $this->model
            ->where('uid', $uid)
            ->where('is_free', 1)
            ->where('type', 'trial')
            ->where('hourly_rate', 0)
            ->exists();
    }
}
