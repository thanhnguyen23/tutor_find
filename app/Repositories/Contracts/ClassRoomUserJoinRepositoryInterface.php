<?php

namespace App\Repositories\Contracts;

interface ClassRoomUserJoinRepositoryInterface
{
    // Định nghĩa các hàm cần thiết
    public function create(array $data);
    public function find(int $id);
    public function findByClassRoomId(int $classRoomId);
    public function findByUserId(int $userId);
    public function findByClassRoomIdAndUserId($classRoomId, $userId);
    public function delete(int $id);
    public function deleteByClassRoomId(int $classRoomId);
    public function deleteByUserId(int $userId);
    public function deleteByClassRoomIdAndUserId(int $classRoomId, int $userId);
}
