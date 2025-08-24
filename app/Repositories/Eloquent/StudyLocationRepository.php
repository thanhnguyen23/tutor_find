<?php

namespace App\Repositories\Eloquent;

use App\Models\StudyLocation;
use App\Repositories\Contracts\StudyLocationRepositoryInterface;

class StudyLocationRepository implements StudyLocationRepositoryInterface
{
    protected $model;

    public function __construct(StudyLocation $model)
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