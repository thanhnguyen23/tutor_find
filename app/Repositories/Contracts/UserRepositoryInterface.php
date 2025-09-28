<?php

namespace App\Repositories\Contracts;

interface UserRepositoryInterface
{
    public function create(array $data);
    public function findByUid(string $uid);
    public function findByEmail(string $email);
    public function findByPhone(string $phone);
    public function findById(int $id);
    public function update(int $id, array $data);
    public function delete(int $id);
    public function getUserDataDetail(int $id);
    public function getTutorsWithDetails($per_page);
    public function getUserDetailByUid(string $uid);
    public function searchTutors(array $filters, $per_page);
}
