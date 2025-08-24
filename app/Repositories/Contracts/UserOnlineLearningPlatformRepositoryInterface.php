<?php

namespace App\Repositories\Contracts;

interface UserOnlineLearningPlatformRepositoryInterface
{
    public function create(array $data);
    public function update(array $data, int $id);
    public function delete(int $id);
    public function find(int $id);
    public function findByUid(int $uid);
    public function deleteByUid(int $uid);
}