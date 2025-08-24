<?php

namespace App\Http\Resources;

use App\Models\UserExperience;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserSubjectResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $experience = UserExperience::evaluateExperience($this->years_of_experience);
        return [
            'id' => $this->id,
            'name' => $this->subject->name,
            'years_of_experience' => $this->years_of_experience,
            'subject_id' => $this->subject_id,
            'level' => $experience['level'],
            'class' => $experience['class'],
            'progress' => $experience['progress'],
            'milestone' => $experience['milestone'],
            'user_subject_levels' => $this->userSubjectLevels->map(function ($level) {
                return [
                    'id' => $level->id,
                    'price' => $level->price,
                    'level_id' => $level->education_level_id,
                    'level_name' => $level->education_level->name,
                    'user_subject_id' => $level->user_subject_id,
                ];
            }),
        ];
    }
}
