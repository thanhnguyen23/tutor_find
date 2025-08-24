<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\User;
use App\Services\UserService;

class ProfileCompletionReminder extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $completionData;

    public function __construct(User $user)
    {
        $this->user = $user;
        $userService = app(UserService::class);
        $this->completionData = $userService->calculateProfileCompletion($user);
    }

    public function build()
    {
        $remainingEmails = UserService::MAX_REMINDER_EMAILS - $this->user->reminder_emails_sent;

        return $this->view('emails.profile-completion-reminder')
            ->subject('Nhắc nhở: Hoàn thiện hồ sơ gia sư của bạn')
            ->with([
                'userName' => $this->user->full_name,
                'profileUrl' => url('/tutor/profile'),
                'completionPercent' => $this->completionData['percent'],
                'completionDetails' => $this->completionData['details'],
                'remainingEmails' => $remainingEmails
            ]);
    }
}
