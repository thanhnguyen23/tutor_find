<?php

namespace App\Repositories\Eloquent;

use App\Models\UserPackage;
use App\Models\UserPackageFeature;
use App\Repositories\Contracts\UserPackageRepositoryInterface;
use Illuminate\Support\Facades\DB;

class UserPackageRepository implements UserPackageRepositoryInterface
{
    protected $model;

    public function __construct(UserPackage $model)
    {
        $this->model = $model;
    }

    public function getByUserId(int $userId)
    {
        return $this->model
            ->with(['subject', 'level', 'features'])
            ->where('user_id', $userId)
            ->get();
    }

    public function find(int $id)
    {
        return $this->model
            ->with(['subject', 'level', 'features'])
            ->findOrFail($id);
    }

    public function create(array $data)
    {
        return $this->model->create($data);
    }

    public function update(int $id, array $data)
    {
        $userPackage = $this->model->find($id);
        if (!$userPackage) {
            return null;
        }
        $userPackage->update($data);
        return $userPackage;
    }

    public function delete(int $id)
    {
        return $this->model->destroy($id);
    }

    public function createWithFeatures(array $data, array $features)
    {
        return DB::transaction(function () use ($data, $features) {
            $package = $this->create($data);

            foreach ($features as $feature) {
                $package->features()->create(['feature' => $feature]);
            }

            return $package->load('features');
        });
    }

    public function updateWithFeatures(int $id, array $data, array $features)
    {
        return DB::transaction(function () use ($id, $data, $features) {
            $package = $this->update($id, $data);

            // Delete existing features
            $package->features()->delete();

            // Create new features
            foreach ($features as $feature) {
                $package->features()->create(['feature' => $feature]);
            }

            return $package->load('features');
        });
    }
}
