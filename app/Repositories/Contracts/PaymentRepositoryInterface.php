<?php

namespace App\Repositories\Contracts;

interface PaymentRepositoryInterface
{
    // Định nghĩa các hàm cần thiết
    public function getAll();

    public function create($data);

    public function whereReferenceCode($referenceCode);
}
