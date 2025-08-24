<?php

namespace App\Repositories\Contracts;

interface UserBookingRepositoryInterface
{
    // Định nghĩa các hàm cần thiết
    public function create(array $data);

    /**
     * Lấy booking theo id
     */
    public function find($id, $uid);

    public function getAll($uid);

    public function getAllPagination($uid, $perPage, $status, $code);

    /**
     * Lấy lịch học sắp tới
     * Tiêu chí: status == confirmed hoặc pending và date >= today
     */
    public function getUpcomingLessons($uid, $perPage, $today);
}
