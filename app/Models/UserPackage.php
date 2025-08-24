<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class UserPackage extends Model
{
    protected $table = 'user_packages';

    protected $fillable = [
        'user_id',
        'subject_id',
        'level_id',
        'name',
        'description',
        'number_of_lessons',
        'duration',
        'price'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    public function level(): BelongsTo
    {
        return $this->belongsTo(EducationLevel::class, 'level_id');
    }

    public function features(): HasMany
    {
        return $this->hasMany(UserPackageFeature::class);
    }
}
