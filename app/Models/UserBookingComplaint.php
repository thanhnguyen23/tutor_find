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
use App\Enums\UserBookingComplaintAction;
use Illuminate\Support\Facades\Log;

class UserBookingComplaint extends Model
{
    use HasFactory;

    public static $LIST_STATUS = [
        UserBookingComplaintAction::Pending->value => 'Đang xử lý',
        UserBookingComplaintAction::UnderReview->value => 'Đang xem xét',
        UserBookingComplaintAction::Resolved->value => 'Đã giải quyết',
        UserBookingComplaintAction::Rejected->value => 'Đã từ chối',
    ];

    public static $rejected_status = UserBookingComplaintAction::Rejected->value;
    public static $resolved_status = UserBookingComplaintAction::Resolved->value;
    public static $pending_status = UserBookingComplaintAction::Pending->value;
    public static $under_review_status = UserBookingComplaintAction::UnderReview->value;

    protected $fillable  = [
        'booking_id', 'complaint_type_id', 'uid', 'reason', 'description', 'status', 'evidence'
    ];

    protected $casts = [
        'evidence' => 'array',
    ];

    public function status () {
        return self::$LIST_STATUS[$this->status] ?? $this->status;
    }

    public function booking() {
        return $this->belongsTo(UserBooking::class, 'booking_id', 'id');
    }

    public function user() {
        return $this->belongsTo(User::class, 'uid', 'uid');
    }

    public function complaintType() {
        return $this->belongsTo(ComplaintType::class, 'complaint_type_id', 'id');
    }

}
