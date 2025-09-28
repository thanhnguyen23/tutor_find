<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\TimeSlotResource;
use App\Http\Resources\UserProfileResource;
use App\Repositories\Contracts\UserRepositoryInterface;
use App\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use App\Models\UserSubjectLevels;
use App\Repositories\Contracts\UserEducationRepositoryInterface;
use App\Repositories\Contracts\UserExperienceRepositoryInterface;
use App\Repositories\Contracts\UserSubjectLevelsRepositoryInterface;
use App\Repositories\Contracts\UserSubjectRepositoryInterface;
use App\Repositories\Contracts\UserPackageRepositoryInterface;
use App\Repositories\Contracts\UserWeeklyTimeSlotRepositoryInterface;
use Illuminate\Validation\ValidationException;
use App\Repositories\Contracts\UserLanguageRepositoryInterface;
use App\Models\DayOfWeek;
use App\Models\TimeSlot;
use App\Http\Resources\UserEducationResource;
use App\Http\Resources\UserExperienceResource;
use App\Http\Resources\UserSubjectResource;
use App\Http\Resources\UserPackageResource;
use App\Http\Resources\WeeklyScheduleResource;
use App\Http\Resources\LanguageResource;
use App\Repositories\Contracts\UserStudyLocationRepositoryInterface;

class UserProfileController extends Controller
{
    protected $userRepository;
    protected $userSubjectRepository;
    protected $userSubjectLevelsRepository;
    protected $userExperienceRepository;
    protected $userEducationRepository;
    protected $userPackageRepository;
    protected $userWeeklyTimeSlotRepository;
    protected $userLanguageRepository;
    protected $userService;
    protected $userStudyLocationRepository;

    public function __construct(
        UserRepositoryInterface $userRepository,
        UserSubjectRepositoryInterface $userSubjectRepository,
        UserSubjectLevelsRepositoryInterface $userSubjectLevelsRepository,
        UserExperienceRepositoryInterface $userExperienceRepository,
        UserEducationRepositoryInterface $userEducationRepository,
        UserPackageRepositoryInterface $userPackageRepository,
        UserWeeklyTimeSlotRepositoryInterface $userWeeklyTimeSlotRepository,
        UserLanguageRepositoryInterface $userLanguageRepository,
        UserService $userService,
        UserStudyLocationRepositoryInterface $userStudyLocationRepository)
    {
        $this->userRepository = $userRepository;
        $this->userSubjectRepository = $userSubjectRepository;
        $this->userSubjectLevelsRepository = $userSubjectLevelsRepository;
        $this->userExperienceRepository = $userExperienceRepository;
        $this->userEducationRepository = $userEducationRepository;
        $this->userPackageRepository = $userPackageRepository;
        $this->userWeeklyTimeSlotRepository = $userWeeklyTimeSlotRepository;
        $this->userLanguageRepository = $userLanguageRepository;
        $this->userService = $userService;
        $this->userStudyLocationRepository = $userStudyLocationRepository;
    }

