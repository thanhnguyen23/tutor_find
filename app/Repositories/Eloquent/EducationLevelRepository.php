<?php

namespace App\Repositories\Eloquent;

use App\Models\EducationLevel;
use App\Repositories\Contracts\EducationLevelRepositoryInterface;

class EducationLevelRepository implements EducationLevelRepositoryInterface
{
    // Triển khai các hàm của Interface
    protected $model;

    public function __construct(EducationLevel $model)
    {
        $this->model = $model;
    }

    public function getAll()
    {
        return $this->model->all();
    }

    public function find($id)
    {
        return $this->model->find($id);
    }

}