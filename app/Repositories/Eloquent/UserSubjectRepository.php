<?php

namespace App\Repositories\Eloquent;

use App\Models\UserSubject;
use App\Repositories\Contracts\UserSubjectRepositoryInterface;

class UserSubjectRepository implements UserSubjectRepositoryInterface
{
    // Triển khai các hàm của Interface
    protected $model;

    public function __construct(UserSubject $model)
    {
        $this->model = $model;
    }

    public function getAll()
    {
        return $this->model->all();
    }

    public function create(array $data)
    {
        return $this->model->create($data);
    }

    public function update(array $data, int $id)
    {
        $userSubject = $this->model->find($id);
        if (!$userSubject) {
            return null;
        }
        $userSubject->update($data);
        return $userSubject;
    }

    public function delete(int $id)
    {
        $userSubject = $this->model->find($id);
        $userSubject->delete();
        return $userSubject;
    }

    public function find(int $id)
    {
        return $this->model->find($id);
    }

    public function findByUserId(int $userId)
    {
        return $this->model->where('user_id', $userId)->get();
    }

    public function findByUserIdAndSubjectId(int $userId, int $subjectId)
    {
        return $this->model->where('user_id', $userId)->where('subject_id', $subjectId)->first();
    }

    public function deleteByUserId(int $userId)
    {
        return $this->model->where('user_id', $userId)->delete();
    }

    public function findByUserAndSubject($userId, $subjectId)
    {
        return $this->model->where('user_id', $userId)
            ->where('subject_id', $subjectId)
            ->first();
    }

    public function existsByUserAndSubject(int $userId, int $subjectId, int $excludeId = null): bool
    {
        $query = $this->model->where('user_id', $userId)
            ->where('subject_id', $subjectId);
        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }
        return $query->exists();
    }
}
