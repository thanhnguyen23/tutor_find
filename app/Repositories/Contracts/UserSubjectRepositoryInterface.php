<?php

namespace App\Repositories\Contracts;

interface UserSubjectRepositoryInterface
{
    public function create(array $data);
    public function update(array $data, int $id);
    public function delete(int $id);
    public function find(int $id);
    public function findByUserId(int $userId);
    public function findByUserIdAndSubjectId(int $userId, int $subjectId);
    public function deleteByUserId(int $userId);
    /**
     * Kiểm tra user đã có subject này chưa
     */
    public function existsByUserAndSubject(int $userId, int $subjectId, int $excludeId = null): bool;
}
