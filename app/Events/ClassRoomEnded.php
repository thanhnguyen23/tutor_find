<?php

namespace App\Events;

use App\Models\ClassRoom;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ClassRoomEnded implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $classroom;

    public function __construct(ClassRoom $classroom)
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
        return 'ended';
    }

    public function broadcastWith()
    {
        return [
            'classroom_id' => $this->classroom->id,
        ];
    }
}


