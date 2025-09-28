<?php

namespace App\Repositories\Contracts;

interface TutorSessionRepositoryInterface
{
    // Định nghĩa các hàm cần thiết
    public function getAll();
    public function create(array $data);
    public function update(array $data, int $id);
    public function delete(int $id);
    public function find(int $id);
}
