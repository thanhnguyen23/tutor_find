<?php

namespace Database\Seeders;

use App\Models\levelLanguage as ModelsLevelLanguage;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class levelLanguage extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $levels = [
            ['name' => 'Sơ cấp (A1)'],
            ['name' => 'Sơ trung cấp (A2)'],
            ['name' => 'Trung cấp (B1)'],
            ['name' => 'Trung cao cấp (B2)'],
            ['name' => 'Cao cấp (C1)'],
            ['name' => 'Thành thạo (C2)']
        ];

        foreach ($levels as $level) {
            ModelsLevelLanguage::create($level);
        }
    }
}
