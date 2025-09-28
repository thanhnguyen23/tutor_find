<?php

namespace App\Repositories\Eloquent;

use App\Models\UserLanguage;
use App\Repositories\Contracts\UserLanguageRepositoryInterface;

class UserLanguageRepository implements userLanguageRepositoryInterface
{
    // Triển khai các hàm của Interface
    protected $model;

    public function __construct(UserLanguage $model)
    {
        $this->model = $model;
    }

    public function getLanguagesByUser(int $uid)
    {
        return $this->model->where('uid', $uid)->get();
    }

    public function create(array $data)
    {
        return $this->model->create($data);
    }

    public function update(array $data, $id)
    {
        $dataFind = $this->model->find($id);
        if (!$dataFind) {
            return null;
        }
        $dataFind->update($data);
        return $dataFind;
    }

    public function delete($id)
    {
        return $this->model->where('id', $id)->delete();
    }

    public function find($id)
    {
        return $this->model->find($id);
    }

    public function all()
    {
        return $this->model->all();
    }

    public function existsByUserAndLanguage(string $uid, int $languageId): bool
    {
        $query = $this->model->where('uid', $uid)
            ->where('language_id', $languageId);
        return $query->exists();
    }

    public function existsNativeLanguage(string $uid): bool
    {
        $query = $this->model->where('uid', $uid)
            ->where('is_native', 1);
        return $query->exists();
    }
}
