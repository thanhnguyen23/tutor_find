<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserPackageResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'subject_id' => $this->subject_id,
            'level_id' => $this->level_id,
            'name' => $this->name,
            'description' => $this->description,
            'number_of_lessons' => $this->number_of_lessons,
            'duration' => $this->duration,
            'price' => $this->price,
            'features' => $this->features,
            'subject' => [
                'id' => $this->subject->id,
                'name' => $this->subject->name,
            ],
            'level' => [
                'id' => $this->level->id,
                'name' => $this->level->name,
            ],
        ];
    }
}
