<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserWeeklyTimeSlot extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'day_of_week_id',
        'time_slot_id',
        'is_available',
        'teaching_mode'
    ];

    protected $casts = [
        'is_available' => 'boolean',
        'time_slot_id' => 'integer'
    ];

    /**
     * Get the user that owns the time slot.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function dayOfWeek(): BelongsTo
    {
        return $this->belongsTo(DayOfWeek::class);
    }

    public function timeSlot(): BelongsTo
    {
        return $this->belongsTo(TimeSlot::class, 'time_slot_id');
    }
}
