<?php

namespace Database\Seeders;

use App\Models\EducationLevel;
use App\Models\Level;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class EducationLevelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $educationLevels = [
            [
                'name' => 'Mẫu giáo',
                'slug' => 'mau-giao',
                'image' => 'mau-giao.jpg',
            ],
            [
                'name' => 'Tiểu học',
                'slug' => 'tieu-hoc',
                'image' => 'tiểu-học.jpg',
            ],
            [
                'name' => 'THCS',
                'slug' => 'thcs',
                'image' => 'thcs.jpg',
            ],
            [
                'name' => 'THPT',
                'slug' => 'thpt',
                'image' => 'thpt.jpg',
            ],
            [
                'name' => 'Đại học',
                'slug' => 'dai-hoc',
                'image' => 'dai-hoc.jpg',
            ],
            [
                'name' => 'Đã tốt nghiệp',
                'slug' => 'da-tot-nghep',
                'image' => 'da-tot-nghep.jpg',
            ],

        ];

        foreach ($educationLevels as $educationLevel) {
            EducationLevel::create($educationLevel);
        }
    }
}
