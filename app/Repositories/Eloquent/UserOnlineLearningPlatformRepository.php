<?php

namespace App\Repositories\Eloquent;

use App\Models\UserOnlineLearningPlatform;
use App\Repositories\Contracts\UserOnlineLearningPlatformRepositoryInterface;

class UserOnlineLearningPlatformRepository implements UserOnlineLearningPlatformRepositoryInterface
{
    // Triển khai các hàm của Interface
    protected $model;

    public function __construct(UserOnlineLearningPlatform $model)
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
        $dataFind = $this->model->find($id);
        if (!$dataFind) {
            return null;
        }
        $dataFind->update($data);
        return $dataFind;
    }

    public function delete(int $id)
    {
        $dataFind = $this->model->find($id);
        $dataFind->delete();
        return $dataFind;
    }

    public function find(int $id)
    {
        return $this->model->find($id);
    }

    public function findByUid(int $uid)
    {
        return $this->model->where('uid', $uid)->get();
    }

    public function deleteByUid(int $uid)
    {
        return $this->model->where('uid', $uid)->delete();
    }
}
