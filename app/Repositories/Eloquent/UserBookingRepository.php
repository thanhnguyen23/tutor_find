<?php

namespace App\Repositories\Eloquent;

use App\Enums\UserBookingAction;
use App\Models\UserBooking;
use App\Repositories\Contracts\UserBookingRepositoryInterface;

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

    public function getAll($uid) {
        return $this->model
        ->where(function ($query) use ($uid) {
            $query->where('uid', $uid)
                  ->orWhere('tutor_uid', $uid);
        })
        ->with([
            'subject',
            'studyLocation',
            'educationLevel',
            'timeSlotStart',
            'timeSlotEnd',
            'package',
            'user',
            'tutor',
            'userBookingLogs',
            'userBookingLogs.user'
        ])
        ->get();
    }

    public function getAllPagination($uid, $perPage, $status, $code) {
        return $this->model
        ->where(function ($query) use ($uid) {
            $query->where('uid', $uid)
                  ->orWhere('tutor_uid', $uid);
        })
        ->when($status, function ($query, $status) {
            $query->where('status', $status instanceof UserBookingAction ? $status->value : $status);
        })
        ->when($code, function ($query, $code) {
            $query->where('request_code', $code);
        })
        ->with([
            'subject',
            'studyLocation',
            'educationLevel',
            'timeSlotStart',
            'timeSlotEnd',
            'package',
            'user',
            'tutor',
            'userBookingLogs',
            'userBookingLogs.user'
        ])
        ->paginate($perPage);
    }

    /**
     * Lấy booking theo id
     */
    public function find($id, $uid)
    {
        return $this->model
        ->where('id', $id)
        ->where('tutor_uid', $uid)
        ->with([
            'subject',
            'educationLevel',
            'studyLocation',
            'timeSlotStart',
            'timeSlotEnd',
            'package',
            'user',
            'tutor',
            'userBookingLogs',
            'userBookingLogs.user'
        ])
        ->first();
    }

    /**
     * Lấy lịch học sắp tới
     * Tiêu chí: status == confirmed hoặc pending và date >= today
     */
    public function getUpcomingLessons($uid, $perPage, $today)
    {
        return $this->model
        ->where('uid', $uid)
        ->whereIn('status', [UserBookingAction::Confirmed->value, UserBookingAction::Pending->value])
        // ->where('date', '>=', $today)
        ->with([
            'subject',
            'studyLocation',
            'educationLevel',
            'timeSlotStart',
            'timeSlotEnd',
            'package',
            'user',
            'tutor',
            'userBookingLogs',
            'userBookingLogs.user'
        ])
        ->orderBy('date', 'asc')
        ->orderBy('start_time_id', 'asc')
        ->paginate($perPage);
    }
}
