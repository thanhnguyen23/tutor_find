<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DayOfWeek extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'slug',
    ];

    public function userWeeklyTimeSlots()
    {
        return $this->hasMany(UserWeeklyTimeSlot::class);
    }
}
