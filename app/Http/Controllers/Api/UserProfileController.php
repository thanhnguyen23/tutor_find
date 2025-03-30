<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserProfileResource;
use App\Repositories\Contracts\UserRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class UserProfileController extends Controller
{
    protected $userRepository;

    public function __construct(UserRepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function getUserDataDetail()
    {
        try {
            $user = Auth::user();
            $userData = $this->userRepository->getUserDataDetail($user->id);

            return response()->json(new UserProfileResource($userData));
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
            $user = Auth::user();

            $dataUpdate = $request->only([
                'first_name',
                'last_name',
                'phone',
                'address',
                'about_you',
            ]);

            $userData = $this->userRepository->getUserDataDetail($user->id);
            $userData->update($dataUpdate);

            return response()->json([
                'message' => 'Cập nhật thông tin thành công',
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

    public function updateEducation(Request $request)
    {
        try {
            $request->validate([
                'id' => 'required|exists:user_educations,id',
                'school_name' => 'sometimes|string|max:255',
                'major' => 'sometimes|string|max:255',
                'time' => 'sometimes|string|max:30',
                'description' => 'sometimes|string|max:255',
            ]);

            $user = Auth::user();
            $userData = $this->userRepository->getUserDataDetail($user->id);

            $updated = $userData->userEducations()
                ->where('id', $request->id)
                ->where('user_id', $user->id)
                ->update($request->only(['school_name', 'major', 'time', 'description']));

            if (!$updated) {
                return response()->json([
                    'message' => 'Không tìm thấy thông tin để cập nhật'
                ], 404);
            }

            $userData = $this->userRepository->getUserDataDetail($user->id);

            return response()->json([
                'message' => 'Cập nhật thông tin thành công',
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

    public function updateExperience(Request $request)
    {
        try {
            $request->validate([
                'id' => 'required|exists:user_experiences,id',
                'name' => 'sometimes|string|max:255',
                'position' => 'sometimes|string|max:255',
                'time' => 'sometimes|string|max:30',
                'description' => 'sometimes|string|max:255',
            ]);

            $user = Auth::user();
            $userData = $this->userRepository->getUserDataDetail($user->id);

            $updated = $userData->userExperiences()
                ->where('id', $request->id)
                ->where('user_id', $user->id)
                ->update($request->only(['name', 'position', 'time', 'description']));

            if (!$updated) {
                return response()->json([
                    'message' => 'Không tìm thấy thông tin để cập nhật'
                ], 404);
            }

            $userData = $this->userRepository->getUserDataDetail($user->id);

            return response()->json([
                'message' => 'Cập nhật thông tin thành công',
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

    public function updateSubject(Request $request)
    {
        try {
            $request->validate([
                'id' => 'required|exists:user_subjects,id',
                'subject_id' => 'required|exists:subjects,id',
                'level_id' => 'required|exists:levels,id',
                'years_of_experience' => 'required|integer|min:0',
            ]);

            $user = Auth::user();
            $userData = $this->userRepository->getUserDataDetail($user->id);

            $updated = $userData->userSubjects()
                ->where('id', $request->id)
                ->where('user_id', $user->id)
                ->update($request->only(['subject_id', 'level_id', 'years_of_experience']));

            if (!$updated) {
                return response()->json([
                    'message' => 'Không tìm thấy thông tin để cập nhật'
                ], 404);
            }

            $userData = $this->userRepository->getUserDataDetail($user->id);

            return response()->json([
                'message' => 'Cập nhật thông tin thành công',
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
}
