<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\EducationLevelResource;
use App\Http\Resources\PackagesResource;
use App\Http\Resources\SubjectsResource;
use App\Http\Resources\TimeSlotResource;
use App\Models\UserBookingComplaint;
use App\Repositories\Contracts\ComplaintTypeRepositoryInterface;
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
use App\Repositories\Contracts\TutorSessionRepositoryInterface;
use App\Repositories\Contracts\UserWeeklyTimeSlotRepositoryInterface;
use App\Repositories\Eloquent\OnlineLearningPlatformRepository;
use Illuminate\Support\Facades\Cache;

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
    protected $complaintTypeRepositoryInterface;
    protected $tutorSessionRepositoryInterface;

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
    NotificationTypeRepositoryInterface $notificationTypeRepositoryInterface,
    ComplaintTypeRepositoryInterface $complaintTypeRepositoryInterface,
    TutorSessionRepositoryInterface $tutorSessionRepositoryInterface)
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
        $this->complaintTypeRepositoryInterface = $complaintTypeRepositoryInterface;
        $this->tutorSessionRepositoryInterface = $tutorSessionRepositoryInterface;
    }

    public function getAllConfigurations()
    {
        try {
            Cache::clear();
            $educationLevels = Cache::remember('config:educationLevels', 86400, function () {
                return $this->educationLevelRepository->getAll();
            });

            $subjects = Cache::remember('config:subjects', 86400, function () {
                return $this->subjectRepository->getAll();
            });

            $locations = Cache::remember('config:locations', 86400, function () {
                return $this->locationRepository->getAll();
            });

            $packages = Cache::remember('config:packages', 86400, function () {
                return $this->packageRepository->getAll();
            });

            $levelLanguages = Cache::remember('config:levelLanguages', 86400, function () {
                return $this->levelLanguageRepository->getAll();
            });

            $languages = Cache::remember('config:languages', 86400, function () {
                return $this->languageRepository->getAll();
            });

            $dayOfWeeks = Cache::remember('config:dayOfWeeks', 86400, function () {
                return $this->dayOfWeekRepository->getAll();
            });

            $timeSlots = Cache::remember('config:timeSlots', 86400, function () {
                return $this->timeSlotRepository->getAll();
            });

            $studyLocation = Cache::remember('config:studyLocation', 86400, function () {
                return $this->studyLocationRepository->getAll();
            });

            $onlineLearningPlatform = Cache::remember('config:onlineLearningPlatform', 86400, function () {
                return $this->OnlineLearningPlatformRepository->getAll();
            });

            $paymentMethods = Cache::remember('config:paymentMethods', 86400, function () {
                return $this->paymentMethodRepositoryInterface->getAll();
            });

            $notificationType = Cache::remember('config:notificationType', 86400, function () {
                return $this->notificationTypeRepositoryInterface->getAll();
            });

            $complaintTypes = Cache::remember('config:complaintTypes', 86400, function () {
                return $this->complaintTypeRepositoryInterface->getAll();
            });

            $listStatusComplaint = Cache::remember('config:listStatusComplaint', 86400, function () {
                return UserBookingComplaint::$LIST_STATUS;
            });

            $tutorSessions = Cache::remember('config:tutorSessions', 86400, function () {
                return $this->tutorSessionRepositoryInterface->getAll();
            });

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
                'timeSlots' => TimeSlotResource::collection($timeSlots),
                'studyLocations' => $studyLocation,
                'onlineLearningPlatform' => $onlineLearningPlatform,
                'paymentMethods' => $paymentMethods,
                'notificationType' => $notificationType,
                'complaintTypes' => $complaintTypes,
                'listStatusComplaint' => $listStatusComplaint,
                'tutorSessions' => $tutorSessions,
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
