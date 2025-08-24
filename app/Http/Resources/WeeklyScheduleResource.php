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
            'time_slot_id_start' => $this->time_slot_id_start,
            'time_slot_id_end' => $this->time_slot_id_end,
            'time_slot_start' => $this->timeSlotStart->name,
            'time_slot_end' => $this->timeSlotEnd->name,
            'day_name' => $this->dayOfWeek->name,
        ];
    }
}
