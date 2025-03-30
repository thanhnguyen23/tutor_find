<?php

namespace App\Repositories\Eloquent;

use App\Models\Packages;
use App\Repositories\Contracts\PackageRepositoryInterface;

class PackageRepository implements PackageRepositoryInterface
{
    protected $package;

    public function __construct(Packages $package)
    {
        $this->package = $package;
    }
    // Triển khai các hàm của Interface
    public function getAll()
    {
        return $this->package->all();
    }
}