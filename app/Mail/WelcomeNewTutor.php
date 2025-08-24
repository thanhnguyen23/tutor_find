<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\User;

class WelcomeNewTutor extends Mailable
{
    use Queueable, SerializesModels;

    public $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    public function build()
    {
        return $this->view('emails.welcome-tutor')
            ->subject('Chào mừng bạn đến với EduTutor - Hoàn thiện hồ sơ ngay!')
            ->with([
                'userName' => $this->user->full_name,
                'profileUrl' => url('/tutor/profile')
            ]);
    }
}
