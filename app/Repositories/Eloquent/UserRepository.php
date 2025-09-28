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

    public function findByUid($uid)
    {
        return $this->model->where('uid', $uid)->first();
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
        $user = $this->model->find($id);
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
            'educationLevel',
            'userEducations',
            'userExperiences',
            'userSubjects.subject',
            'userSubjects.userSubjectLevels.education_level',
            'userWeeklyTimeSlots.timeSlot',
            'userPackages.subject',
            'userPackages.level',
            'userPackages.features',
            'userLanguages.language',
            'userLanguages.level_language',
            'userStudyLocations',
            'tutorSession'
        ])->first();
    }

    public function getTutorsWithDetails($per_page)
    {
        return $this->model
            ->where('role', 1)
            ->with([
                'educationLevel',
                'userSubjects.subject',
                'userSubjects.userSubjectLevels.education_level',
                'userEducations',
                'userExperiences'
            ])
            ->limit(9)
            ->paginate($per_page);
    }

    public function getUserDetailByUid($uid)
    {
        return $this->model
            ->where('uid', $uid)
            ->where('role', 1)
            ->with([
                'educationLevel',
                'userSubjects.subject',
                'userSubjects.userSubjectLevels.education_level',
                'userEducations',
                'userExperiences',
                'userWeeklyTimeSlots',
                'userLanguages.language',
                'userLanguages.level_language',
                'userPackages.subject',
                'userPackages.level',
                'userPackages.features',
                'userStudyLocations.studyLocation'
            ])->first();
    }

    public function searchTutors($filters, $per_page)
    {
        $query = $this->model
            ->where('role', 1)
            ->where('profile_completed', 1)
            ->with([
                'educationLevel',
                'userSubjects.subject',
                'userSubjects.userSubjectLevels.education_level',
                'userEducations',
                'userExperiences',
                'provinces',
                'districts',
                'wards',
            ]);

        // Filter by subject
        if (!empty($filters['subject_id'])) {
            $query->whereHas('userSubjects', function ($q) use ($filters) {
                $q->where('subject_id', $filters['subject_id']);
            });
        }

        // Filter by education level
        if (!empty($filters['education_level_id'])) {
            $query->whereHas('userSubjects.userSubjectLevels', function ($q) use ($filters) {
                $q->where('education_level_id', $filters['education_level_id']);
            });
        }

        // Filter by experience
        if (!empty($filters['experience'])) {
            switch ($filters['experience']) {
                case 'new':
                    $query->whereHas('userSubjects', function ($q) {
                        $q->where('years_of_experience', '<', 3);
                    });
                    break;
                case 'experienced':
                    $query->whereHas('userSubjects', function ($q) {
                        $q->where('years_of_experience', '>=', 3)
                          ->where('years_of_experience', '<', 5);
                    });
                    break;
                case 'expert':
                    $query->whereHas('userSubjects', function ($q) {
                        $q->where('years_of_experience', '>=', 5);
                    });
                    break;
            }
        }

        // Filter by price range
        if (!empty($filters['price_range'])) {
            $priceRange = explode('-', $filters['price_range']);
            if (count($priceRange) === 2) {
                $minPrice = (int)$priceRange[0];
                $maxPrice = $priceRange[1] === '+' ? null : (int)$priceRange[1];

                $query->whereHas('userSubjects.userSubjectLevels', function ($q) use ($minPrice, $maxPrice) {
                    if ($maxPrice) {
                        $q->whereBetween('price', [$minPrice, $maxPrice]);
                    } else {
                        $q->where('price', '>=', $minPrice);
                    }
                });
            }
        }

        // Filter by province
        if (!empty($filters['provinces_id'])) {
            $query->where('provinces_id', $filters['provinces_id']);
        }
        // Filter by sex
        if (!empty($filters['sex'])) {
            $query->where('sex', $filters['sex']);
        }

        // Sort results
        if (!empty($filters['sort_by'])) {
            switch ($filters['sort_by']) {
                case 'rating':
                    // Sort by average rating (placeholder - you might need to add rating field)
                    $query->orderBy('id', 'desc');
                    break;
                case 'price_low':
                    $query->whereHas('userSubjects.userSubjectLevels', function ($q) {
                        $q->orderBy('price', 'asc');
                    });
                    break;
                case 'price_high':
                    $query->whereHas('userSubjects.userSubjectLevels', function ($q) {
                        $q->orderBy('price', 'desc');
                    });
                    break;
                case 'experience':
                    $query->whereHas('userSubjects', function ($q) {
                        $q->orderBy('years_of_experience', 'desc');
                    });
                    break;
                default:
                    $query->orderBy('id', 'desc');
            }
        } else {
            $query->orderBy('id', 'desc');
        }

        return $query->paginate($per_page);
    }
}
