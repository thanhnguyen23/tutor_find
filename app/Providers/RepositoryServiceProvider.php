<?php

namespace App\Providers;

use App\Repositories\Contracts\EducationLevelRepositoryInterface;
use App\Repositories\Contracts\LocationRepositoryInterface;
use App\Repositories\Contracts\PackageRepositoryInterface;
use App\Repositories\Contracts\SubjectRepositoryInterface;
use App\Repositories\Contracts\UserRepositoryInterface;
use App\Repositories\Contracts\UserEducationRepositoryInterface;
use App\Repositories\Contracts\UserSubjectRepositoryInterface;
use App\Repositories\Contracts\UserExperienceRepositoryInterface;
use App\Repositories\Eloquent\EducationLevelRepository;
use App\Repositories\Eloquent\LocationRepository;
use App\Repositories\Eloquent\PackageRepository;
use App\Repositories\Eloquent\SubjectRepository;
use App\Repositories\Eloquent\UserRepository;
use App\Repositories\Eloquent\UserEducationRepository;
use App\Repositories\Eloquent\UserSubjectRepository;
use App\Repositories\Eloquent\UserExperienceRepository;
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
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
