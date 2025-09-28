<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentMethod extends Model
{
    use HasFactory;

    public static $LIST_CODE_TEXT = [
        'atm' => 'Chuyển khoản qua tài khoản ngân hàng',
        'vnpay' => 'VNPay',
        'cash' => 'Tiền mặt',
        'momo' => 'Momo',
    ];

    public static $LIST_CODE = [
        'atm' => 'atm',
        'vnpay' => 'vnpay',
        'cash' => 'cash',
        'momo' => 'momo',
    ];

    protected $fillable  = ['name', 'description', 'is_default', 'status'];

    public function code () {
        return self::$LIST_CODE_TEXT[$this->code] ?? $this->code;
    }
}
