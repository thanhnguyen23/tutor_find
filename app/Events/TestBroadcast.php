<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class TestBroadcast implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $message = 'Test message from Pusher!';

    public function __construct()
    {
        //
    }

    public function broadcastOn()
    {
        return new PresenceChannel('users');  // Hoặc PrivateChannel nếu không dùng presence
    }

    public function broadcastWith()
    {
        return ['message' => $this->message];
    }
}
