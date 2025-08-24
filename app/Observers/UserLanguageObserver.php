<?php

namespace App\Observers;

use App\Models\UserLanguage;
use App\Services\UserService;

class UserLanguageObserver
{
    public function created(UserLanguage $language)
    {
        $this->updateProfileCompletion($language);
    }

    public function updated(UserLanguage $language)
    {
        $this->updateProfileCompletion($language);
    }

    public function deleted(UserLanguage $language)
    {
        $this->updateProfileCompletion($language);
    }

    protected function updateProfileCompletion(UserLanguage $language)
    {
        $user = $language->user;
        if ($user) {
            app(UserService::class)->calculateAndSaveProfileCompletion($user);
        }
    }
}
