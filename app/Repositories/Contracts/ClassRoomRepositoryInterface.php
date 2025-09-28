<?php

namespace App\Repositories\Contracts;

interface ClassRoomRepositoryInterface
{
    public function paginate(array $filters = [], int $perPage = 10, $uid);
    public function create(array $data);
    public function find(int $id);
    public function findZoom(int $zoom_id);
    public function update(int $id, array $data);
}
