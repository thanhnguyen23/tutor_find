<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Packages extends Model
{
    use HasFactory;

    protected $fillable = [
        'package_id',
        'name',
        'price',
        'discount_percent',
        'max_contacts',
        'months',
        'years',
        'is_popular',
        'icon',
        'features',
        'is_active',
    ];

    protected $casts = [
        'features' => 'array',
    ];
}
