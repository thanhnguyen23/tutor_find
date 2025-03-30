<?php

namespace App\Repositories\Eloquent;

use App\Models\User;
use App\Repositories\Contracts\UserRepositoryInterface;
use Illuminate\Support\Facades\Hash;

class UserRepository implements UserRepositoryInterface
{
    // Triển khai các hàm của Interface
    protected $model;

    public function __construct(User $model) {
        $this->model = $model;
    }

    public function findById($id) {
        return $this->model->find($id);
    }

    public function findByEmail($email)
    {
        return $this->model->where('email', $email)->first();
    }

    public function create(array $data) {
        return $this->model->create($data);
    }

    public function findByPhone(string $phone)
    {
        return $this->model->where('phone', $phone)->first();
    }

    public function update(int $id, array $data)
    {
        $user = $this->findById($id);
        if (!$user) {
            return null;
        }

        $user->update($data);
        return $user;
    }

    public function delete(int $id)
    {
        $user = $this->findById($id);
        if (!$user) {
            return false;
        }

        return $user->delete();
    }

    public function getUserDataDetail(int $id)
    {
        return $this->model->where('id', $id)
        ->with([
            'userEducations',
            'userExperiences',
            'userSubjects.subject',
            'userTimeSlots',
            'userWeeklyTimeSlots'
        ])->first();
    }
}
