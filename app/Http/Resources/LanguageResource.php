<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LanguageResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'uid' => $this->uid,
            'language_id' => $this->language_id,
            'level_id' => $this->level_id,
            'is_native' => $this->is_native,
            'language' => $this->language->name,
            'level' => $this->level_language->name,
        ];
    }
}
