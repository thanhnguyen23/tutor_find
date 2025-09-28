<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Broadcasting\PrivateChannel;

class WebRtcSignal implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public string $classroomId;
    public string $from;
    public string $to;
    public array $signal;

    public function __construct(string $classroomId, string $from, string $to, array $signal)
    {
        $this->classroomId = $classroomId;
        $this->from = $from;
        $this->to = $to;
        $this->signal = $signal;
    }

    public function broadcastOn()
    {
        try {
            return new PrivateChannel('private-webrtc.' . $this->to); // Channel dựa trên UID đích
        } catch (\Exception $e) {
            throw $e;
        }
    }

    public function broadcastAs()
    {
        try {
            return 'webrtc.signal';
        } catch (\Exception $e) {
            throw $e;
        }
    }

    public function broadcastWith()
    {
        try {
            return ['classroomId' => $this->classroomId, 'from' => $this->from, 'to' => $this->to, 'signal' => $this->signal];
        } catch (\Exception $e) {
            throw $e;
        }
    }
}


