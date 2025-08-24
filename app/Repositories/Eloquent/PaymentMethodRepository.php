<?php

namespace App\Repositories\Eloquent;

use App\Models\PaymentMethod;
use App\Repositories\Contracts\PaymentMethodRepositoryInterface;

class PaymentMethodRepository implements PaymentMethodRepositoryInterface
{
    // Triển khai các hàm của Interface

    protected $model;

    public function __construct(PaymentMethod $model)
    {
        $this->model = $model;
    }
    // Triển khai các hàm của Interface
    public function getAll()
    {
        return $this->model->all();
    }
}