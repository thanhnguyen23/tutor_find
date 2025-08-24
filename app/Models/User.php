<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    const ROLE_TUTOR = 1;
    const ROLE_STUDENT = 0;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'uid',
        'role',
        'cccd',
        'cccd_front',
        'cccd_back',
        'sex',
        'first_name',
        'last_name',
        'email',
        'password',
        'phone',
        'address',
        'districts_id',
        'provinces_id',
        'wards_id',
        'about_you',
        'title_ads',
        'student_number',
        'lesson_number',
        'education_level_id',
        'reminder_emails_sent',
        'last_reminder_sent_at',
        'profile_completed',
        'price',
        'avatar',
        'is_active',
        'referral_link',
        'is_free_study',
        'free_study_time',
        'last_activity',
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    protected $appends = [
        'full_name',
    ];

    protected $casts = [
        'is_free_study' => 'boolean',
        'free_study_time' => 'integer',
    ];

    public function roleName()
    {
        return $this->role == 1 ? 'Gia sư' : 'Học sinh';
    }

    public function markProfileCompleted()
    {
        $this->update(['profile_completed' => true]);
    }

    public function markProfileNotCompleted()
    {
        $this->update(['profile_completed' => false]);
    }

    public function getFullNameAttribute()
    {
        return $this->first_name . ' ' . $this->last_name;
    }

    public function userEducations()
    {
        return $this->hasMany(UserEducation::class);
    }

    public function userExperiences()
    {
        return $this->hasMany(UserExperience::class);
    }

    public function userSubjects()
    {
        return $this->hasMany(UserSubject::class);
    }

    public function userTimeSlots()
    {
        return $this->hasMany(UserTimeSlot::class);
    }

    public function userWeeklyTimeSlots()
    {
        return $this->hasMany(UserWeeklyTimeSlot::class);
    }

    public function userPackages()
    {
        return $this->hasMany(UserPackage::class);
    }

    public function userLanguages()
    {
        return $this->hasMany(UserLanguage::class);
    }

    public function userStudyLocations()
    {
        return $this->hasMany(UserStudyLocation::class, 'uid', 'uid');
    }

    public function educationLevel()
    {
        return $this->hasOne(EducationLevel::class, 'id', 'education_level_id');
    }

    public function provinces()
    {
        return $this->hasOne(Province::class, 'id', 'provinces_id');
    }

    public function districts()
    {
        return $this->hasOne(District::class, 'id', 'districts_id');
    }

    public function wards()
    {
        return $this->hasOne(Ward::class, 'id', 'wards_id');
    }
}
