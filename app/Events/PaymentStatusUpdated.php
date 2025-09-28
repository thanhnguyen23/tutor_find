<?php

namespace App\Events;

use App\Models\Payment;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PaymentStatusUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Payment $payment;

    public function __construct(Payment $payment)
    {
        $this->payment = $payment->fresh();
    }

    public function broadcastOn(): Channel
    {
        // Per-user private channel so only the owner receives updates
        return new PrivateChannel('payments.' . $this->payment->user_uid);
    }

    public function broadcastAs(): string
    {
        return 'payment.status.updated';
    }

    public function broadcastWith(): array
    {
        return [
            'payment_id' => $this->payment->id,
            'booking_id' => $this->payment->booking_id,
            'status' => $this->payment->status,
            'reference_code' => $this->payment->reference_code,
            'paid_at' => optional($this->payment->paid_at)->toIso8601String(),
            'expired_at' => optional($this->payment->expired_at)->toIso8601String(),
        ];
    }
}


