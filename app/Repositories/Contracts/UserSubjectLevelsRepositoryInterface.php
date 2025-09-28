<?php

namespace App\Repositories\Contracts;

interface UserSubjectLevelsRepositoryInterface
{
    // Định nghĩa các hàm cần thiết
    public function create(array $data);
    public function update(array $data, int $id);
    public function delete(int $id);
    public function deleteAll(int $userSubjectId);
    public function getLevelsByUserSubject($userSubjectId);
    public function checkLevelExists($userSubjectId, $levelId);
    public function getPriceByUserSubjectAndLevel($userId, $subjectId, $educationLevelId);
}
