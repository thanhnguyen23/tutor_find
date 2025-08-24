<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserLanguage extends Model
{
    use HasFactory;
    protected $fillable = ['user_id', 'language_id', 'level_language_id', 'is_native'];

    protected $casts = [
        'is_native' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function language()
    {
        return $this->belongsTo(Language::class, 'language_id', 'id');
    }

    public function level_language()
    {
        return $this->belongsTo(LevelLanguage::class, 'level_language_id', 'id');
    }
}
