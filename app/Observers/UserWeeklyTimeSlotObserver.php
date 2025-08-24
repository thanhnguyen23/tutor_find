<?php

namespace App\Observers;

use App\Models\UserWeeklyTimeSlot;
use App\Services\UserService;

class UserWeeklyTimeSlotObserver
{
    public function created(UserWeeklyTimeSlot $slot)
    {
        $this->updateProfileCompletion($slot);
    }

    public function updated(UserWeeklyTimeSlot $slot)
    {
        $this->updateProfileCompletion($slot);
    }

    public function deleted(UserWeeklyTimeSlot $slot)
    {
        $this->updateProfileCompletion($slot);
    }

    protected function updateProfileCompletion(UserWeeklyTimeSlot $slot)
    {
        $user = $slot->user;
        if ($user) {
            app(UserService::class)->calculateAndSaveProfileCompletion($user);
        }
    }
}
