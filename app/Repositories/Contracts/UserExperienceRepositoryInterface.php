<?php

namespace App\Repositories\Contracts;

interface UserExperienceRepositoryInterface
{
    public function create(array $data);
    public function findByUserId(int $userId);
    public function deleteByUserId(int $userId);
}
