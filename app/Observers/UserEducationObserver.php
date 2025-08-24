<?php

namespace App\Observers;

use App\Models\UserEducation;
use App\Services\UserService;

class UserEducationObserver
{
    public function created(UserEducation $education)
    {
        $this->updateProfileCompletion($education);
    }

    public function updated(UserEducation $education)
    {
        $this->updateProfileCompletion($education);
    }

    public function deleted(UserEducation $education)
    {
        $this->updateProfileCompletion($education);
    }

    protected function updateProfileCompletion(UserEducation $education)
    {
        $user = $education->user;
        if ($user) {
            app(UserService::class)->calculateAndSaveProfileCompletion($user);
        }
    }
}
