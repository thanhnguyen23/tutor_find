<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EducationLevel extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'slug',
        'image',
    ];

    public function userEducationLevels()
    {
        return $this->hasMany(UserEducationLevel::class);
    }

    public function userPackages()
    {
        return $this->hasMany(UserPackage::class);
    }
}
