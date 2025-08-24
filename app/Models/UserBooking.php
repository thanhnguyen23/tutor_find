<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use App\Models\Subject;
use App\Models\EducationLevel;
use App\Models\TimeSlot;
use App\Models\Packages;
use App\Models\User;
use App\Enums\UserBookingAction;
use Illuminate\Support\Facades\Log;

class UserBooking extends Model
{
    use HasFactory;

    public static $LIST_STATUS = [
        UserBookingAction::Pending->value => "Chờ xác nhận",
        UserBookingAction::Confirmed->value => "Đã xác nhận",
        UserBookingAction::Completed->value => "Đã hoàn thành",
        UserBookingAction::Cancelled->value => "Đã hủy"
    ];

    public static $cancelled_status = UserBookingAction::Cancelled->value;
    public static $pending_status = UserBookingAction::Pending->value;
    public static $confirmed_status = UserBookingAction::Confirmed->value;
    public static $completed_status = UserBookingAction::Completed->value;

    protected $fillable  = ['uid', 'tutor_uid', 'request_code', 'subject_id', 'education_level_id', 'date', 'start_time_id', 'end_time_id', 'location', 'study_location_id', 'note', 'hourly_rate', 'duration', 'package_id', 'num_sessions', 'total_price', 'status', 'is_package'];

    protected $casts = [
        'is_package' => 'boolean',
    ];

    protected static function booted()
    {
        static::creating(function ($booking) {
            $booking->request_code = strtoupper(Str::random(10));
        });
    }

    public function status () {
        return self::$LIST_STATUS[$this->status] ?? $this->status;
    }

    // Subject relationship
    public function subject()
    {
        return $this->belongsTo(Subject::class, 'subject_id');
    }

    // Education level relationship
    public function educationLevel()
    {
        return $this->belongsTo(EducationLevel::class, 'education_level_id');
    }

    // Time slot start relationship
    public function timeSlotStart()
    {
        return $this->belongsTo(TimeSlot::class, 'start_time_id');
    }

    // Time slot end relationship
    public function timeSlotEnd()
    {
        return $this->belongsTo(TimeSlot::class, 'end_time_id');
    }

    // Package relationship
    public function package()
    {
        return $this->belongsTo(UserPackage::class, 'package_id', 'id');
    }

    // User relationship
    public function user()
    {
        return $this->belongsTo(User::class, 'uid', 'uid');
    }

    public function tutor()
    {
        return $this->belongsTo(User::class, 'tutor_uid', 'uid');
    }

    public function studyLocation()
    {
        return $this->belongsTo(StudyLocation::class, 'study_location_id');
    }

    public function userBookingLogs()
    {
        return $this->hasMany(UserBookingLog::class, 'user_booking_id', 'id');
    }
}
