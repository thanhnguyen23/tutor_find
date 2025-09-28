<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserBookingComplaintResource extends JsonResource
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
            'booking_id' => $this->booking_id,
            'complaint_type_id' => $this->complaint_type_id,
            'uid' => $this->uid,
            'reason' => $this->reason,
            'description' => $this->description,
            'status' => $this->status,
            'evidence' => $this->evidence,
            'created_at' => $this->created_at,
            'complaint_expected_date' => $this->created_at ? Carbon::parse($this->created_at)->addDays(3)->format('Y-m-d') : null,
            'booking' => $this->booking ? [
                'id' => $this->booking->id,
                'request_code' => $this->booking->request_code,
            ] : null,
            'complaint_type' => $this->complaintType ? [
                'id' => $this->complaintType->id,
                'name' => $this->complaintType->name,
            ] : null,
            'user' => $this->user ? [
                'uid' => $this->user->uid,
                'full_name' => $this->user->full_name,
            ] : null,
        ];
    }

    public function complaintType()
    {
        return $this->complaintType ? [
            'id' => $this->complaintType->id,
            'name' => $this->complaintType->name,
        ] : null;
    }
}
