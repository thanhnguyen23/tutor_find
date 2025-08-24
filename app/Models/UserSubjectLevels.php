<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserSubjectLevels extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_subject_id',
        'education_level_id',
        'price'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function user_subject()
    {
        return $this->belongsTo(UserSubject::class);
    }

    public function education_level()
    {
        return $this->belongsTo(EducationLevel::class);
    }
}
