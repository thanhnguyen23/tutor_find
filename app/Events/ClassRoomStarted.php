<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class ClassRoomStarted implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $classroom;

    public function __construct($classroom)
    {
        $this->classroom = $classroom;
    }

    public function broadcastOn()
    {
        // Notify both tutor and student via their UID channels used by app
        return [
            new PrivateChannel('classroom.' . ($this->classroom->booking?->tutor?->uid)),
            new PrivateChannel('classroom.' . ($this->classroom->booking?->user?->uid)),
        ];
    }

    public function broadcastAs()
    {
        return 'started';
    }

    public function broadcastWith()
    {
        return [
            'classroom_id' => $this->classroom->id,
        ];
    }
}
