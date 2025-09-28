<?php

namespace App\Repositories\Eloquent;

use App\Models\UserSubjectLevels;
use App\Repositories\Contracts\UserSubjectLevelsRepositoryInterface;

class UserSubjectLevelsRepository implements UserSubjectLevelsRepositoryInterface
{
    protected $model;

    public function __construct(UserSubjectLevels $model)
    {
        $this->model = $model;
    }
    // Triển khai các hàm của Interface
    public function create(array $data)
    {
        return $this->model->create($data);
    }

    public function update(array $data, int $id)
    {
        $userSubjectLevel = $this->model->find($id);
        if (!$userSubjectLevel) {
            return null;
        }
        $userSubjectLevel->update($data);
        return $userSubjectLevel;
    }

    public function delete(int $id)
    {
        return $this->model->find($id)->delete();
    }

    public function deleteAll(int $userSubjectId)
    {
        return $this->model->where('user_subject_id', $userSubjectId)->delete();
    }

    public function getLevelsByUserSubject($userSubjectId)
    {
        return $this->model->where('user_subject_id', $userSubjectId)
            ->with('education_level')
            ->get()
            ->map(function ($level) {
                return [
                    'id' => $level->education_level->id,
                    'name' => $level->education_level->name,
                    'price' => $level->price
                ];
            });
    }

    public function checkLevelExists($userSubjectId, $levelId)
    {
        return $this->model->where('user_subject_id', $userSubjectId)
            ->where('education_level_id', $levelId)
            ->exists();
    }

    public function getPriceByUserSubjectAndLevel($userId, $subjectId, $educationLevelId)
    {
        return $this->model->whereHas('user_subject', function($query) use ($userId, $subjectId) {
            $query->where('user_id', $userId)
                  ->where('subject_id', $subjectId);
        })->where('education_level_id', $educationLevelId)->first();
    }
}
