<?php

namespace App\Models;

use App\Enums\PaymentAction;
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
        UserBookingAction::WaitingPayment->value => "Chờ than toán",
        UserBookingAction::Pending->value => "Chờ xác nhận",
        UserBookingAction::Confirmed->value => "Đã xác nhận",
        UserBookingAction::AwaitingClass->value => "Chờ lớp học hoàn thành",
        UserBookingAction::Completed->value => "Đã hoàn thành",
        UserBookingAction::Rejected->value => "Đã từ chối",
        UserBookingAction::Missed->value => "Đã bỏ lỡ",
    ];

    public static $waiting_payment = UserBookingAction::WaitingPayment->value;
    public static $rejected_status = UserBookingAction::Rejected->value;
    public static $cancelled_status = UserBookingAction::Cancelled->value;
    public static $pending_status = UserBookingAction::Pending->value;
    public static $confirmed_status = UserBookingAction::Confirmed->value;
    public static $completed_status = UserBookingAction::Completed->value;
    // Payment status flow and labels
    public static $PAYMENT_FLOW = ['pending', 'confirmed', 'awaiting_class', 'completed', 'cancelled', 'expired'];

    public static $LIST_PAYMENT_STATUS = [
        'pending' => 'Chờ thanh toán',
        'confirmed' => 'Đã xác nhận',
        'completed' => 'Hoàn tất',
        'cancelled' => 'Đã hủy',
        'expired' => 'Đã hết hạn',
    ];

    public static $payment_waiting_payment = 'waiting_payment';
    public static $payment_pending_status = 'pending';
    public static $payment_confirmed_status = 'confirmed';
    public static $payment_completed_status = 'completed';
    public static $payment_cancelled_status = 'cancelled';
    public static $payment_expired_status = 'expired';
    public static $payment_rejected_status = 'rejected';

    protected $fillable  = [
        'uid',
        'tutor_uid',
        'request_code',
        'subject_id',
        'education_level_id',
        'tutor_session_id',
        'date',
        'start_time',
        'end_time',
        'time_slot_id',
        'location',
        'study_location_id',
        'note',
        'hourly_rate',
        'duration',
        'package_id',
        'num_sessions',
        'total_price',
        'status',
        'is_package',
        'is_free',
        'type'
    ];

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

    public function payment()
    {
        return $this->hasOne(Payment::class, 'booking_id', 'id');
    }

    public function userBookingComplaint()
    {
        return $this->hasOne(UserBookingComplaint::class, 'booking_id', 'id');
    }

    public function classRoom() {
        return $this->hasOne(ClassRoom::class, 'booking_id', 'id')->latestOfMany('id');
    }

    public function timeSlot()
    {
        return $this->belongsTo(TimeSlot::class, 'time_slot_id');
    }

    public function tutorSession()
    {
        return $this->belongsTo(TutorSession::class, 'tutor_session_id');
    }

    public function isTrial(): bool
    {
        return $this->type === 'trial';
    }

    public function isPaid(): bool
    {
        return $this->type === 'paid';
    }

    // Scope: lọc theo user (student hoặc tutor)
    public function scopeOfUser($query, $uid)
    {
        return $query->where(function ($q) use ($uid) {
            $q->where('uid', $uid)
              ->orWhere('tutor_uid', $uid);
        });
    }

    // Scope: chỉ lấy booking đã thanh toán thành công
    public function scopePaid($query)
    {
        return $query->whereHas('payment', function ($q) {
            $q->where('status', PaymentAction::Success);
        });
    }

    // Scope: eager load các quan hệ mặc định
    public function scopeWithRelations($query)
    {
        return $query->with([
            'subject',
            'studyLocation',
            'educationLevel',
            'timeSlotStart',
            'timeSlotEnd',
            'package',
            'user',
            'tutor',
            'userBookingLogs',
            'userBookingLogs.user',
            'payment',
            'payment.paymentMethod',
            'userBookingComplaint',
            'classRoom'
        ]);
    }
}
