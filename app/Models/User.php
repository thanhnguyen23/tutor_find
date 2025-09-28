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
        'education_level_id',
        'reminder_emails_sent',
        'last_reminder_sent_at',
        'profile_completed',
        'avatar',
        'is_active',
        'referral_link',
        'free_study',
        'is_free_study',
        'tutor_session_id',
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
        return $this->hasMany(UserLanguage::class, 'uid', 'uid');
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

    public function bookings()
    {
        return $this->hasMany(UserBooking::class, 'uid', 'uid');
    }

    public function hasFreeTrial(): bool
    {
        return (bool) $this->is_free_study;
    }

    public function hasFreeTrialLeft(): bool
    {
        // Nếu gia sư không bật trial thì coi như không có
        if ($this->is_free_study != 1) {
            return false;
        }

        // Nếu user đã từng có booking trial free thì không còn nữa
        return !$this->bookings()
            ->where('type', 'trial')
            ->where('is_free', 1)
            ->exists();
    }

    public function tutorSession()
    {
        return $this->belongsTo(TutorSession::class, 'tutor_session_id');
    }
}
