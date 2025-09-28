<?php

namespace App\Repositories\Contracts;

interface UserBookingRepositoryInterface
{
    // Định nghĩa các hàm cần thiết
    public function create(array $data);

    public function updateStatus($id, $status);

    /**
     * Lấy booking theo id
     */
    public function find($id, $uid);

    public function getAll($uid, $perPage, $status, $code);

    public function getAvailableForClassroom($uid, $code);

    public function getAllPagination($uid, $perPage, $status, $code);

    public function updateExpireUserBooking();

    /**
     * Lấy lịch học sắp tới
     * Tiêu chí: status == confirmed hoặc pending và date >= today
     */
    public function getUpcomingLessons($uid, $perPage, $today);

    /**
     * Lấy booking với time slots để tạo classroom
     */
    public function findForClassroom($bookingId);

    /**
     * Kiểm tra booking đã có classroom chưa
     */
    public function hasClassroom($bookingId);

    /**
     * Kiểm tra user đã sử dụng free trial chưa
     */
    public function hasUsedFreeTrial($uid);
}
