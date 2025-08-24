<?php

namespace App\Repositories\Eloquent;

use App\Models\UserEducationLevel;
use App\Repositories\Contracts\UserEducationLevelRepositoryInterface;

class UserEducationLevelRepository implements UserEducationLevelRepositoryInterface
{
    // Triển khai các hàm của Interface
    protected $model;

    public function __construct(UserEducationLevel $model)
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
        $userEducationLevel = $this->model->find($id);
        if (!$userEducationLevel) {
            return null;
        }
        $userEducationLevel->update($data);
        return $userEducationLevel;
    }

}
