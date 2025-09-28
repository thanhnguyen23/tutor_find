<?php

namespace App\Observers;

use App\Models\UserStudyLocation;
use App\Services\UserService;
use Illuminate\Support\Facades\Log;

class UserStudyLocationObserver
{
    public function created(UserStudyLocation $studyLocation)
    {
        $this->updateProfileCompletion($studyLocation);
    }

    public function updated(UserStudyLocation $studyLocation)
    {
        $this->updateProfileCompletion($studyLocation);
    }

    public function deleted(UserStudyLocation $studyLocation)
    {
        $this->updateProfileCompletion($studyLocation);
    }

    protected function updateProfileCompletion(UserStudyLocation $studyLocation)
    {
        Log::info($studyLocation);
        $user = $studyLocation->user;
        if ($user) {
            app(UserService::class)->calculateAndSaveProfileCompletion($user);
        }
    }
}
