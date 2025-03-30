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

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'role',
        'first_name',
        'last_name',
        'email',
        'password',
        'phone',
        'address',
        'about_you',
        'student_number',
        'lesson_number',
        'price',
        'avatar',
        'is_active',
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    protected $appends = [
        'full_name',
    ];

    public function roleName()
    {
        return $this->role == 1 ? 'Gia sư' : 'Học sinh';
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

}
