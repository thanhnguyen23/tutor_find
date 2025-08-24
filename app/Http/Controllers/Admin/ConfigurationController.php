<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\EducationLevelResource;
use App\Http\Resources\PackagesResource;
use App\Http\Resources\SubjectsResource;
use App\Repositories\Contracts\DayOfWeekRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Repositories\Contracts\EducationLevelRepositoryInterface;
use App\Repositories\Contracts\LanguageRepositoryInterface;
use App\Repositories\Contracts\LevelLanguageRepositoryInterface;
use App\Repositories\Contracts\LocationRepositoryInterface;
use App\Repositories\Contracts\NotificationTypeRepositoryInterface;
use App\Repositories\Contracts\PackageRepositoryInterface;
use App\Repositories\Contracts\PaymentMethodRepositoryInterface;
use App\Repositories\Contracts\StudyLocationRepositoryInterface;
use App\Repositories\Contracts\SubjectRepositoryInterface;
use App\Repositories\Contracts\TimeSlotRepositoryInterface;
use App\Repositories\Contracts\UserWeeklyTimeSlotRepositoryInterface;
use App\Repositories\Eloquent\OnlineLearningPlatformRepository;

class ConfigurationController extends Controller
{
    protected $educationLevelRepository;
    protected $subjectRepository;
    protected $locationRepository;
    protected $packageRepository;
    protected $levelLanguageRepository;
    protected $languageRepository;
    protected $dayOfWeekRepository;
    protected $timeSlotRepository;
    protected $studyLocationRepository;
    protected $OnlineLearningPlatformRepository;
    protected $paymentMethodRepositoryInterface;
    protected $notificationTypeRepositoryInterface;

    public function __construct(
    EducationLevelRepositoryInterface $educationLevelRepository,
    SubjectRepositoryInterface $subjectRepository,
    LocationRepositoryInterface $locationRepository,
    PackageRepositoryInterface $packageRepository,
    LevelLanguageRepositoryInterface $levelLanguageRepository,
    LanguageRepositoryInterface $languageRepository,
    DayOfWeekRepositoryInterface $dayOfWeekRepository,
    TimeSlotRepositoryInterface $timeSlotRepository,
    StudyLocationRepositoryInterface $studyLocationRepository,
    OnlineLearningPlatformRepository $OnlineLearningPlatformRepository,
    PaymentMethodRepositoryInterface $paymentMethodRepositoryInterface,
    NotificationTypeRepositoryInterface $notificationTypeRepositoryInterface)
    {
        $this->educationLevelRepository = $educationLevelRepository;
        $this->subjectRepository = $subjectRepository;
        $this->locationRepository = $locationRepository;
        $this->packageRepository = $packageRepository;
        $this->levelLanguageRepository = $levelLanguageRepository;
        $this->languageRepository = $languageRepository;
        $this->dayOfWeekRepository = $dayOfWeekRepository;
        $this->timeSlotRepository = $timeSlotRepository;
        $this->studyLocationRepository = $studyLocationRepository;
        $this->OnlineLearningPlatformRepository = $OnlineLearningPlatformRepository;
        $this->paymentMethodRepositoryInterface = $paymentMethodRepositoryInterface;
        $this->notificationTypeRepositoryInterface = $notificationTypeRepositoryInterface;
    }

    public function getAllConfigurations() {
        try {
            $educationLevels = $this->educationLevelRepository->getAll();
            $subjects = $this->subjectRepository->getAll();
            $locations = $this->locationRepository->getAll();
            $packages = $this->packageRepository->getAll();
            $levelLanguages = $this->levelLanguageRepository->getAll();
            $languages = $this->languageRepository->getAll();
            $dayOfWeeks = $this->dayOfWeekRepository->getAll();
            $timeSlots = $this->timeSlotRepository->getAll();
            $studyLocation = $this->studyLocationRepository->getAll();
            $onlineLearningPlatform = $this->OnlineLearningPlatformRepository->getAll();
            $paymentMethods = $this->paymentMethodRepositoryInterface->getAll();
            $notificationType = $this->notificationTypeRepositoryInterface->getAll();

            return response()->json([
                'educationLevels' => EducationLevelResource::collection($educationLevels),
                'subjects' => SubjectsResource::collection($subjects),
                'packages' => PackagesResource::collection($packages),
                'provinces' => $locations['province'],
                'districts' => $locations['district'],
                'wards' => $locations['ward'],
                'levelLanguages' => $levelLanguages,
                'languages' => $languages,
                'dayOfWeeks' => $dayOfWeeks,
                'timeSlots' => $timeSlots,
                'studyLocations' => $studyLocation,
                'onlineLearningPlatform' => $onlineLearningPlatform,
                'paymentMethods' => $paymentMethods,
                'notificationType' => $notificationType,
            ]);
        } catch (\Exception $e) {
            Log::error(__METHOD__, [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json(['message' => 'Đã xảy ra lỗi, vui lòng thử lại sau'], 500);
        }
    }
}
