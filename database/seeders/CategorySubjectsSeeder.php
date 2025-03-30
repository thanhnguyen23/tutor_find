<?php

namespace Database\Seeders;

use App\Models\CategorySubject;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategorySubjectsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            ['id' => 1, 'slug' => 'khoa-hoc-tu-nhien', 'name' => 'Khoa học tự nhiên', 'description' => 'Các môn học về toán, vật lý, hóa học, sinh học'],
            ['id' => 2, 'slug' => 'khoa-hoc-xa-hoi', 'name' => 'Khoa học xã hội', 'description' => 'Các môn học về lịch sử, địa lý, giáo dục công dân'],
            ['id' => 3, 'slug' => 'ngon-ngu', 'name' => 'Ngôn ngữ', 'description' => 'Các môn học về ngôn ngữ và văn học'],
            ['id' => 4, 'slug' => 'cong-nghe', 'name' => 'Công nghệ', 'description' => 'Các môn học về công nghệ và tin học'],
            ['id' => 5, 'slug' => 'nghe-thuat', 'name' => 'Nghệ thuật', 'description' => 'Các môn học về mỹ thuật, âm nhạc, thiết kế'],
            ['id' => 6, 'slug' => 'the-duc', 'name' => 'Thể dục', 'description' => 'Các môn học về thể thao và sức khỏe'],
            ['id' => 7, 'slug' => 'ky-nang', 'name' => 'Kỹ năng', 'description' => 'Các môn học về kỹ năng sống và trải nghiệm'],
            ['id' => 8, 'slug' => 'kinh-te', 'name' => 'Kinh tế', 'description' => 'Các môn học về kinh tế và quản trị'],
            ['id' => 9, 'slug' => 'ky-thuat', 'name' => 'Kỹ thuật', 'description' => 'Các môn học về kỹ thuật và công trình'],
            ['id' => 10, 'slug' => 'y-hoc', 'name' => 'Y học', 'description' => 'Các môn học về y khoa và sức khỏe'],
            ['id' => 11, 'slug' => 'luat', 'name' => 'Luật', 'description' => 'Các môn học về pháp luật'],
            ['id' => 12, 'slug' => 'giao-duc', 'name' => 'Giáo dục', 'description' => 'Các môn học về sư phạm và giáo dục'],
        ];

        foreach ($categories as $category) {
            CategorySubject::create($category);
        }
    }
}
