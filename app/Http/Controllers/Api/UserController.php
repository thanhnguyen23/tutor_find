<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Repositories\Contracts\UserRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class UserController extends Controller
{
    const PER_PAGE = 6;
    protected $userRepository;

    public function __construct(UserRepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function getTutors(Request $request)
    {
        try {
            $filters = $request->only([
                'subject_id',
                'education_level_id',
                'experience',
                'provinces_id',
                'sex',
                'time_slot_start',
                'time_slot_end',
                'sort_by'
            ]);

            $allData = $this->userRepository->searchTutors($filters, self::PER_PAGE);
            return UserResource::collection($allData)
            ->additional([
                'success' => true,
            ]);
        } catch (\Exception $e) {
            Log::error('Error searching tutors: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Đã xảy ra lỗi khi tìm kiếm gia sư'
            ], 500);
        }
    }

    public function getTutorDetail($uid)
    {
        $tutor = $this->userRepository->getTutorDetailByUid($uid);
        // Có thể custom lại response nếu cần
        return response()->json(new UserResource($tutor));
    }
}
