<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Province extends Model
{
    use HasFactory;

    protected $table = 'ref_location_provinces';

    protected $fillable = [
        'name',
        'slug',
        'type',
    ];

    public function districts()
    {
        return $this->hasMany(District::class);
    }
}
