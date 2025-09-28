<?php

namespace App\Http\Resources;

use App\Enums\ClassRoomAction;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ClassRoomResource extends JsonResource
{
    public function toArray(Request $request): array
    {

        return [
            'id' => $this->id,
            'topic' => $this->topic,
            'agenda' => $this->agenda,
            'status' => $this->status,
            'scheduled_start_time' => $this->scheduled_start_time,
            'scheduled_end_time' => $this->scheduled_end_time,
            'scheduled_duration' => $this->scheduled_duration,
            'actual_start_time' => $this->actual_start_time,
            'actual_end_time' => $this->actual_end_time,
            'actual_duration' => $this->actual_duration,
            'participants_count' => $this->participants_count,
            'max_participants' => $this->max_participants,
            'created_at' => $this->created_at,
            'status_text' => $this->status(),
            'time_info' => $this->buildTimeInfo(),

            // Thông tin booking cho ClassRoom.vue
            'booking' => $this->booking ? [
                'id' => $this->booking->id,
                'request_code' => $this->booking->request_code,
                'tutor' => $this->booking->tutor ? [
                    'uid' => $this->booking->tutor->uid ?? null,
                    'full_name' => $this->booking->tutor->full_name ?? ($this->booking->tutor->name ?? null),
                ] : null,
                'user' => $this->booking->user ? [
                    'uid' => $this->booking->user->uid ?? null,
                    'full_name' => $this->booking->user->full_name ?? ($this->booking->user->name ?? null),
                ] : null,
                'subject' => $this->booking->subject ? [
                    'id' => $this->booking->subject->id,
                    'name' => $this->booking->subject->name,
                ] : null,
                'education_level' => $this->booking->educationLevel ? [
                    'id' => $this->booking->educationLevel->id,
                    'name' => $this->booking->educationLevel->name,
                ] : null,
            ] : null,
        ];
    }

    private function buildTimeInfo(): array
    {
        $now = Carbon::now();
        $scheduledStart = $this->scheduled_start_time ? Carbon::parse($this->scheduled_start_time) : null;
        $scheduledEnd = $this->scheduled_end_time ? Carbon::parse($this->scheduled_end_time) : null;

        $canStart = $this->canStart($scheduledStart, $scheduledEnd);
        $timeUntilStart = $scheduledStart ? $now->diffInMinutes($scheduledStart, false) : null;
        $timeUntilEnd = $scheduledEnd ? $now->diffInMinutes($scheduledEnd, false) : null;
        $isMissed = $scheduledEnd ? $now->gt($scheduledEnd) : false;

        $timeInfo = [
            'can_start' => $canStart,
            'is_missed' => $isMissed,
            'current_time' => $now->format('Y-m-d H:i:s'),
            'scheduled_start_time' => $scheduledStart?->format('Y-m-d H:i:s'),
            'scheduled_end_time' => $scheduledEnd?->format('Y-m-d H:i:s'),
            'time_until_start_minutes' => $timeUntilStart,
            'time_until_end_minutes' => $timeUntilEnd,
            'is_before_start' => is_int($timeUntilStart) ? $timeUntilStart > 0 : null,
            'is_after_end' => is_int($timeUntilEnd) ? $timeUntilEnd < 0 : null,
            'status' => $this->status,
        ];

        $timeInfo['time_status_text'] = $this->getTimeStatusText($timeInfo);

        return $timeInfo;
    }

    private function canStart(?Carbon $scheduledStart, ?Carbon $scheduledEnd): bool
    {
        if (!$scheduledStart || !$scheduledEnd) {
            return false;
        }

        if ($this->status === ClassRoomAction::Ended->value) {
            return false;
        }

        $now = Carbon::now();

        if ($now->gt($scheduledEnd)) {
            return false;
        }

        $allowedStartTime = $scheduledStart->copy()->subMinutes(5);
        return $now->gte($allowedStartTime) && $now->lte($scheduledEnd);
    }

    private function getTimeStatusText(array $timeInfo): string
    {
        $status = $timeInfo['status'] ?? null;
        $canStart = (bool)($timeInfo['can_start'] ?? false);
        $timeUntilStart = (int)($timeInfo['time_until_start_minutes'] ?? 0);
        $isMissed = (bool)($timeInfo['is_missed'] ?? false);

        if ($status === ClassRoomAction::Ended->value) {
            return 'Buổi học đã kết thúc';
        } elseif ($isMissed) {
            return 'Buổi học đã lỡ';
        } elseif ($canStart) {
            return 'Đã đến giờ học, có thể bắt đầu';
        } elseif ($timeUntilStart > 0) {
            return 'Bắt đầu sau ' . $this->formatTimeUntilStart($timeUntilStart);
        }
        return 'Trạng thái thời gian chưa xác định';
    }

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
}
