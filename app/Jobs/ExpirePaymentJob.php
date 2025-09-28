<?php

namespace App\Jobs;

use App\Enums\PaymentAction;
use App\Events\PaymentStatusUpdated;
use App\Models\Payment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ExpirePaymentJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $paymentId;

    public function __construct(int $paymentId)
    {
        $this->paymentId = $paymentId;
    }

    public function handle(): void
    {
        $payment = Payment::find($this->paymentId);
        if (!$payment) {
            return;
        }

        // Only mark as non_payment if still pending
        if ($payment->status === PaymentAction::Pending->value) {
            $payment->status = PaymentAction::NonPayment->value;
            $payment->save();

            // Broadcast update to frontend
            try {
                event(new PaymentStatusUpdated($payment));
            } catch (\Throwable $e) {
                Log::error('ExpirePaymentJob broadcast failed: ' . $e->getMessage());
            }
        }
    }
}


