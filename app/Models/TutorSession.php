<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TutorSession extends Model
{
    use HasFactory;

    protected $fillable = [
        'time',
        'duration_hours', // Thời lượng (theo giờ, ví dụ 0.25, 0.5, 1.00)
        'description',
        'recommended',
        'allow_free'
    ];

    protected $casts = [
        'duration_hours' => 'float',
        'recommended' => 'boolean',
        'allow_free' => 'boolean',
    ];
}
