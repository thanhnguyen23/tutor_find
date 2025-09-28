<?php

namespace App\Repositories\Eloquent;

use App\Models\ClassRoomUserJoin;
use App\Repositories\Contracts\ClassRoomUserJoinRepositoryInterface;

class ClassRoomUserJoinRepository implements ClassRoomUserJoinRepositoryInterface
{
    // Triển khai các hàm của Interface
    protected $model;

    public function __construct(ClassRoomUserJoin $model)
    {
        $this->model = $model;
    }

    public function create(array $data)
    {
        return $this->model->create($data);
    }

    public function find(int $id)
    {
        return $this->model->find($id);
    }

    public function findByClassRoomId(int $classRoomId)
    {
        return $this->model->where('class_room_id', $classRoomId)->get();
    }

    public function findByUserId(int $userId)
    {
        return $this->model->where('uid', $userId)->get();
    }

    public function findByClassRoomIdAndUserId($classRoomId, $userId)
    {
        return $this->model->where('class_room_id', $classRoomId)->where('uid', $userId)->first();
    }

    public function delete(int $id)
    {
        return $this->model->destroy($id);
    }

    public function deleteByClassRoomId(int $classRoomId)
    {
        return $this->model->where('class_room_id', $classRoomId)->delete();
    }

    public function deleteByUserId(int $userId)
    {
        return $this->model->where('uid', $userId)->delete();
    }

    public function deleteByClassRoomIdAndUserId(int $classRoomId, int $userId)
    {
        return $this->model->where('class_room_id', $classRoomId)->where('uid', $userId)->delete();
    }
}
