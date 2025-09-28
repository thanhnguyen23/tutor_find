<?php

namespace App\Repositories\Eloquent;

use App\Repositories\Contracts\NotificationLogRepositoryInterface;
use App\Repositories\Contracts\NotificationTypeRepositoryInterface;
use App\Models\NotificationLog;
use App\Models\NotificationType;

class NotificationLogRepository implements NotificationLogRepositoryInterface
{
    protected $model;
    protected $notificationTypeRepo;

    public function __construct(NotificationLog $model, NotificationTypeRepositoryInterface $notificationTypeRepo)
    {
        $this->model = $model;
        $this->notificationTypeRepo = $notificationTypeRepo;
    }

    public function getAllPagination($uid, $per_page)
    {
        return $this->model
        ->where('uid', $uid)
        ->with(['notificationTypes', 'user'])
        ->orderBy('id', 'desc')
        ->paginate($per_page);
    }

    public function getAllPaginationFilter($uid, $is_read, $per_page)
    {
        return $this->model
        ->where('uid', $uid)
        ->where('is_read', $is_read)
        ->with(['notificationTypes', 'user'])
        ->orderBy('id', 'desc')
        ->paginate($per_page);
    }

    public function create(array $data)
    {
        // $data['notification_type'] phải là string
        return $this->model->create($data);
    }

    public function createSchedule(array $data)
    {
        $data['notification_type'] = NotificationType::$TYPE_SCHEDULE;
        return $this->model->create($data);
    }

    public function createMessage(array $data)
    {
        $data['notification_type'] = NotificationType::$TYPE_MESSAGE;
        return $this->model->create($data);
    }

    public function createReview(array $data)
    {
        $data['notification_type'] = NotificationType::$TYPE_REVIEW;
        return $this->model->create($data);
    }

    public function markAllAsRead($uid)
    {
        return $this->model->where('uid', $uid)->update(['is_read' => 1]);
    }

    public function update($data, $uid, $id)
    {
        $query = $this->model->where('uid', $uid)->where('id', $id);

        if (!$query->exists()) {
            return null;
        }

        $query->update($data);

        return $query->first(); // Trả về bản ghi đã cập nhật
    }
}
