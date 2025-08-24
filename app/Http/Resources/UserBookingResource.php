<?php

namespace App\Http\Resources;

use App\Models\UserBooking;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserBookingResource extends JsonResource
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
            'request_code' => $this->request_code,
            'date' => $this->date,
            'location' => $this->location,
            'note' => $this->note,
            'hourly_rate' => $this->hourly_rate,
            'duration' => $this->duration,
            'total_price' => $this->total_price,
            'status' => $this->status,
            'statusText' => $this->status(),
            'study_location' => $this->studyLocation,
            'is_package' => $this->is_package,
            'subject' => $this->subject ? [
                'id' => $this->subject->id,
                'name' => $this->subject->name,
            ] : null,
            'education_level' => $this->educationLevel ? [
                'id' => $this->educationLevel->id,
                'name' => $this->educationLevel->name,
            ] : null,
            'time_slot_start' => $this->timeSlotStart ? [
                'id' => $this->timeSlotStart->id,
                'name' => $this->timeSlotStart->name,
            ] : null,
            'time_slot_end' => $this->timeSlotEnd ? [
                'id' => $this->timeSlotEnd->id,
                'name' => $this->timeSlotEnd->name,
            ] : null,
            'package' => $this->package ? [
                'id' => $this->package->id,
                'name' => $this->package->name,
                'price' => $this->package->price,
                'duration' => $this->package->duration,
                'number_of_lessons' => $this->package->number_of_lessons ?? null,
            ] : null,
            'user' => $this->user ? [
                'uid' => $this->user->uid,
                'full_name' => $this->user->full_name,
                'avatar' => $this->user->avatar,
                'email' => $this->user->email,
                'phone' => $this->user->phone,
            ] : null,
            'tutor' => $this->tutor ? [
                'uid' => $this->tutor->uid,
                'full_name' => $this->tutor->full_name,
                'avatar' => $this->tutor->avatar,
                'email' => $this->tutor->email,
                'phone' => $this->tutor->phone,
            ] : null,
            'user_booking_logs' => $this->userBookingLogs ? $this->userBookingLogs->map(function ($log) {
                return [
                    'id' => $log->id,
                    'status' => $log->status,
                    'statusText' => $log->status(),
                    'note' => $log->note,
                    'created_at' => $log->created_at,
                    'user' => $log->user ? [
                        'uid' => $log->user->uid,
                        'full_name' => $log->user->full_name,
                    ] : null,
                ];
            }) : null,
        ];
    }
}
