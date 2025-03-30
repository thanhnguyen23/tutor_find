<?php

namespace App\Repositories\Contracts;

interface EducationLevelRepositoryInterface
{
    // Định nghĩa các hàm cần thiết
    public function getAll();

    public function find($id);
}