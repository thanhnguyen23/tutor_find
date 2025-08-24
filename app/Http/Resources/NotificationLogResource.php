<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class NotificationLogResource extends JsonResource
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
            'uid' => $this->uid,
            'user_uid' => $this->user_uid,
            'name' => $this->name,
            'description' => $this->description,
            'icon' => $this->notificationTypes->icon,
            'is_read' => $this->is_read,
            'user' => $this->user ? [
                'full_name' => $this->user->getFullNameAttribute(),
                'uid' => $this->user->uid
            ] : null,
            'created_at' => $this->created_at,
        ];
    }
}
