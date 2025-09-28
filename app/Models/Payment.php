<?php

namespace App\Models;

use App\Enums\PaymentAction;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    public static $LIST_STATUS = [
        PaymentAction::Pending->value => "Chờ thanh toán",
        PaymentAction::Success->value => "Thanh toán thành công",
        PaymentAction::Failed->value => "Thanh toán thất bại",
        PaymentAction::Refunded->value => "Đã hoàn tiền",
        PaymentAction::NonPayment->value => "Không thanh toán"
    ];

    public function status () {
        return self::$LIST_STATUS[$this->status] ?? $this->status;
    }

    protected $fillable = [
        'booking_id',
        'payment_method_id',
        'user_uid',

        'transaction_id',
        'account_number',
        'transaction_date',
        'payment_code',
        'payment_code',
        'transfer_type',
        'transfer_amount',
        'accumulated',
        'sub_account',
        'reference_code',
        'amount',
        'fee',
        'refunded_amount',
        'currency',

        'gateway',
        'status',

        'paid_at',
        'expired_at',

        'note',
        'raw',
    ];


    protected $casts = [
        'paid_at' => 'datetime',
        'expired_at' => 'datetime',
    ];

    public function paymentMethod()
    {
        return $this->belongsTo(PaymentMethod::class);
    }

    public function booking() {
        return $this->belongsTo(UserBooking::class);
    }

    public function user() {
        return $this->belongsTo(User::class, 'user_uid', 'uid');
    }
}
