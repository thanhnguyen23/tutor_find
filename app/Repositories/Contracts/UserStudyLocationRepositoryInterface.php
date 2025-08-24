<?php

namespace App\Repositories\Contracts;

interface UserStudyLocationRepositoryInterface
{
    public function create(array $data);
    public function update(array $data, int $id);
    public function delete($uid, $id);
    public function deleteAll($uid);
    public function find(int $id);
    public function findByUid(int $uid);
    public function deleteByUid(int $uid);
}