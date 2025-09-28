<?php

// app/Jobs/EndClassroomJob.php
namespace App\Jobs;

use App\Enums\ClassRoomAction;
use App\Enums\UserBookingAction;
use App\Events\ClassRoomEnded;
use App\Models\ClassRoom;
use App\Repositories\Eloquent\ClassRoomRepository;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class EndClassroomJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $classroomId;

    public function __construct($classroomId)
    {
        $this->classroomId = $classroomId;
    }

    public function handle()
    {
        try {
            $classroom = ClassRoom::with(['booking'])->find($this->classroomId);

            if (!$classroom) {
                return;
            }

            // Only end if not already ended
            if ($classroom->status === ClassRoomAction::Ended->value) {
                return;
            }

            DB::beginTransaction();

            try {
                $now = now()->timezone('Asia/Ho_Chi_Minh');
                $actualDuration = $this->calculateActualDuration($classroom, $now);

                // Update classroom status to ended
                $classroom->update([
                    'status' => ClassRoomAction::Ended->value,
                    'actual_end_time' => $now,
                    'actual_duration' => $actualDuration,
                    'participants_count' => $classroom->participants_count ?? 0,
                ]);

                // Update associated booking status to completed
                if ($classroom->booking) {
                    $this->updateBookingStatus($classroom->booking);
                }

                DB::commit();

                // Broadcast classroom ended event
                broadcast(new ClassRoomEnded($classroom));

            } catch (\Throwable $e) {
                DB::rollBack();
                throw $e;
            }

        } catch (\Throwable $e) {
            Log::error('EndClassroomJob failed', [
                'classroom_id' => $this->classroomId,
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            // Re-throw to trigger retry mechanism
            throw $e;
        }
    }

    private function calculateActualDuration($classroom, $endTime)
    {
        if (!$classroom->actual_start_time) {
            return 0;
        }

        $startTime = Carbon::parse($classroom->actual_start_time);
        return $startTime->diffInMinutes($endTime);
    }

    private function updateBookingStatus($booking): void
    {
        if (!$booking) {
            return;
        }

        // Only update if booking is in awaiting_class status
        if ($booking->status === UserBookingAction::AwaitingClass->value) {
            $booking->update([
                'status' => UserBookingAction::Completed->value
            ]);

        } else {
            Log::warning('Booking status not updated - invalid current status', [
                'booking_id' => $booking->id,
                'current_status' => $booking->status,
                'classroom_id' => $this->classroomId
            ]);
        }
    }
}

