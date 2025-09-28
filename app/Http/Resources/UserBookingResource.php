<?php

namespace App\Http\Resources;

use App\Enums\UserBookingAction;
use App\Models\UserBooking;
use App\Models\UserBookingComplaint;
use Carbon\Carbon;
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
        $isMissed = ($this->status !== UserBookingAction::Completed->value) && ($this->end_time ? Carbon::parse($this->end_time)->lt(Carbon::now()) : false);
        $time_text = null;

        $status = $this->status;
        $canStart = (bool)($this->can_start ?? false);

        // Tính toán thời gian còn lại từ start_time thay vì sử dụng $timeUntilStart
        $timeUntilStart = null;
        if ($this->start_time) {
            $startTime = Carbon::parse($this->start_time);
            $now = Carbon::now();

            if ($startTime->gt($now)) {
                $diffInMinutes = $now->diffInMinutes($startTime);
                $timeUntilStart = $diffInMinutes;
            }
        }

        if ($status == UserBookingAction::Completed->value) {
            $time_text = 'Buổi học đã kết thúc';
        } elseif ($isMissed) {
            $time_text = 'Buổi học đã lỡ';
        } elseif ($canStart) {
            $time_text = 'Đã đến giờ học';
        } elseif ($timeUntilStart > 0) {
            // Cải thiện hiển thị thời gian
            $time_text = 'Buổi học sẽ diễn ra sau ' . $this->formatTimeUntilStart($timeUntilStart);
        } else {
            $time_text = 'Trạng thái thời gian chưa xác định';
        }

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
            'start_time' => $this->start_time,
            'end_time' => $this->end_time,
            'start_time_text' => $this->start_time ? Carbon::parse($this->start_time)->format('H:i') : null,
            'end_time_text' => $this->end_time ? Carbon::parse($this->end_time)->format('H:i') : null,
            'time_text' => $time_text,
            'time_slot_id' => $this->time_slot_id,
            'tutor_session_id' => $this->tutor_session_id,
            'is_missed' => $isMissed,
            'statusClass' => $this->status,
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
            'time_slot' => $this->timeSlot ? [
                'id' => $this->timeSlot->id,
                'name' => $this->timeSlot->name,
            ] : null,
            'tutor_session' => $this->tutorSession ? [
                'id' => $this->tutorSession->id,
                'name' => $this->tutorSession->name,
                'duration_hours' => $this->tutorSession->duration_hours,
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
                'address' => $this->user->address,
            ] : null,
            'tutor' => $this->tutor ? [
                'uid' => $this->tutor->uid,
                'full_name' => $this->tutor->full_name,
                'avatar' => $this->tutor->avatar,
                'email' => $this->tutor->email,
                'phone' => $this->tutor->phone,
                'address' => $this->tutor->address,
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
            'payment' => $this->payment ? [
                'id' => $this->payment->id,
                'status' => $this->payment->status,
                'statusText' => $this->payment->status(),
                'amount' => $this->payment->amount,
                'paid_at' => $this->payment->paid_at,
                'payment_method' => $this->payment->paymentMethod ? [
                    'id' => $this->payment->paymentMethod->id,
                    'name' => $this->payment->paymentMethod->name,
                    'description' => $this->payment->paymentMethod->description,
                    'code' => $this->payment->paymentMethod->code,
                ] : null,
            ] : null,
            'user_booking_complaint' => $this->userBookingComplaint ? [
                'id' => $this->userBookingComplaint->id,
                'status' => $this->userBookingComplaint->status,
                'statusText' => $this->userBookingComplaint->status(),
                'description' => $this->userBookingComplaint->description,
                'evidence' => $this->userBookingComplaint->evidence,
                'created_at' => $this->userBookingComplaint->created_at,
                'complaint_expected_date' => $this->userBookingComplaint->created_at ? Carbon::parse($this->userBookingComplaint->created_at)->addDays(3)->format('Y-m-d') : null,
                'booking' => $this->userBookingComplaint->booking ? [
                    'id' => $this->userBookingComplaint->booking->id,
                    'request_code' => $this->userBookingComplaint->booking->request_code,
                ] : null,
                'user' => $this->userBookingComplaint->user ? [
                    'uid' => $this->userBookingComplaint->user->uid,
                    'full_name' => $this->userBookingComplaint->user->full_name,
                ] : null,
                'complaint_type' => $this->userBookingComplaint->complaintType ? [
                    'id' => $this->userBookingComplaint->complaintType->id,
                    'name' => $this->userBookingComplaint->complaintType->name,
                ] : null,
            ] : null,
            'class_room' => $this->classRoom
        ];
    }

    // Function helper để format thời gian
    private function formatTimeUntilStart($minutes)
    {
        if ($minutes < 60) {
            // Dưới 1 giờ - hiển thị phút
            return $minutes . ' phút';
        } elseif ($minutes < 1440) { // 1440 phút = 24 giờ
            // Dưới 1 ngày - hiển thị giờ và phút
            $hours = intval($minutes / 60);
            $remainingMinutes = $minutes % 60;

            if ($remainingMinutes == 0) {
                return $hours . ' giờ';
            } else {
                return $hours . ' giờ ' . $remainingMinutes . ' phút';
            }
        } else {
            // Từ 1 ngày trở lên - hiển thị ngày và giờ
            $days = intval($minutes / 1440);
            $remainingMinutes = $minutes % 1440;
            $hours = intval($remainingMinutes / 60);

            $result = $days . ' ngày';
            if ($hours > 0) {
                $result .= ' ' . $hours . ' giờ';
            }

            return $result;
        }
    }

    // Hoặc sử dụng Carbon's diffForHumans cho cách hiển thị tự nhiên hơn
    private function formatTimeUntilStartWithCarbon($startTime)
    {
        $startTime = Carbon::parse($startTime);
        $now = Carbon::now();

        if ($startTime->gt($now)) {
            // Sử dụng Carbon để hiển thị thời gian tự nhiên
            return $now->diffForHumans($startTime, true);
            // Kết quả ví dụ: "2 giờ", "1 ngày", "30 phút"
        }

        return null;
    }
}
