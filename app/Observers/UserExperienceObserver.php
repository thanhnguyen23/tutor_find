<?php

namespace App\Observers;

use App\Models\UserExperience;
use App\Services\UserService;

class UserExperienceObserver
{
    public function created(UserExperience $experience)
    {
        $this->updateProfileCompletion($experience);
    }

    public function updated(UserExperience $experience)
    {
        $this->updateProfileCompletion($experience);
    }

    public function deleted(UserExperience $experience)
    {
        $this->updateProfileCompletion($experience);
    }

    protected function updateProfileCompletion(UserExperience $experience)
    {
        $user = $experience->user;
        if ($user) {
            app(UserService::class)->calculateAndSaveProfileCompletion($user);
        }
    }
}
