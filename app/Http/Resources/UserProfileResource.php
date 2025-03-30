<?php

namespace App\Http\Resources;

use App\Models\UserExperience;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserProfileResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'full_name' => $this->full_name,
            'email' => $this->email,
            'phone' => $this->phone,
            'address' => $this->address,
            'about_you' => $this->about_you,
            'role' => $this->roleName(),
            'avatar' => $this->avatar,
            'user_subjects' => $this->userSubjects->map(function ($subject) {
                $experience = UserExperience::evaluateExperience($subject->years_of_experience);
                return [
                    'id' => $subject->id,
                    'name' => $subject->subject->name,
                    'years_of_experience' => $subject->years_of_experience,
                    'subject_id' => $subject->subject_id,
                    'level' => $experience['level'],
                    'class' => $experience['class'],
                    'progress' => $experience['progress'],
                    'milestone' => $experience['milestone'],
                ];
            }),
            'user_educations' => $this->userEducations->map(function ($education) {
                return [
                    'id' => $education->id,
                    'school_name' => $education->school_name,
                    'major' => $education->major,
                    'time' => $education->time,
                    'description' => $education->description,
                ];
            }),
            'user_experiences' => $this->userExperiences->map(function ($experience) {
                return [
                    'id' => $experience->id,
                    'name' => $experience->name,
                    'position' => $experience->position,
                    'time' => $experience->time,
                    'description' => $experience->description,
                ];
            }),
            'user_time_slots' => $this->userTimeSlots->map(function ($timeSlot) {
                return [
                    'id' => $timeSlot->id,
                    'start_time' => $timeSlot->start_time,
                    'end_time' => $timeSlot->end_time,
                ];
            }),
            'user_weekly_time_slots' => $this->userWeeklyTimeSlots->map(function ($weeklyTimeSlot) {
                return [
                    'id' => $weeklyTimeSlot->id,
                    'day' => $weeklyTimeSlot->day,
                    'start_time' => $weeklyTimeSlot->start_time,
                    'end_time' => $weeklyTimeSlot->end_time,
                ];
            }),
        ];
    }
}
