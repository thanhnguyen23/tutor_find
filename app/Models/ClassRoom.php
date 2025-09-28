<?php

namespace App\Models;

use App\Enums\ClassRoomAction;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClassRoom extends Model
{
    use HasFactory;

    protected $table = 'class_room';

    protected $fillable = [
        'booking_id',
        'tutor_uid',
        'student_uid',
        'scheduled_start_time',
        'scheduled_end_time',
        'scheduled_duration',
        'actual_start_time',
        'actual_end_time',
        'actual_duration',
        'participants_count',
        'max_participants',
        'status',
        'topic',
        'agenda',
        'created_at',
        'updated_at',
        'zoom_created_at'
    ];

    public static $LIST_STATUS = [
        ClassRoomAction::Pending->value => "Chờ lớp học",
        ClassRoomAction::Scheduled->value => "Đã lên lịch",
        ClassRoomAction::Started->value => "Đã bắt đầu",
        ClassRoomAction::Ended->value => "Đã kết thúc",
        ClassRoomAction::Missed->value => "Đã bỏ lỡ",
        ClassRoomAction::Cancelled->value => "Đã hủy",
        ClassRoomAction::Error->value => "Xảy ra lỗi",
    ];

    public function status () {
        return self::$LIST_STATUS[$this->status] ?? $this->status;
    }

    public function booking() {
        return $this->belongsTo(UserBooking::class);
    }
}
