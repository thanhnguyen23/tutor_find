<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\URL;
use App\Models\UserEducation;
use App\Models\UserExperience;
use App\Models\UserSubject;
use App\Models\UserWeeklyTimeSlot;
use App\Models\UserStudyLocation;
use App\Models\UserLanguage;
use App\Observers\UserEducationObserver;
use App\Observers\UserExperienceObserver;
use App\Observers\UserSubjectObserver;
use App\Observers\UserWeeklyTimeSlotObserver;
use App\Observers\UserStudyLocationObserver;
use App\Observers\UserLanguageObserver;
use Carbon\Carbon;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Carbon::setLocale('vi');
        date_default_timezone_set('Asia/Ho_Chi_Minh');

        if (app()->environment('local')) {
            URL::forceScheme('http');
        }

        UserSubject::observe(UserSubjectObserver::class);
        UserEducation::observe(UserEducationObserver::class);
        UserExperience::observe(UserExperienceObserver::class);
        UserWeeklyTimeSlot::observe(UserWeeklyTimeSlotObserver::class);
        UserStudyLocation::observe(UserStudyLocationObserver::class);
        UserLanguage::observe(UserLanguageObserver::class);

        // Handle user online/offline status
        if (Auth::guard('sanctum')->check()) {
            Redis::set('user-is-online-' . Auth::guard('sanctum')->user()->id, true);
            Redis::expire('user-is-online-' . Auth::guard('sanctum')->user()->id, 300);
        }

        // Đăng ký thêm các observer khác nếu cần
    }
}
