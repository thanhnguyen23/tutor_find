<?php

namespace App\Repositories\Eloquent;

use App\Models\Payment;
use App\Repositories\Contracts\PaymentRepositoryInterface;

class PaymentRepository implements PaymentRepositoryInterface
{
    // Triển khai các hàm của Interface

    protected $model;

    public function __construct(Payment $model)
    {
        $this->model = $model;
    }
    // Triển khai các hàm của Interface
    public function getAll()
    {
        return $this->model->all();
    }

    public function create($data)
    {
        return $this->model->create($data);
    }

    public function whereReferenceCode($referenceCode)
    {
        return $this->model->where('reference_code', $referenceCode)->first();
    }
}
