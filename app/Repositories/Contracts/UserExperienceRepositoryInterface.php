<?php

namespace App\Repositories\Contracts;

interface UserExperienceRepositoryInterface
{
    public function create(array $data);
    public function update(array $data, $id);
    public function findByUserId(int $userId);
    public function deleteByUserId(int $userId);
    public function delete(int $id);
    public function find(int $id);
}
