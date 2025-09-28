<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class ParticipantUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $channelName;
    public $classroom;
    public $participantsCount;

    public function __construct($classroom, $participantsCount)
    {
        $this->classroom = $classroom;
        $this->participantsCount = $participantsCount;
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
        return 'updated';
    }

    public function broadcastWith()
    {
        return [
            'classroom_id' => $this->classroom->id,
            'participants_count' => $this->participantsCount,
        ];
    }
}
