<?php

namespace App\Providers;

use App\Repositories\Contracts\ConversationRepositoryInterface;
use App\Repositories\Contracts\DayOfWeekRepositoryInterface;
use App\Repositories\Contracts\EducationLevelRepositoryInterface;
use App\Repositories\Contracts\LanguageRepositoryInterface;
use App\Repositories\Contracts\LevelLanguageRepositoryInterface;
use App\Repositories\Contracts\LocationRepositoryInterface;
use App\Repositories\Contracts\MessageRepositoryInterface;
use App\Repositories\Contracts\NotificationLogRepositoryInterface;
use App\Repositories\Contracts\NotificationTypeRepositoryInterface;
use App\Repositories\Contracts\OnlineLearningPlatformRepositoryInterface;
use App\Repositories\Contracts\PackageRepositoryInterface;
use App\Repositories\Contracts\PaymentMethodRepositoryInterface;
use App\Repositories\Contracts\StudyLocationRepositoryInterface;
use App\Repositories\Contracts\SubjectRepositoryInterface;
use App\Repositories\Contracts\TimeSlotRepositoryInterface;
use App\Repositories\Contracts\UserBookingRepositoryInterface;
use App\Repositories\Contracts\UserRepositoryInterface;
use App\Repositories\Contracts\UserEducationRepositoryInterface;
use App\Repositories\Contracts\UserSubjectRepositoryInterface;
use App\Repositories\Contracts\UserExperienceRepositoryInterface;
use App\Repositories\Contracts\UserLanguageRepositoryInterface;
use App\Repositories\Contracts\UserPackageRepositoryInterface;
use App\Repositories\Contracts\UserStudyLocationRepositoryInterface;
use App\Repositories\Contracts\UserSubjectLevelsRepositoryInterface;
use App\Repositories\Contracts\UserWeeklyTimeSlotRepositoryInterface;
use App\Repositories\Eloquent\ConversationRepository;
use App\Repositories\Eloquent\DayOfWeekRepository;
use App\Repositories\Eloquent\EducationLevelRepository;
use App\Repositories\Eloquent\LanguageRepository;
use App\Repositories\Eloquent\LevelLanguageRepository;
use App\Repositories\Eloquent\LocationRepository;
use App\Repositories\Eloquent\MessageRepository;
use App\Repositories\Eloquent\NotificationLogRepository;
use App\Repositories\Eloquent\NotificationTypeRepository;
use App\Repositories\Eloquent\OnlineLearningPlatformRepository;
use App\Repositories\Eloquent\PackageRepository;
use App\Repositories\Eloquent\PaymentMethodRepository;
use App\Repositories\Eloquent\StudyLocationRepository;
use App\Repositories\Eloquent\SubjectRepository;
use App\Repositories\Eloquent\TimeSlotRepository;
use App\Repositories\Eloquent\UserBookingRepository;
use App\Repositories\Eloquent\UserRepository;
use App\Repositories\Eloquent\UserEducationRepository;
use App\Repositories\Eloquent\UserSubjectRepository;
use App\Repositories\Eloquent\UserExperienceRepository;
use App\Repositories\Eloquent\UserLanguageRepository;
use App\Repositories\Eloquent\UserPackageRepository;
use App\Repositories\Eloquent\UserStudyLocationRepository;
use App\Repositories\Eloquent\UserSubjectLevelsRepository;
use App\Repositories\Eloquent\UserWeeklyTimeSlotRepository;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind(EducationLevelRepositoryInterface::class, EducationLevelRepository::class);
        $this->app->bind(SubjectRepositoryInterface::class, SubjectRepository::class);
        $this->app->bind(LocationRepositoryInterface::class, LocationRepository::class);
        $this->app->bind(PackageRepositoryInterface::class, PackageRepository::class);
        $this->app->bind(UserRepositoryInterface::class, UserRepository::class);
        $this->app->bind(UserEducationRepositoryInterface::class, UserEducationRepository::class);
        $this->app->bind(UserSubjectRepositoryInterface::class, UserSubjectRepository::class);
        $this->app->bind(UserExperienceRepositoryInterface::class, UserExperienceRepository::class);
        $this->app->bind(UserSubjectLevelsRepositoryInterface::class, UserSubjectLevelsRepository::class);
        $this->app->bind(UserPackageRepositoryInterface::class, UserPackageRepository::class);
        $this->app->bind(UserWeeklyTimeSlotRepositoryInterface::class, UserWeeklyTimeSlotRepository::class);
        $this->app->bind(UserLanguageRepositoryInterface::class, UserLanguageRepository::class);
        $this->app->bind(LevelLanguageRepositoryInterface::class, LevelLanguageRepository::class);
        $this->app->bind(LanguageRepositoryInterface::class, LanguageRepository::class);
        $this->app->bind(DayOfWeekRepositoryInterface::class, DayOfWeekRepository::class);
        $this->app->bind(TimeSlotRepositoryInterface::class, TimeSlotRepository::class);
        $this->app->bind(StudyLocationRepositoryInterface::class, StudyLocationRepository::class);
        $this->app->bind(OnlineLearningPlatformRepositoryInterface::class, OnlineLearningPlatformRepository::class);
        $this->app->bind(PaymentMethodRepositoryInterface::class, PaymentMethodRepository::class);
        $this->app->bind(UserBookingRepositoryInterface::class, UserBookingRepository::class);
        $this->app->bind(UserStudyLocationRepositoryInterface::class, UserStudyLocationRepository::class);
        $this->app->bind(NotificationTypeRepositoryInterface::class, NotificationTypeRepository::class);
        $this->app->bind(NotificationLogRepositoryInterface::class, NotificationLogRepository::class);
        $this->app->bind(ConversationRepositoryInterface::class, ConversationRepository::class);
        $this->app->bind(MessageRepositoryInterface::class, MessageRepository::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
