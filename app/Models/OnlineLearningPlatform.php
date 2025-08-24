<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OnlineLearningPlatform extends Model
{
    use HasFactory;

    protected $table = 'online_learning_platform';

    protected $fillable  = [
        'name'
    ];
}
