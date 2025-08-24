<?php

namespace App\Repositories\Contracts;

interface NotificationTypeRepositoryInterface
{
    // Định nghĩa các hàm cần thiết
    public function getAll();
    public function findByType($type);
}
