<?php

namespace App\Repositories\Eloquent;

use App\Models\TutorSession;
use App\Repositories\Contracts\TutorSessionRepositoryInterface;

class TutorSessionRepository implements TutorSessionRepositoryInterface
{
    // Triển khai các hàm của Interface
    protected $model;

    public function __construct(TutorSession $model)
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
        return $this->model->find($id)->update($data);
    }

    public function delete(int $id)
    {
        return $this->model->find($id)->delete();
    }

    public function find(int $id)
    {
        return $this->model->find($id);
    }
}
