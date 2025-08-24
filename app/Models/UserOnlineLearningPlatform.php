<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserOnlineLearningPlatform extends Model
{
    use HasFactory;

    protected $fillable  = [
        'uid', 'id_online_learning_platform'
    ];
}
