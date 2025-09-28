<?php

namespace App\Repositories\Eloquent;

use App\Models\ComplaintType;
use App\Repositories\Contracts\ComplaintTypeRepositoryInterface;

class ComplaintTypeRepository implements ComplaintTypeRepositoryInterface
{
    // Triển khai các hàm của Interface
    protected $model;

    public function __construct(ComplaintType $model)
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
