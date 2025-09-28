<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TutorResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'uid' => $this->uid,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'full_name' => "{$this->first_name} {$this->last_name}",
            'address' => $this->address,
            'avatar' => $this->avatar,
            'about_you' => $this->about_you,
            'role' => $this->role,
            'provinces_name' => $this->provinces?->name,
            'districts_name' => $this->districts?->name,
            'free_study'=> $this->free_study,
            'is_free_study' => $this->is_free_study,
            'tutor_session_id' => $this->tutor_session_id,
            'user_subjects' => $this->userSubjects->map(function ($userSubject) {
                return [
                    'id' => $userSubject->id,
                    'subject_id' => $userSubject->subject_id,
                    'years_of_experience' => $userSubject->years_of_experience,
                    'subject_name' => $userSubject->subject->name,
                    'subject_image' => $userSubject->subject->image,
                    'subject_slug' => $userSubject->subject->slug,
                    'user_subject_levels' => $userSubject->userSubjectLevels->map(function ($userSubjectLevel) {
                        return [
                            'id' => $userSubjectLevel->id,
                            'education_level_id' => $userSubjectLevel->education_level_id,
                            'price' => $userSubjectLevel->price,
                            'education_level' => $userSubjectLevel->education_level->name,
                            'education_level_slug' => $userSubjectLevel->education_level->slug,
                            'education_level_image' => $userSubjectLevel->education_level->image,
                            'education_level_description' => $userSubjectLevel->education_level->description,
                        ];
                    }),
                ];
            }),
            'user_educations' => $this->userEducations,
            'user_experiences' => $this->userExperiences,
            'user_weekly_time_slots' => $this->userWeeklyTimeSlots->map(function ($weeklyTimeSlot) {
                return [
                    'id' => $weeklyTimeSlot->id,
                    'day_of_week_id' => $weeklyTimeSlot->day_of_week_id,
                    'day_of_week_name' => $weeklyTimeSlot->dayOfWeek->name,
                    'day_of_week_code' => $weeklyTimeSlot->dayOfWeek->code,
                    'time_slot_id' => $weeklyTimeSlot->time_slot_id,
                    'time_slot_name' => optional($weeklyTimeSlot->timeSlot)->name,
                ];
            }),
            'user_languages' => $this->userLanguages->map(function ($userLanguage) {
                return [
                    'id' => $userLanguage->id,
                    'uid' => $userLanguage->uid,
                    'language_id' => $userLanguage->language_id,
                    'level_language_id' => $userLanguage->level_language_id,
                    'is_native' => $userLanguage->is_native,
                    'language' => $userLanguage->language->name,
                    'level_language' => $userLanguage->level_language->name,
                    'emoji' => $userLanguage->language->emoji,
                ];
            }),
            'user_packages' => $this->userPackages->map(function ($userPackage) {
                return [
                    'id' => $userPackage->id,
                    'user_id' => $userPackage->user_id,
                    'subject_id' => $userPackage->subject_id,
                    'level_id' => $userPackage->level_id,
                    'name' => $userPackage->name,
                    'description' => $userPackage->description,
                    'number_of_lessons' => $userPackage->number_of_lessons,
                    'duration' => $userPackage->duration,
                    'price' => $userPackage->price,
                    'subject' => $userPackage->subject->name,
                    'subject_image' => $userPackage->subject->image,
                    'subject_slug' => $userPackage->subject->slug,
                    'level' => $userPackage->level->name,
                    'level_image' => $userPackage->level->image,
                    'level_slug' => $userPackage->level->slug,
                    'level_description' => $userPackage->level->description,
                    'features' => $userPackage->features->map(function ($feature) {
                        return [
                            'id' => $feature->id,
                            'feature' => $feature->feature,
                        ];
                    }),
                ];
            }),
            'user_study_locations' => $this->userStudyLocations->map(function ($studyLocations) {
                return [
                    'id' => $studyLocations->id,
                    'uid' => $studyLocations->uid,
                    'study_location_id' => $studyLocations->study_location_id,
                    'max_distance' => $studyLocations->max_distance,
                    'transportation_fee' => $studyLocations->transportation_fee,
                    'study_location' => $studyLocations->studyLocation,
                ];
            }),
        ];
    }
}
