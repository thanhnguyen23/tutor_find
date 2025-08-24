<?php

namespace App\Repositories\Eloquent;

use App\Models\UserStudyLocation;
use App\Repositories\Contracts\UserStudyLocationRepositoryInterface;

class UserStudyLocationRepository implements UserStudyLocationRepositoryInterface
{
    // Triển khai các hàm của Interface
    protected $model;

    public function __construct(UserStudyLocation $model)
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

    public function delete($uid, $id)
    {
        $this->model
        ->where('uid', $uid)
        ->where('id', $id)
        ->delete();
    }

    public function deleteAll($uid) {
        $this->model
        ->where('uid', $uid)
        ->delete();
    }

    public function find(int $id)
    {
        return $this->model->find($id);
    }

    public function findByUid(int $uid)
    {
        return $this->model->where('uid', $uid)->get();
    }

    public function checkExists ($uid, $id) {
        return $this->model->where('uid', $uid)->where('study_location_id')->get();
    }

    public function deleteByUid(int $uid)
    {
        return $this->model->where('uid', $uid)->delete();
    }
}
