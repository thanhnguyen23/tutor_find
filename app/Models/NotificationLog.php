<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NotificationLog extends Model
{
    use HasFactory;

    protected $fillable  = [
        'notification_type',
        'uid',
        'user_uid',
        'name',
        'description',
        'is_read',
    ];

    protected $casts = [
        'is_read' => 'boolean',
    ];

    public function notificationTypes() {
        return $this->hasOne(NotificationType::class, 'type', 'notification_type');
    }

    public function user() {
        return $this->hasOne(User::class, 'uid', 'user_uid');
    }
}
