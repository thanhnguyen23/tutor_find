<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserExperience extends Model
{
    use HasFactory;

    const LEVEL_MILESTONE = [
        'new' => 0,
        'experienced' => 3,
        'expert' => 5,
    ];

    const LEVEL_CLASS = [
        'new' => 'new',
        'experienced' => 'experienced',
        'expert' => 'expert',
    ];

    const LEVEL_MILESTONE_YEARS_MAX = 10;

    protected $fillable = [
        'user_id',
        'name',
        'position',
        'start_date',
        'end_date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public static function evaluateExperience($years)
    {
        $progress = min($years * 10, self::LEVEL_MILESTONE_YEARS_MAX * 10); // Tối đa 100%
        if ($years >= self::LEVEL_MILESTONE['expert']) {
            return [
                'level' => 'Chuyên gia',
                'class' => self::LEVEL_CLASS['expert'],
                'progress' => $progress,
                'years' => $years,
                'milestone' => self::LEVEL_MILESTONE_YEARS_MAX,
            ];
        } elseif ($years >= self::LEVEL_MILESTONE['experienced']) {
            return [
                'level' => 'Kinh nghiệm',
                'class' => self::LEVEL_CLASS['experienced'],
                'progress' => $progress,
                'years' => $years,
                'milestone' => self::LEVEL_MILESTONE_YEARS_MAX,
            ];
        }
        return [
            'level' => 'Mới',
            'class' => self::LEVEL_CLASS['new'],
            'progress' => $progress,
            'years' => $years,
            'milestone' => self::LEVEL_MILESTONE_YEARS_MAX,
        ];
    }
}
