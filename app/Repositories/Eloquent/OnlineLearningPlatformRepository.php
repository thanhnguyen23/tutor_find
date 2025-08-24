<?php

namespace App\Repositories\Eloquent;

use App\Models\OnlineLearningPlatform;
use App\Repositories\Contracts\OnlineLearningPlatformRepositoryInterface;

class OnlineLearningPlatformRepository implements OnlineLearningPlatformRepositoryInterface
{
    protected $model;

    public function __construct(OnlineLearningPlatform $model)
    {
        $this->model = $model;
    }
    // Triển khai các hàm của Interface
    public function getAll()
    {
        return $this->model->all();
    }
}