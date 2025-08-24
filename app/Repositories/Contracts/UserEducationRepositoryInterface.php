<?php

namespace App\Repositories\Contracts;

interface UserEducationRepositoryInterface
{
    // Định nghĩa các hàm cần thiết
    public function getAll();
    public function create(array $data);
    public function update(array $data, $id);
    public function delete(int $id);
    public function find(int $id);
    public function findByUserId(int $userId);
    public function deleteByUserId(int $userId);
}