    public function getUserDataDetail()
    {
        try {
            $user = auth()->user();
            $userData = $this->userRepository->getUserDataDetail($user->id);

            return response()->json([
                'data' => new UserProfileResource($userData)
            ]);
        } catch (\Exception $e) {
            Log::error(__METHOD__, [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json(['message' => 'Đã xảy ra lỗi, vui lòng thử lại sau'], 500);
        }
    }

    public function updateUserData(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'first_name' => 'required|string|max:255',
                'last_name' => 'required|string|max:255',
                'address' => 'nullable|string|max:255',
                'phone' => 'nullable|string|max:20',
                'about_you' => 'nullable|string'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $user = auth()->user();
            $data = $request->only(['first_name', 'last_name', 'address', 'phone', 'about_you']);
            $updatedUser = $this->userRepository->update($user->id, $data);

            return response()->json([
                'message' => 'Cập nhật thông tin thành công',
                'data' => new UserProfileResource($updatedUser)
            ]);
        } catch (\Exception $e) {
            Log::error(__METHOD__, [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json(['message' => 'Đã xảy ra lỗi, vui lòng thử lại sau'], 500);
        }
    }

    public function updateProfileInfo(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'first_name' => 'nullable|string|max:255',
                'last_name' => 'nullable|string|max:255',
                'phone' => 'nullable|string|max:20',
                'email' => 'nullable|email|max:255',
                'address' => 'nullable|string|max:255',
                'about_you' => 'nullable|string|max:255',
                'cccd' => 'nullable|string|max:20',
                'sex' => 'nullable|integer|in:1,2',
                'is_free_study' => 'nullable|integer|max:1',
                'tutor_session_id' => 'nullable|integer|exists:tutor_sessions,id',
                'provinces_id' => 'nullable|integer',
                'districts_id' => 'nullable|integer',
                'wards_id' => 'nullable|integer',
            ]);

            $user = auth()->user();
            $data = $request->only([
                'first_name', 'last_name', 'phone', 'email', 'address', 'about_you', 'referral_link',
                'cccd', 'sex', 'is_free_study', 'tutor_session_id', 'provinces_id', 'districts_id', 'wards_id'
            ]);

            $updatedUser = $this->userRepository->update($user->id, $data);

            return response()->json([
                'message' => 'Cập nhật thông tin hồ sơ thành công',
                'data' => new UserProfileResource($updatedUser)
            ]);
        } catch (\Exception $e) {
            Log::error(__METHOD__, [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json(['message' => 'Đã xảy ra lỗi, vui lòng thử lại sau'], 500);
        }
    }

    public function updateEducation(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'id' => 'required|integer',
                'school_name' => 'required|string|max:255',
                'major' => 'required|string|max:255',
                'time' => 'required|string|max:255',
                'description' => 'nullable|string',
                'certificate' => 'nullable|image|mimes:jpeg,png,jpg|max:10240' // 10MB max
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $user = auth()->user();
            $data = $request->only(['school_name', 'major', 'time', 'description']);
            $data['user_id'] = $user->id;

            // Handle certificate file upload
            if ($request->hasFile('certificate')) {
                $certificatePath = $request->file('certificate')->store('certificates', 'public');
                $data['certificate'] = $certificatePath;
            }

            $education = $this->userEducationRepository->update($data, $request->id);

            Log::info($education);

            return response()->json([
                'message' => 'Cập nhật học vấn thành công',
                'data' => new UserEducationResource($education)
            ]);
        } catch (\Exception $e) {
            Log::error(__METHOD__, [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json(['message' => 'Đã xảy ra lỗi, vui lòng thử lại sau'], 500);
        }
    }

    public function updateExperience(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'position' => 'required|string|max:255',
                'time' => 'required|string|max:255',
                'description' => 'nullable|string'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $user = auth()->user();
            $data = $request->only(['name', 'position', 'time', 'description']);
            $data['user_id'] = $user->id;

            $experience = $this->userExperienceRepository->update($data, $request->id);

            return response()->json([
                'message' => 'Cập nhật kinh nghiệm thành công',
                'data' => new UserExperienceResource($experience)
            ]);
        } catch (\Exception $e) {
            Log::error(__METHOD__, [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json(['message' => 'Đã xảy ra lỗi, vui lòng thử lại sau'], 500);
        }
    }

    public function updateSubject(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'user_subject_id' => 'required|exists:user_subjects,id',
                'subject_id' => 'required|exists:subjects,id',
                'years_of_experience' => 'required|numeric|min:0',
                'levels' => 'required|array',
                'levels.*.level_id' => 'required|exists:education_levels,id',
                'levels.*.price' => 'required|numeric|min:0'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $user = auth()->user();
            $data = $request->only(['subject_id', 'years_of_experience']);
            $data['user_id'] = $user->id;

            // Kiểm tra trùng môn học (trừ chính bản ghi đang sửa)
            if ($this->userSubjectRepository->existsByUserAndSubject($user->id, $request->subject_id, $request->user_subject_id)) {
                return response()->json([
                    'message' => 'Môn học này đã tồn tại!'
                ], 422);
            }

            DB::beginTransaction();

            $subject = $this->userSubjectRepository->update($data, $request->user_subject_id);
            $this->userSubjectLevelsRepository->deleteAll($request->user_subject_id);

            foreach ($request->levels as $level) {
                $this->userSubjectLevelsRepository->create([
                    'user_subject_id' => $request->user_subject_id,
                    'education_level_id' => $level['level_id'],
                    'price' => $level['price']
                ]);
            }

            DB::commit();

            return response()->json([
                'message' => 'Cập nhật môn học thành công',
                'data' => new UserSubjectResource($subject)
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error(__METHOD__, [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json(['message' => 'Đã xảy ra lỗi, vui lòng thử lại sau'], 500);
        }
    }

    public function addSubject(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'subject_id' => 'required|exists:subjects,id',
                'years_of_experience' => 'required|numeric|min:0',
                'levels' => 'required|array',
                'levels.*.level_id' => 'required|exists:education_levels,id',
                'levels.*.price' => 'required|numeric|min:0'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $user = auth()->user();
            $data = $request->only(['subject_id', 'years_of_experience']);
            $data['user_id'] = $user->id;

            // Kiểm tra trùng môn học
            if ($this->userSubjectRepository->existsByUserAndSubject($user->id, $request->subject_id)) {
                return response()->json([
                    'message' => 'Môn học này đã tồn tại!'
                ], 422);
            }

            DB::beginTransaction();

            $subject = $this->userSubjectRepository->create($data);

            foreach ($request->levels as $level) {
                $this->userSubjectLevelsRepository->create([
                    'user_subject_id' => $subject->id,
                    'education_level_id' => $level['level_id'],
                    'price' => $level['price']
                ]);
            }

            DB::commit();

            return response()->json([
                'message' => 'Thêm môn học thành công',
                'data' => new UserSubjectResource($subject)
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error(__METHOD__, [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json(['message' => 'Đã xảy ra lỗi, vui lòng thử lại sau'], 500);
        }
    }

    public function addEducation(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'school_name' => 'required|string|max:255',
                'major' => 'required|string|max:255',
                'time' => 'required|string|max:255',
                'description' => 'nullable|string',
                'certificate' => 'nullable|image|mimes:jpeg,png,jpg|max:10240' // 10MB max
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $user = auth()->user();
            $data = $request->only(['school_name', 'major', 'time', 'description']);
            $data['user_id'] = $user->id;

            // Handle certificate file upload
            if ($request->hasFile('certificate')) {
                $certificatePath = $request->file('certificate')->store('certificates', 'public');
                $data['certificate'] = $certificatePath;
            }

            $education = $this->userEducationRepository->create($data);

            return response()->json([
                'message' => 'Thêm học vấn thành công',
                'data' => new UserEducationResource($education)
            ]);
        } catch (\Exception $e) {
            Log::error(__METHOD__, [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json(['message' => 'Đã xảy ra lỗi, vui lòng thử lại sau'], 500);
        }
    }

    public function addExperience(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'position' => 'required|string|max:255',
                'time' => 'required|string|max:255',
                'description' => 'nullable|string'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $user = auth()->user();
            $data = $request->only(['name', 'position', 'time', 'description']);
            $data['user_id'] = $user->id;

            $experience = $this->userExperienceRepository->create($data);

            return response()->json([
                'message' => 'Thêm kinh nghiệm thành công',
                'data' => new UserExperienceResource($experience)
            ]);
        } catch (\Exception $e) {
            Log::error(__METHOD__, [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json(['message' => 'Đã xảy ra lỗi, vui lòng thử lại sau'], 500);
        }
    }

    public function createUserPackage(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'subject_id' => 'required|exists:subjects,id',
                'level_id' => 'required|exists:education_levels,id',
                'name' => 'required|string|max:255',
                'description' => 'nullable|string',
                'number_of_lessons' => 'required|integer|min:1',
                'duration' => 'required|integer|min:1',
                'price' => 'required|numeric|min:0',
                'features' => 'required|array',
                'features.*' => 'required|string',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $user = auth()->user();
            $data = $request->all();
            $data['user_id'] = $user->id;

            $package = $this->userPackageRepository->createWithFeatures($data, $request->features);

            return response()->json([
                'message' => 'Gói dịch vụ được tạo thành công',
                'data' => new UserPackageResource($package)
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error(__METHOD__, [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json(['message' => 'Đã xảy ra lỗi, vui lòng thử lại sau'], 500);
        }
    }

    public function updateUserPackage(Request $request, int $id)
    {
        try {
            $validator = Validator::make($request->all(), [
                'subject_id' => 'required|exists:subjects,id',
                'level_id' => 'required|exists:education_levels,id',
                'name' => 'required|string|max:255',
                'description' => 'nullable|string',
                'number_of_lessons' => 'required|integer|min:1',
                'duration' => 'required|integer|min:1',
                'price' => 'required|numeric|min:0',
                'features' => 'required|array',
                'features.*' => 'required|string',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $user = auth()->user();
            $package = $this->userPackageRepository->find($id);

            if ($package->user_id !== $user->id) {
                return response()->json(['message' => 'Không có quyền truy cập'], 403);
            }

            $data = $request->all();
            $package = $this->userPackageRepository->updateWithFeatures($id, $data, $request->features);

            return response()->json([
                'message' => 'Gói dịch vụ được cập nhật thành công',
                'data' => new UserPackageResource($package)
            ]);
        } catch (\Exception $e) {
            Log::error(__METHOD__, [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json(['message' => 'Đã xảy ra lỗi, vui lòng thử lại sau'], 500);
        }
    }

    public function deleteUserPackage(int $id)
    {
        try {
            $user = auth()->user();
            $package = $this->userPackageRepository->find($id);

            if ($package->user_id !== $user->id) {
                return response()->json(['message' => 'Không có quyền truy cập'], 403);
            }

            $this->userPackageRepository->delete($id);

            return response()->json([
                'message' => 'Gói dịch vụ được xóa thành công',
            ]);
        } catch (\Exception $e) {
            Log::error(__METHOD__, [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json(['message' => 'Đã xảy ra lỗi, vui lòng thử lại sau'], 500);
        }
    }

    public function deleteEducation(int $id)
    {
        try {
            $user = auth()->user();
            $education = $this->userEducationRepository->find($id);

            if ($education->user_id !== $user->id) {
                return response()->json(['message' => 'Không có quyền truy cập'], 403);
            }

            $this->userEducationRepository->delete($id);

            return response()->json([
                'message' => 'Gói dịch vụ được xóa thành công',
            ]);
        } catch (\Exception $e) {
            Log::error(__METHOD__, [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json(['message' => 'Đã xảy ra lỗi, vui lòng thử lại sau'], 500);
        }
    }

    public function deleteExperience(int $id)
    {
        try {
            $user = auth()->user();
            $experience = $this->userExperienceRepository->find($id);

            if ($experience->user_id !== $user->id) {
                return response()->json(['message' => 'Không có quyền truy cập'], 403);
            }

            $this->userExperienceRepository->delete($id);

            return response()->json([
                'message' => 'Gói dịch vụ được xóa thành công',
            ]);
        } catch (\Exception $e) {
            Log::error(__METHOD__, [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json(['message' => 'Đã xảy ra lỗi, vui lòng thử lại sau'], 500);
        }
    }

    public function deleteSubject(int $id)
    {
        try {
            $user = auth()->user();
            $subject = $this->userSubjectRepository->find($id);

            if ($subject->user_id !== $user->id) {
                return response()->json(['message' => 'Không có quyền truy cập'], 403);
            }

            $this->userSubjectRepository->delete($id);

            return response()->json([
                'message' => 'Gói dịch vụ được xóa thành công',
            ]);
        } catch (\Exception $e) {
            Log::error(__METHOD__, [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json(['message' => 'Đã xảy ra lỗi, vui lòng thử lại sau'], 500);
        }
    }

    /**
     * Get the user's weekly schedule
     */
    public function getWeeklySchedule(Request $request)
    {
        try {
            $userId = auth()->id();
            $timeSlots = $this->userWeeklyTimeSlotRepository->getTimeSlotsByUser($userId);
            return response()->json([
                'time_slots' => $timeSlots,
            ]);
        } catch (\Exception $e) {
            Log::error('Error getting weekly schedule: ' . $e->getMessage());
            return response()->json(['message' => 'Error retrieving schedule'], 500);
        }
    }

    /**
     * Update the user's weekly schedule (add time slot)
     */
    public function updateWeeklySchedule(Request $request)
    {
        try {
            $request->validate([
                'day_of_week_id' => 'required|exists:day_of_weeks,id',
                'time_slot_ids' => 'required|array|min:1',
                'time_slot_ids.*' => 'required|exists:time_slots,id',
            ]);

            $userId = auth()->id();

            // Lấy tất cả slot đã có của user cho day_of_week này
            $existingSlots = $this->userWeeklyTimeSlotRepository->getTimeSlotsByUser($userId)
                ->where('day_of_week_id', $request->day_of_week_id);

            $existingTimeSlotIds = $existingSlots->pluck('time_slot_id')->all();
            $incomingIds = $request->input('time_slot_ids', []);

            // Loại bỏ những id đã tồn tại
            $toCreateIds = array_values(array_diff($incomingIds, $existingTimeSlotIds));

            if (empty($toCreateIds)) {
                return response()->json([
                    'success' => true,
                    'message' => 'No new time slots to add',
                    'data' => []
                ]);
            }

            $items = array_map(function ($timeSlotId) use ($request, $userId) {
                return [
                    'user_id' => $userId,
                    'day_of_week_id' => $request->day_of_week_id,
                    'time_slot_id' => $timeSlotId,
                ];
            }, $toCreateIds);

            $created = $this->userWeeklyTimeSlotRepository->createMany($items);

            return response()->json([
                'success' => true,
                'message' => 'Time slots added successfully',
                'data' => WeeklyScheduleResource::collection($created)
            ]);
        } catch (ValidationException $e) {
            return response()->json(['message' => $e->errors()], 422);
        } catch (\Exception $e) {
            Log::error('Error adding time slot: ' . $e->getMessage());
            return response()->json(['message' => 'Error adding time slot'], 500);
        }
    }

    public function updateTimeSlot(Request $request, $id)
    {
        try {
            $request->validate([
                'day_of_week_id' => 'required|exists:day_of_weeks,id',
                'time_slot_id' => 'required|exists:time_slots,id',
            ]);
            $timeSlot = $this->userWeeklyTimeSlotRepository->getTimeSlotById($id);
            if (!$timeSlot || $timeSlot->user_id !== auth()->id()) {
                return response()->json(['message' => 'Time slot not found'], 404);
            }
            $updatedTimeSlot = $this->userWeeklyTimeSlotRepository->update($id, $request->all());
            Log::info($updatedTimeSlot);
            return response()->json([
                'success' => true,
                'message' => 'Time slot updated successfully',
                'data' => new WeeklyScheduleResource($updatedTimeSlot)
            ]);
        } catch (\Exception $e) {
            Log::error('Error updating time slot: ' . $e->getMessage());
            return response()->json(['message' => 'Error updating time slot'], 500);
        }
    }

    /**
     * Delete a time slot
     */
    public function deleteTimeSlot($id)
    {
        try {
            $timeSlot = $this->userWeeklyTimeSlotRepository->getTimeSlotById($id);

            if (!$timeSlot || $timeSlot->user_id !== auth()->id()) {
                return response()->json(['message' => 'Time slot not found'], 404);
            }

            $this->userWeeklyTimeSlotRepository->delete($id);
            return response()->json(['message' => 'Time slot deleted successfully']);
        } catch (\Exception $e) {
            Log::error('Error deleting time slot: ' . $e->getMessage());
            return response()->json(['message' => 'Error deleting time slot'], 500);
        }
    }

    /**
     * Add a new language for the user
     */
    public function addLanguage(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'language_id' => 'required|integer',
                'level_language_id' => 'required_if:is_native,0',
                'is_native' => 'required',
            ], [
                'level_language_id.required_if' => 'Vui lòng chọn trình độ nếu không phải ngôn ngữ mẹ đẻ.'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $user = auth()->user();
            // Kiểm tra trùng ngôn ngữ

            if ($this->userLanguageRepository->existsByUserAndLanguage($user->uid, $request->language_id)) {
                return response()->json([
                    'message' => 'Ngôn ngữ này đã tồn tại!'
                ], 422);
            }
            // Kiểm tra đã có ngôn ngữ mẹ đẻ chưa
            if ($request->is_native && $this->userLanguageRepository->existsNativeLanguage($user->uid)) {
                return response()->json([
                    'message' => 'Bạn chỉ được chọn một ngôn ngữ mẹ đẻ!'
                ], 422);
            }

            $language = $this->userLanguageRepository->create([
                'language_id' => $request->language_id,
                'level_language_id' => $request->level_language_id ?? null,
                'is_native' => $request->is_native,
                'uid' => $user->uid
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Language added successfully',
                'data' => new LanguageResource($language)
            ]);
        } catch (\Exception $e) {
            Log::error('Error adding language: ' . $e->getMessage());
            return response()->json(['message' => 'Error adding language'], 500);
        }
    }

    /**
     * Update a language entry
     */
    public function updateLanguage(Request $request, int $id)
    {
        try {
            $validator = Validator::make($request->all(), [
                'language_id' => 'required|integer',
                'level_language_id' => 'required|integer',
                'is_native' => 'required',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $user = auth()->user();
            $language = $this->userLanguageRepository->find($id);

            if (!$language || $language->user_id !== $user->id) {
                return response()->json(['message' => 'Language not found'], 404);
            }

            // Kiểm tra trùng ngôn ngữ (trừ chính bản ghi đang sửa)
            if ($this->userLanguageRepository->existsByUserAndLanguage($user->uid, $request->language_id)) {
                return response()->json([
                    'message' => 'Ngôn ngữ này đã tồn tại!'
                ], 422);
            }
            // Kiểm tra đã có ngôn ngữ mẹ đẻ chưa (trừ chính bản ghi đang sửa)
            if ($request->is_native && $this->userLanguageRepository->existsNativeLanguage($user->uid)) {
                return response()->json([
                    'message' => 'Bạn chỉ được chọn một ngôn ngữ mẹ đẻ!'
                ], 422);
            }

            $updatedLanguage = $this->userLanguageRepository->update([
                'language_id' => $request->language_id,
                'level_language_id' => $request->level_language_id,
                'is_native' => $request->is_native,
            ], $id);

            return response()->json([
                'success' => true,
                'message' => 'Language updated successfully',
                'data' => new LanguageResource($updatedLanguage)
            ]);
        } catch (\Exception $e) {
            Log::error('Error updating language: ' . $e->getMessage());
            return response()->json(['message' => 'Error updating language'], 500);
        }
    }

    /**
     * Delete a language entry
     */
    public function deleteLanguage(int $id)
    {
        try {
            $user = auth()->user();
            $language = $this->userLanguageRepository->find($id);

            if (!$language || $language->user_id !== $user->id) {
                return response()->json(['message' => 'Language not found'], 404);
            }

            $this->userLanguageRepository->delete($id);

            return response()->json([
                'success' => true,
                'message' => 'Language deleted successfully'
            ]);
        } catch (\Exception $e) {
            Log::error('Error deleting language: ' . $e->getMessage());
            return response()->json(['message' => 'Error deleting language'], 500);
        }
    }

    /**
     * Get user's languages
     */
    public function getUserLanguages()
    {
        try {
            $user = auth()->user();
            $languages = $this->userLanguageRepository->getLanguagesByUser($user->id);

            return response()->json([
                'success' => true,
                'data' => LanguageResource::collection($languages)
            ]);
        } catch (\Exception $e) {
            Log::error('Error getting languages: ' . $e->getMessage());
            return response()->json(['message' => 'Error retrieving languages'], 500);
        }
    }

    /**
     * Xoá hết các bản ghi cũ rồi thêm mới toàn bộ các lựa chọn study location cho user
     */
    public function addStudyLocation(Request $request)
    {
        $user = auth()->user();
        $data = $request->input('study_methods', []);

        // Validate từng item
        foreach ($data as $item) {
            $validator = Validator::make($item, [
                'id' => 'required|integer',
                'max_distance' => 'nullable|numeric|min:1',
                'transportation_fee' => 'nullable|numeric|min:0',
            ]);
            if ($validator->fails()) {
                return response()->json([
                    'message' => 'Dữ liệu không hợp lệ',
                    'errors' => $validator->errors()
                ], 422);
            }
        }

        // Xoá hết các bản ghi cũ của user
        $this->userStudyLocationRepository->deleteAll($user->uid);

        // Thêm mới toàn bộ các lựa chọn gửi lên
        $created = [];
        foreach ($data as $item) {
            $created[] = $this->userStudyLocationRepository->create([
                'uid' => $user->uid,
                'study_location_id' => $item['id'],
                'max_distance' => $item['max_distance'] ?? null,
                'transportation_fee' => $item['transportation_fee'] ?? null,
            ]);
        }

        return response()->json([
            'message' => 'Cập nhật hình thức học thành công',
            'data' => $created
        ]);
    }
}

