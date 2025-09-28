<?php

namespace App\Listeners;

use App\Jobs\EndClassroomJob;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class HandleChannelVacated
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(object $event): void
    {
        // Ví dụ channel: presence-classroom.123
        if (str_starts_with($event->channelName, 'presence-classroom.')) {
            $classroomId = str_replace('presence-classroom.', '', $event->channelName);

            // Đặt lịch end sau 5 phút
            EndClassroomJob::dispatch($classroomId)->delay(now()->addMinutes(5));
        }
    }
}
