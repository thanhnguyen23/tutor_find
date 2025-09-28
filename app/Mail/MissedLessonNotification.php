<?php

namespace App\Mail;

use App\Models\UserBooking;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class MissedLessonNotification extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public UserBooking $booking;
    public bool $isTutor;

    /**
     * Create a new message instance.
     */
    public function __construct(UserBooking $booking, bool $isTutor)
    {
        $this->booking = $booking;
        $this->isTutor = $isTutor;
    }

    /**
     * Build the message.
     */
    public function build(): self
    {
        if ($this->isTutor) {
            $subject = 'Nhắc nhở và khiển trách: Bạn đã bỏ lỡ một buổi dạy';
            return $this->subject($subject)
                ->view('emails.missed_lesson_tutor')
                ->with([
                    'booking' => $this->booking,
                ]);
        }

        $paymentAmount = optional($this->booking->payment)->amount ?? 0;
        $isFree = $paymentAmount <= 0;
        $subject = 'Xin lỗi: Buổi học của bạn đã bị lỡ';
        return $this->subject($subject)
            ->view('emails.missed_lesson_user')
            ->with([
                'booking' => $this->booking,
                'isFree' => $isFree,
            ]);
    }
}


