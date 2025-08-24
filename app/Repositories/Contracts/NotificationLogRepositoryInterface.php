<?php

namespace App\Repositories\Contracts;

interface NotificationLogRepositoryInterface
{
    // Định nghĩa các hàm cần thiết

    public function getAllPagination($uid, $per_page);
    public function getAllPaginationFilter($uid, $is_read, $per_page);
    public function create(array $data);
    public function createSchedule(array $data);
    public function createMessage(array $data);
    public function createReview(array $data);
    public function markAllAsRead($uid);
    public function update($data, $uid, $id);
}
