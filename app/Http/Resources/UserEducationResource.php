<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserEducationResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'school_name' => $this->school_name,
            'major' => $this->major,
            'time' => $this->time,
            'description' => $this->description,
            'certificate' => $this->certificate ? "/storage/" . $this->certificate : null,
        ];
    }
}
