<?php

namespace App\Models;

use App\Enums\UserBookingAction;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserBookingLog extends Model
{
    use HasFactory;

    protected $fillable  = ['uid', 'user_booking_id', 'status', 'note'];

    public static $LIST_STATUS = [
        UserBookingAction::Pending->value => "Chờ xác nhận",
        UserBookingAction::Confirmed->value => "Đã xác nhận",
        UserBookingAction::Completed->value => "Đã hoàn thành",
        UserBookingAction::Cancelled->value => "Đã hủy",
        UserBookingAction::Rejected->value => "Đã từ chối",
    ];

    public function status () {
        return self::$LIST_STATUS[$this->status] ?? $this->status;
    }

    public function user()
    {
        return $this->hasOne(User::class, 'uid', 'uid');
    }

    public function user_booking() {
        return $this->hasOne(UserBooking::class, 'id', 'user_booking_id');
    }
}
