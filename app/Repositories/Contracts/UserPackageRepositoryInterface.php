<?php

namespace App\Repositories\Contracts;

interface UserPackageRepositoryInterface
{
    public function getByUserId(int $userId);
    public function find(int $id);
    public function create(array $data);
    public function update(int $id, array $data);
    public function delete(int $id);
    public function createWithFeatures(array $data, array $features);
    public function updateWithFeatures(int $id, array $data, array $features);
}
