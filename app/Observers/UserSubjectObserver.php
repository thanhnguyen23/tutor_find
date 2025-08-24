<?php

namespace App\Observers;

use App\Models\UserSubject;
use App\Services\UserService;

class UserSubjectObserver
{
    public function created(UserSubject $subject)
    {
        $this->updateProfileCompletion($subject);
    }

    public function updated(UserSubject $subject)
    {
        $this->updateProfileCompletion($subject);
    }

    public function deleted(UserSubject $subject)
    {
        $this->updateProfileCompletion($subject);
    }

    protected function updateProfileCompletion(UserSubject $subject)
    {
        $user = $subject->user;
        if ($user) {
            app(UserService::class)->calculateAndSaveProfileCompletion($user);
        }
    }
}
