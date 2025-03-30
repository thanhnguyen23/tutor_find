<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
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

class AuthController extends Controller
{
    protected $userRepository;
    protected $userEducationRepository;
    protected $userSubjectRepository;
    protected $userExperienceRepository;

    public function __construct(
        UserRepositoryInterface $userRepository,
        UserEducationRepositoryInterface $userEducationRepository,
        UserSubjectRepositoryInterface $userSubjectRepository,
        UserExperienceRepositoryInterface $userExperienceRepository
    ) {
        $this->userRepository = $userRepository;
        $this->userEducationRepository = $userEducationRepository;
        $this->userSubjectRepository = $userSubjectRepository;
        $this->userExperienceRepository = $userExperienceRepository;
    }

    public function register(Request $request)
    {
        // 1. Định nghĩa validation rules ngắn gọn hơn
        $rules = [
            'role' => ['required', Rule::in([0, 1])],
            'firstName' => 'required|string|max:255',
            'lastName' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'phone' => 'required|string|max:10|unique:users',
            'password' => 'required|string|min:8',
            'passwordConfirmation' => 'required|same:password',
            'terms' => 'required|accepted',
        ];

        // Thêm rules cho tutor (role = 1)
        $tutorRules = [
            'educations.*' => [
                'school_name' => 'required_if:role,1|string|max:255',
                'major' => 'required_if:role,1|string|max:255',
                'time' => 'required_if:role,1|date',
            ],
            'experiences.*' => [
                'name' => 'required_if:role,1|string|max:255',
                'position' => 'required_if:role,1|string|max:255',
                'start_date' => 'required_if:role,1|date',
                'time' => 'required_if:role,1|date',
            ],
        ];

         // Gộp rules
        $validator = Validator::make($request->all(), array_merge($rules, $request->role == 1 ? $tutorRules : []));

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            return DB::transaction(function () use ($request) {
                // 2. Tạo user
                $userData = [
                    'role' => $request->role,
                    'first_name' => $request->firstName,
                    'last_name' => $request->lastName,
                    'email' => $request->email,
                    'phone' => $request->phone,
                    'password' => Hash::make($request->password),
                ];
                $user = $this->userRepository->create($userData);

                // 3. Xử lý dữ liệu theo role
                $this->handleRoleSpecificData($request, $user);

                // 4. Tạo token và trả về response
                return response()->json([
                    'message' => 'Registration successful',
                    'user' => new UserResource($user),
                    'token' => $user->createToken('auth_token')->plainTextToken,
                ], 201);
            });
        } catch (\Exception $e) {
            Log::error(__METHOD__, [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json(['message' => 'Đã xảy ra lỗi, vui lòng thử lại sau'], 500);
        }
    }

    // Hàm hỗ trợ xử lý dữ liệu theo role
    private function handleRoleSpecificData(Request $request, $user)
    {
        if ($request->role === 0 && $request->has('educationLevels')) {
            $this->userEducationRepository->create([
                'user_id' => $user->id,
                'education_level_id' => $request->educationLevels,
            ]);
            return;
        }

        if ($request->role === 1) {
            $this->saveEducations($request->educations, $user->id);
            $this->saveSubjects($request->subjects, $user->id);
            $this->saveExperiences($request->experiences, $user->id);
        }
    }

    // Lưu educations
    private function saveEducations(?array $educations, int $userId): void
    {
        if (empty($educations)) return;

        foreach ($educations as $education) {
            $this->userEducationRepository->create([
                'user_id' => $userId,
                'school_name' => $education['school_name'],
                'major' => $education['major'],
                'time' => $education['time'],
                'description' => $education['description'],
            ]);
        }
    }

    // Lưu subjects
    private function saveSubjects(?array $subjects, int $userId): void
    {
        if (empty($subjects)) return;

        foreach ($subjects as $subject) {
            Log::info($subject['years_of_experience']);
            $this->userSubjectRepository->create([
                'user_id' => $userId,
                'subject_id' => $subject['id'],
                'years_of_experience' => $subject['years_of_experience'],
            ]);
        }
    }

    // Lưu experiences
    private function saveExperiences(?array $experiences, int $userId): void
    {
        if (empty($experiences)) return;

        foreach ($experiences as $experience) {
            $this->userExperienceRepository->create([
                'user_id' => $userId,
                'name' => $experience['name'],
                'position' => $experience['position'],
                'time' => $experience['time'],
                'description' => $experience['description'],
            ]);
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
            'user' => new UserResource($user),
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
