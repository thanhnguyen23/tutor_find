<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CategorySubject extends Model
{
    use HasFactory;

    protected $table = 'category_subjects';

    protected $fillable = ['name', 'slug', 'description'];

    public function subjects()
    {
        return $this->hasMany(Subject::class);
    }
}
