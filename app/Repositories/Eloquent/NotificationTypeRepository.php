<?php

namespace App\Repositories\Eloquent;

use App\Models\NotificationType;
use App\Repositories\Contracts\NotificationTypeRepositoryInterface;

class NotificationTypeRepository implements NotificationTypeRepositoryInterface
{
    // Triển khai các hàm của Interface
    protected $model;

    public function __construct(NotificationType $model)
    {
        $this->model = $model;
    }
    // Triển khai các hàm của Interface
    public function getAll()
    {
        return $this->model->all();
    }

    public function findByType($type)
    {
        return $this->model->where('name', $type)->first();
    }
}
