<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class WeeklyScheduleResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'day_of_week_id' => $this->day_of_week_id,
            'time_slot_id' => $this->time_slot_id,
            'time_slot_name' => optional($this->timeSlot)->name,
            'day_name' => $this->dayOfWeek->name,
        ];
    }
}
