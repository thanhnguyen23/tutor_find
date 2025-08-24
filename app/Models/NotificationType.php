<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NotificationType extends Model
{
    use HasFactory;

    public static $TYPE_SCHEDULE = "schedule";
    public static $TYPE_MESSAGE = "message";
    public static $TYPE_REVIEW = "review";
    public static $TYPE_PROFILE = "profile";

    protected $table = 'notification_type';

    protected $fillable  = [
        'name',
        'icon',
        'type'
    ];
}
