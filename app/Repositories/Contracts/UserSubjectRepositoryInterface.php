<?php

namespace App\Repositories\Contracts;

interface UserSubjectRepositoryInterface
{
    public function create(array $data);
    public function findByUserId(int $userId);
    public function deleteByUserId(int $userId);
}
