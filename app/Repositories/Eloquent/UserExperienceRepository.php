<?php

namespace App\Repositories\Eloquent;

use App\Models\UserExperience;
use App\Repositories\Contracts\UserExperienceRepositoryInterface;

class UserExperienceRepository implements UserExperienceRepositoryInterface
{
    // Triển khai các hàm của Interface
    protected $model;

    public function __construct(UserExperience $model)
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

    public function delete(int $id)
    {
        $userExperience = $this->model->find($id);
        $userExperience->delete();
        return $userExperience;
    }


    public function update(array $data, $id)
    {
        $userExperience = $this->model->find($id);
        if (!$userExperience) {
            return null;
        }
        $userExperience->update($data);
        return $userExperience;
    }

    public function find(int $id)
    {
        return $this->model->find($id);
    }

    public function findByUserId(int $userId)
    {
        return $this->model->where('user_id', $userId)->get();
    }

    public function deleteByUserId(int $userId)
    {
        return $this->model->where('user_id', $userId)->delete();
    }
}
