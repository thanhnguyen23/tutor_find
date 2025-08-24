<?php

namespace App\Repositories\Eloquent;

use App\Models\Language;
use App\Repositories\Contracts\LanguageRepositoryInterface;

class LanguageRepository implements LanguageRepositoryInterface
{
    // Triển khai các hàm của Interface
    protected $language;

    public function __construct(Language $language)
    {
        $this->language = $language;
    }

    public function getAll()
    {
        return $this->language->all();
    }
}