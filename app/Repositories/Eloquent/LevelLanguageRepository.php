<?php

namespace App\Repositories\Eloquent;

use App\Models\levelLanguage;
use App\Repositories\Contracts\LevelLanguageRepositoryInterface;

class LevelLanguageRepository implements LevelLanguageRepositoryInterface
{
    // Triển khai các hàm của Interface
    protected $levelLanguage;

    public function __construct(levelLanguage $levelLanguage)
    {
        $this->levelLanguage = $levelLanguage;
    }

    public function getAll()
    {
        return $this->levelLanguage->all();
    }
}