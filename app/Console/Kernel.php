<?php

namespace App\Console;

use App\Enums\ClassRoomAction;
use App\Enums\UserBookingAction;
use App\Jobs\ExpireClassRoomJob;
use App\Jobs\ExpireUserBookingJob;
use App\Models\ClassRoom;
use App\Models\UserBooking;
use App\Repositories\Eloquent\UserBookingRepository;
use Carbon\Carbon;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Mail\MissedLessonNotification;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // $schedule->command('inspire')->hourly();

        // Send profile completion reminders every Monday at 9 AM
        $schedule->command('tutors:send-completion-reminders')
            ->weekly()
            ->mondays()
            ->at('09:00');


        $schedule->call(function () {
            try {
                UserBooking::whereIn('status', [
                    UserBookingAction::Pending->value,
                    UserBookingAction::AwaitingClass->value,
                    UserBookingAction::Confirmed->value,
                ])
                ->whereNotNull('end_time')
                ->where('end_time', '<', Carbon::now()->subMinutes(5))
                // ->where('end_time', '>=', $timeLimit)
                ->chunkById(100, function ($bookings) {
                    foreach ($bookings as $booking) {
                        DB::transaction(function () use ($booking) {
                            $booking->update([
                                'status' => UserBookingAction::Missed->value,
                            ]);

                            $classRoom = $booking->classRoom;
                            if (
                                $classRoom &&
                                in_array($classRoom->status, [
                                ClassRoomAction::Pending->value,
                                ClassRoomAction::Scheduled->value,
                            ])) {
                                $classRoom->update([
                                    'status' => ClassRoomAction::Missed->value,
                                ]);
                            }

                            // Gửi thông báo cho học viên
                            // if ($booking->user && $booking->user->email) {
                            //     Mail::to($booking->user->email)->queue(
                            //         new MissedLessonNotification($booking, false)
                            //     );
                            // }

                            // Gửi thông báo khiển trách gia sư
                            if ($booking->tutor && $booking->tutor->email) {
                                Mail::to($booking->tutor->email)->queue(
                                    new MissedLessonNotification($booking, true)
                                );
                            }
                        });
                    }
                });
            } catch (\Throwable $e) {
                Log::error('Daily expiration job failed', [
                    'message' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                ]);
                throw $e;
            }
        })->everyMinute();
        // ->dailyAt('02:30');
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
