<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserPackageFeature extends Model
{
    protected $table = 'user_package_features';

    protected $fillable = [
        'user_package_id',
        'feature'
    ];

    public function userPackage(): BelongsTo
    {
        return $this->belongsTo(UserPackage::class);
    }
}
