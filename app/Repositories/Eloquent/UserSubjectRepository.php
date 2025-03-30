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

    public function update(array $data, $id)
    {
        $userSubject = $this->model->find($id);
        $userSubject->update($data);
        return $userSubject;
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
