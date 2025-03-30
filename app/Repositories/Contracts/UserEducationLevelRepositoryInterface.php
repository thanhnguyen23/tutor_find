<?php

namespace App\Repositories\Contracts;

interface UserEducationLevelRepositoryInterface
{
    public function getAll();
    public function create(array $data);
    public function update(array $data, $id);
}
