<?php

namespace App\Repositories\Eloquent;

use App\Models\DayOfWeek;
use App\Repositories\Contracts\DayOfWeekRepositoryInterface;

class DayOfWeekRepository implements DayOfWeekRepositoryInterface
{
    protected $dayOfWeek;

    public function __construct(DayOfWeek $dayOfWeek)
    {
        $this->dayOfWeek = $dayOfWeek;
    }
    // Triển khai các hàm của Interface
    public function getAll() {
        return $this->dayOfWeek->all();
    }
}