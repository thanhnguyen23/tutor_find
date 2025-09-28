<?php

namespace App\Http\Resources;

use App\Models\UserExperience;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Services\UserService;

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
            'cccd' => $this->cccd,
            'sex' => $this->sex,
            'referral_link' => $this->referral_link,
            'is_free_study' => $this->is_free_study,
            'tutor_session_id' => $this->tutor_session_id,
            'provinces_id' => $this->provinces_id,
            'districts_id' => $this->districts_id,
            'wards_id' => $this->wards_id,
            'profile_completion' => app(UserService::class)->calculateProfileCompletion($this),
            'profile_completed' => $this->profile_completed,
            'education_level' => $this->educationLevel ? [
                'id' => $this->educationLevel?->id,
                'name' => $this->educationLevel?->name,
                'description' => $this->educationLevel?->description,
            ] : null,
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
                    'user_subject_levels' => $subject->userSubjectLevels->map(function ($level) {
                        return [
                            'id' => $level->id,
                            'price' => $level->price,
                            'level_id' => $level->education_level_id,
                            'level_name' => $level->education_level->name,
                            'user_subject_id' => $level->user_subject_id,
                        ];
                    }),
                ];
            }),
            'user_educations' => $this->userEducations->map(function ($education) {
                return [
                    'id' => $education->id,
                    'school_name' => $education->school_name,
                    'major' => $education->major,
                    'time' => $education->time,
                    'description' => $education->description,
                    'certificate' => $education->certificate ? "/storage/" . $education->certificate : null,
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
            'user_time_slots' => TimeSlotResource::collection($this->userTimeSlots),
            'user_weekly_time_slots' => $this->userWeeklyTimeSlots->map(function ($weeklyTimeSlot) {
                return [
                    'id' => $weeklyTimeSlot->id,
                    'day_of_week_id' => $weeklyTimeSlot->day_of_week_id,
                    'time_slot_id' => $weeklyTimeSlot->time_slot_id,
                    'time_slot_name' => optional($weeklyTimeSlot->timeSlot)->name,
                ];
            }),
            'user_packages' => $this->userPackages
            ->groupBy('subject_id')
            ->map(function ($packages, $subjectId) {
                $firstPackage = $packages->first();
                return [
                    'subject_id' => $subjectId,
                    'subject_name' => $firstPackage->subject->name,
                    'packages' => $packages->map(function ($package) {
                        return [
                            'id' => $package->id,
                            'name' => $package->name,
                            'price' => $package->price,
                            'subject' => $package->subject->name,
                            'subject_id' => $package->subject_id,
                            'level' => $package->level->name,
                            'level_id' => $package->level_id,
                            'level_name' => $package->level->name,
                            'number_of_lessons' => $package->number_of_lessons,
                            'duration' => $package->duration,
                            'description' => $package->description,
                            'features' => $package->features->map(function ($feature) {
                                return $feature->feature;
                            }),
                            'total_features' => $package->features->count()
                        ];
                    })
                ];
            })->values(),
            'user_languages' => $this->userLanguages->map(function ($language) {
                return [
                    'id' => $language->id,
                    'language' => $language->language?->name,
                    'language_id' => $language->language_id,
                    'level' => $language->level_language?->name,
                    'level_language_id' => $language->level_language_id,
                    'is_native' => $language->is_native,
                ];
            }),
            'user_study_locations' => $this->userStudyLocations->map(function ($studyLocations) {
                return [
                    'id' => $studyLocations->id,
                    'uid' => $studyLocations->uid,
                    'study_location_id' => $studyLocations->study_location_id,
                    'max_distance' => $studyLocations->max_distance,
                    'transportation_fee' => $studyLocations->transportation_fee,
                ];
            }),
            'tutor_session' => $this->tutorSession ? [
                'id' => $this->tutorSession->id,
                'time' => $this->tutorSession->time,
                'duration_hours' => $this->tutorSession->duration_hours,
                'description' => $this->tutorSession->description,
                'recommended' => $this->tutorSession->recommended,
                'allow_free' => $this->tutorSession->allow_free,
            ] : null,
        ];
    }
}
