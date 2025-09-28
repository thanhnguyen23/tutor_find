<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;

class ConfigService
{
    public function getByKey($key)
    {
        return Cache::get("config:$key");
    }
}
