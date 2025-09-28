<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Services\UserService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class SendProfileCompletionReminders extends Command
{
    protected $signature = 'tutors:send-completion-reminders';
    protected $description = 'Send profile completion reminder emails to tutors';

    protected $userService;

    public function __construct(UserService $userService)
    {
        parent::__construct();
        $this->userService = $userService;
    }

    public function handle()
    {
        $tutors = User::where('role', User::ROLE_TUTOR)
            ->where('profile_completed', false)
            ->where(function ($query) {
                $query->where('reminder_emails_sent', '<', UserService::MAX_REMINDER_EMAILS)
                    ->orWhereNull('reminder_emails_sent');
            })
            ->where(function ($query) {
                $query->whereNull('last_reminder_sent_at')
                    ->orWhere('last_reminder_sent_at', '<=', Carbon::now()->subWeek());
            })
            ->get();

        $count = 0;
        foreach ($tutors as $tutor) {
            if ($this->userService->needsProfileCompletionReminder($tutor)) {
                if ($this->userService->sendProfileCompletionReminder($tutor)) {
                    $count++;
                }
            }
        }

        $this->info("Sent {$count} reminder emails to tutors.");
    }
}
