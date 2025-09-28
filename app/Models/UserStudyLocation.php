<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserStudyLocation extends Model
{
    use HasFactory;

    protected $fillable  = [
        'uid', 'study_location_id', 'max_distance', 'transportation_fee'
    ];

    public function studyLocation()
    {
        return $this->hasMany(StudyLocation::class, 'id', 'study_location_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'uid', 'uid');
    }
}
