<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\NotificationLogResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Repositories\Contracts\NotificationLogRepositoryInterface;
use App\Repositories\Contracts\NotificationTypeRepositoryInterface;
use App\Models\NotificationType;
use Illuminate\Support\Facades\Log;

class NotificationLogController extends Controller
{
    const PER_PAGE = 6;
    protected $notificationLogRepo;
    protected $notificationTypeRepo;

    public function __construct(
        NotificationLogRepositoryInterface $notificationLogRepo,
        NotificationTypeRepositoryInterface $notificationTypeRepo
    ) {
        $this->notificationLogRepo = $notificationLogRepo;
        $this->notificationTypeRepo = $notificationTypeRepo;
    }

    public function getAll(Request $request) {
        Validator::make($request->all(), [
            'is_read' => 'nullable|numeric|min:0|max:1',
            'per_page' => 'nullable|numeric|min:1|max:100',
        ]);

        $uid = auth()->user()->uid;
        $per_page = $request->per_page ?? self::PER_PAGE;
        $is_read = $request->is_read;

        if ($is_read !== null) {
            $allData = $this->notificationLogRepo
            ->getAllPaginationFilter(
                $uid,
                $is_read,
                $per_page,
            );
        } else {
            $allData = $this->notificationLogRepo
            ->getAllPagination(
                $uid,
                $per_page,
            );
        }

        return NotificationLogResource::collection($allData)
        ->additional([
            'success' => true,
        ]);
    }

    /**
     * Thêm notification log mới theo type
     * Request: type (string), name, description
     */
    public function store(Request $request)
    {
        $request->validate([
            'type' => 'required|string',
            'name' => 'required|string',
            'description' => 'nullable|string',
        ]);

        $type = $request->input('type');
        $notificationType = $this->notificationTypeRepo->findByType($type);
        if (!$notificationType) {
            return response()->json(['message' => 'Notification type not found'], 404);
        }

        $data = [
            'notification_type_id' => $notificationType->id,
            'name' => $request->input('name'),
            'description' => $request->input('description'),
        ];
        $log = $this->notificationLogRepo->create($data);
        return response()->json($log, 201);
    }

    public function storeSchedule(Request $request)
    {
        return $this->storeByType($request, NotificationType::$TYPE_SCHEDULE);
    }

    public function storeMessage(Request $request)
    {
        return $this->storeByType($request, NotificationType::$TYPE_MESSAGE);
    }

    public function storeReview(Request $request)
    {
        return $this->storeByType($request, NotificationType::$TYPE_REVIEW);
    }

    protected function storeByType(Request $request, $type)
    {
        $request->validate([
            'name' => 'required|string',
            'description' => 'nullable|string',
        ]);

        $notificationType = $this->notificationTypeRepo->findByType($type);
        if (!$notificationType) {
            return response()->json(['message' => 'Notification type not found'], 404);
        }

        $data = [
            'notification_type_id' => $notificationType->id,
            'name' => $request->input('name'),
            'description' => $request->input('description'),
        ];
        $log = $this->notificationLogRepo->create($data);
        return response()->json($log, 201);
    }

    /**
     * Đánh dấu tất cả thông báo là đã đọc cho user hiện tại
     */
    public function markAllAsRead(Request $request)
    {
        $uid = auth()->user()->uid;
        $this->notificationLogRepo->markAllAsRead($uid);
        return response()->json(['success' => true]);
    }

    /**
     * Đánh dấu 1 thông báo là đã đọc
     */
    public function markAsRead($id)
    {
        $uid = auth()->user()->uid;
        $updated = $this->notificationLogRepo->update([
            'is_read' => 1
        ], $uid, $id);

        if ($updated) {
            return response()->json(['success' => true, 'data' => $updated]);
        } else {
            return response()->json(['success' => false, 'message' => 'Không tìm thấy dữ liệu'], 404);
        }
    }
}
