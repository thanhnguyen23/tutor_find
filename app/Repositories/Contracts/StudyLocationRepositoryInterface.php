<?php

namespace App\Repositories\Contracts;

interface StudyLocationRepositoryInterface
{
    public function getAll();

    public function find($id);
}