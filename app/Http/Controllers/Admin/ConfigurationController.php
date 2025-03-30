<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\EducationLevelResource;
use App\Http\Resources\PackagesResource;
use App\Http\Resources\SubjectsResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Repositories\Contracts\EducationLevelRepositoryInterface;
use App\Repositories\Contracts\LocationRepositoryInterface;
use App\Repositories\Contracts\PackageRepositoryInterface;
use App\Repositories\Contracts\SubjectRepositoryInterface;

class ConfigurationController extends Controller
{
    protected $educationLevelRepository;
    protected $subjectRepository;
    protected $locationRepository;
    protected $packageRepository;
    public function __construct(EducationLevelRepositoryInterface $educationLevelRepository, SubjectRepositoryInterface $subjectRepository, LocationRepositoryInterface $locationRepository, PackageRepositoryInterface $packageRepository)
    {
        $this->educationLevelRepository = $educationLevelRepository;
        $this->subjectRepository = $subjectRepository;
        $this->locationRepository = $locationRepository;
        $this->packageRepository = $packageRepository;
    }

    public function getAllConfigurations() {
        try {
            $educationLevels = $this->educationLevelRepository->getAll();
            $subjects = $this->subjectRepository->getAll();
            $locations = $this->locationRepository->getAll();
            $packages = $this->packageRepository->getAll();

            return response()->json([
                'educationLevels' => EducationLevelResource::collection($educationLevels),
                'subjects' => SubjectsResource::collection($subjects),
                'packages' => PackagesResource::collection($packages),
                'provinces' => $locations['province'],
                'districts' => $locations['district'],
                'wards' => $locations['ward'],
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
