<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudyLocation extends Model
{
    use HasFactory;

    protected $fillable  = [
        'name', 'description', 'icon', 'home_tutor', 'home_user',
    ];

    protected $casts = [
        'home_tutor' => 'boolean',
        'home_user' => 'boolean',
    ];
}
