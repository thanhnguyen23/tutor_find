<?php

namespace App\Repositories\Eloquent;

use App\Models\UserEducation;
use App\Repositories\Contracts\UserEducationRepositoryInterface;

class UserEducationRepository implements UserEducationRepositoryInterface
{
    // Triển khai các hàm của Interface
    protected $model;

    public function __construct(UserEducation $model)
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

    public function update(array $data, $id)
    {
        $userEducation = $this->model->find($id);
        $userEducation->update($data);
        return $userEducation;
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
