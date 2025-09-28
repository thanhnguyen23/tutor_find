<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\TutorResource;
use App\Repositories\Contracts\UserRepositoryInterface;
use App\Repositories\Contracts\UserEducationRepositoryInterface;
use App\Repositories\Contracts\UserSubjectRepositoryInterface;
use App\Repositories\Contracts\UserExperienceRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Support\Str;
use App\Repositories\Contracts\EducationLevelRepositoryInterface;
use App\Repositories\Contracts\NotificationLogRepositoryInterface;

class AuthController extends Controller
{
    protected $userRepository;
    protected $userEducationRepository;
    protected $userSubjectRepository;
    protected $userExperienceRepository;
    protected $userEducationLevelRepository;
    protected $educationLevelRepository;
    protected $notificationLogRepository;

    public function __construct(
        UserRepositoryInterface $userRepository,
        UserEducationRepositoryInterface $userEducationRepository,
        UserSubjectRepositoryInterface $userSubjectRepository,
        UserExperienceRepositoryInterface $userExperienceRepository,
        EducationLevelRepositoryInterface $educationLevelRepository,
        NotificationLogRepositoryInterface $notificationLogRepository
    ) {
        $this->userRepository = $userRepository;
        $this->userEducationRepository = $userEducationRepository;
        $this->userSubjectRepository = $userSubjectRepository;
        $this->userExperienceRepository = $userExperienceRepository;
        $this->educationLevelRepository = $educationLevelRepository;
        $this->notificationLogRepository = $notificationLogRepository;
    }

    public function register(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'first_name' => 'required|string|max:255',
                'last_name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users',
                'password' => 'required|string|min:8',
                'role' => 'required|in:0,1',
                'educationLevels' => 'nullable|numeric|min:1',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $user = User::create([
                'uid' => Str::uuid(),
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'email' => $request->email,
                'phone' => $request->phone,
                'password' => Hash::make($request->password),
                'role' => $request->role,
                'reminder_emails_sent' => 0,
                'education_level_id' => $request->role == User::ROLE_STUDENT ? $request->educationLevels : null,
            ]);

            // Gửi notification nhắc hoàn thiện profile sử dụng UserService
            $completion = app(\App\Services\UserService::class)->calculateProfileCompletion($user);
            if (!$completion['completed']) {
                $fields = [];
                $map = [
                    'personal_info' => 'Thông tin cá nhân',
                    'subjects' => 'Môn học',
                    'education' => 'Học vấn',
                    'experience' => 'Kinh nghiệm',
                    'schedule' => 'Lịch trình',
                    'languages' => 'Ngôn ngữ/gói dịch vụ',
                ];
                foreach ($completion['details'] as $key => $done) {
                    if (!$done && isset($map[$key])) {
                        $fields[] = $map[$key];
                    }
                }
                $fieldsText = $fields ? ('Các mục cần hoàn thiện: ' . implode(', ', $fields)) : 'Vui lòng hoàn thiện hồ sơ cá nhân.';
                $this->notificationLogRepository->create([
                    'uid' => $user->uid,
                    'name' => 'Hoàn thiện hồ sơ',
                    'description' => $fieldsText,
                    'notification_type' => 'profile',
                ]);
            }

            // Send welcome email if user is a tutor
            if ($user->role === User::ROLE_TUTOR) {
                $userService = app(UserService::class);
                $userService->sendWelcomeEmail($user);
            }

            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json([
                'access_token' => $token,
                'token_type' => 'Bearer',
                'user' => new TutorResource($user)
            ]);

        } catch (\Exception $e) {
            Log::error('Registration error: ' . $e->getMessage());
            return response()->json([
                'message' => 'An error occurred during registration'
            ], 500);
        }
    }

    public function login(Request $request)
    {
        // Validation
        $validator = Validator::make($request->all(), [
            'email' => 'required_without:phone|email|nullable',
            'phone' => 'required_without:email|string|max:10|nullable',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        // Xác định trường đăng nhập
        $field = $request->filled('email') ? 'email' : 'phone';
        $value = $request->input($field);

        if ($request->has('email')) {
            $user = $this->userRepository->findByEmail($value);
        } else {
            $user = $this->userRepository->findByPhone($value);
        }

        if (!$user) {
            return response()->json([
                'message' => "$field không tồn tại",
            ], 401);
        }

        if (!Hash::check($request->password, $user->password)) {
            return response()->json([
                'message' => 'Mật khẩu không chính xác',
            ], 401);
        }

        Auth::login($user);
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Login successful',
            'user' => new TutorResource($user),
            'token' => $token,
        ], 200);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Successfully logged out'
        ]);
    }

    public function verifyToken(Request $request)
    {
        // Lấy token từ header Authorization (Bearer token)
        $token = $request->bearerToken();

        if (!$token) {
            return response()->json([
                'message' => 'Token not provided',
            ], 401); // Unauthorized
        }

        // Kiểm tra token hợp lệ với Sanctum (hoặc JWT)
        try {
            // Nếu dùng Sanctum
            $user = Auth::user();

            if (!$user) {
                return response()->json([
                    'message' => 'Invalid token',
                ], 401);
            }

            return response()->json([
                'message' => 'Token is valid',
                'user' => $user,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Token verification failed',
                'error' => $e->getMessage(),
            ], 401);
        }
    }
}
