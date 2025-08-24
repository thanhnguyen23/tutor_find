<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Language extends Model
{
    use HasFactory;
    protected $fillable = ['name', 'emoji'];

    public function users_languages()
    {
        return $this->belongsToMany(User::class, 'user_language');
    }
}
