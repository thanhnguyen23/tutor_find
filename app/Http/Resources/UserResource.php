<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
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
            'tutor_session' => $this->when($this->tutorSession, [
                'id' => $this->tutorSession?->id,
                'time' => $this->tutorSession?->time,
                'duration_hours' => $this->tutorSession?->duration_hours,
                'description' => $this->tutorSession?->description,
                'recommended' => $this->tutorSession?->recommended,
                'allow_free' => $this->tutorSession?->allow_free,
            ]),
        ];
    }
}
