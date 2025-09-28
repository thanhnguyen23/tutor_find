<?php

namespace App\Jobs;

use App\Enums\ClassRoomAction;
use App\Enums\UserBookingAction;
use App\Models\UserBooking;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ExpireUserBookingJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $booking;

    public function __construct(UserBooking $booking)
    {
        $this->booking = $booking;
    }

    public function handle()
    {
        try {
            DB::transaction(function () {
                $this->booking->update([
                    'status' => UserBookingAction::Missed->value,
                ]);

                $classRoom = $this->booking->classRoom;
                if (
                    $classRoom &&
                    in_array($classRoom->status, [
                    ClassRoomAction::Pending->value,
                    ClassRoomAction::Scheduled->value,
                ]) && $classRoom->scheduled_end_time && $classRoom->scheduled_end_time < now()->subMinutes(5)) {
                    $classRoom->update([
                        'status' => ClassRoomAction::Expired->value,
                    ]);
                }
            });
        } catch (\Throwable $e) {
            Log::error("Failed to expire Booking ID {$this->booking->id}", [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            throw $e;
        }
    }
}
