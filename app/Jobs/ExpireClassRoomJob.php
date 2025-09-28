<?php

namespace App\Jobs;

use App\Enums\ClassRoomAction;
use App\Enums\UserBookingAction;
use App\Models\ClassRoom;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ExpireClassRoomJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $classRoom;

    public function __construct(ClassRoom $classRoom)
    {
        $this->classRoom = $classRoom;
    }

    public function handle()
    {
        try {
            DB::transaction(function () {
                $this->classRoom->update([
                    'status' => ClassRoomAction::Missed->value,
                ]);

                $booking = $this->classRoom->booking;
                if ($booking && in_array($booking->status, [
                    UserBookingAction::Pending->value,
                    UserBookingAction::AwaitingClass->value,
                    UserBookingAction::Confirmed->value,
                ]) && $booking->end_time && $booking->end_time < now()->subMinutes(30)) {
                    $booking->update([
                        'status' => UserBookingAction::Missed->value,
                        'updated_at' => now(),
                    ]);
                }
            });
        } catch (\Throwable $e) {
            Log::error("Failed to expire ClassRoom ID {$this->classRoom->id}", [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            throw $e;
        }
    }
}
