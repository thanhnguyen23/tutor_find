<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Packages;

class PackageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Packages::create([
            'package_id' => 'free',
            'name' => 'Gói Miễn Phí',
            'price' => 0,
            'max_contacts' => 3,
            'months' => null,
            'years' => null,
            'is_popular' => false,
            'icon' => 'fa-solid fa-gift',
            'discount_percent' => 0,
            'features' => json_encode([
                ['icon' => 'fa-solid fa-infinity', 'text' => 'Tối đa hiển thị 1 liên hệ từ học sinh'],
                ['icon' => 'fa-solid fa-ranking-star', 'text' => 'Hiển thị cơ bản trong tìm kiếm'],
                ['icon' => 'fa-regular fa-infinity', 'text' => 'Không giới hạn thời gian sử dụng']
            ]),
            'is_active' => true
        ]);

        // Gói theo tháng
        Packages::create([
            'package_id' => 'standard_monthly',
            'name' => 'Gói Tiêu Chuẩn',
            'price' => 100000,
            'max_contacts' => 15,
            'months' => 1,
            'years' => null,
            'is_popular' => true,
            'icon' => 'fa-solid fa-star',
            'discount_percent' => 0,
            'features' => json_encode([
                ['icon' => 'fa-solid fa-infinity', 'text' => 'Tối đa hiển thị 15 liên hệ từ học sinh'],
                ['icon' => 'fa-solid fa-ranking-star', 'text' => 'Ưu tiên hiển thị trong tìm kiếm'],
                ['icon' => 'fa-regular fa-clock', 'text' => 'Thời hạn 1 tháng']
            ]),
            'is_active' => true
        ]);

        Packages::create([
            'package_id' => 'premium_monthly',
            'name' => 'Gói Nâng Cao',
            'price' => 200000,
            'max_contacts' => null,
            'months' => 1,
            'years' => null,
            'is_popular' => false,
            'icon' => 'fa-solid fa-crown',
            'discount_percent' => 0,
            'features' => json_encode([
                ['icon' => 'fa-solid fa-infinity','text' => 'Không giới hạn liên hệ từ học sinh'],
                ['icon' => 'fa-solid fa-ranking-star', 'text' => 'Ưu tiên cao nhất trong tìm kiếm'],
                ['icon' => 'fa-regular fa-clock', 'text' => 'Thời hạn 1 tháng'],
                ['icon' => 'fa-solid fa-certificate', 'text' => 'Huy hiệu "Gia sư chuyên nghiệp"']
            ]),
            'is_active' => true
        ]);

        // Gói theo năm
        Packages::create([
            'package_id' => 'standard_yearly',
            'name' => 'Gói Tiêu Chuẩn (Năm)',
            'price' => 1200000,
            'max_contacts' => 15,
            'months' => null,
            'years' => 1,
            'is_popular' => true,
            'icon' => 'fa-solid fa-star',
            'discount_percent' => 20,
            'features' => json_encode([
                ['icon' => 'fa-solid fa-infinity', 'text' => 'Tối đa hiển thị 15 liên hệ từ học sinh'],
                ['icon' => 'fa-solid fa-ranking-star', 'text' => 'Ưu tiên hiển thị trong tìm kiếm'],
                ['icon' => 'fa-regular fa-clock', 'text' => 'Thời hạn 1 năm'],
                ['icon' => 'fa-solid fa-percent', 'text' => 'Tiết kiệm 20% so với gói tháng']
            ]),
            'is_active' => true
        ]);

        Packages::create([
            'package_id' => 'premium_yearly',
            'name' => 'Gói Nâng Cao (Năm)',
            'price' => 2400000,
            'max_contacts' => null,
            'months' => null,
            'years' => 1,
            'is_popular' => false,
            'icon' => 'fa-solid fa-crown',
            'discount_percent' => 20,
            'features' => json_encode([
                ['icon' => 'fa-solid fa-infinity','text' => 'Không giới hạn liên hệ từ học sinh'],
                ['icon' => 'fa-solid fa-ranking-star', 'text' => 'Ưu tiên cao nhất trong tìm kiếm'],
                ['icon' => 'fa-regular fa-clock', 'text' => 'Thời hạn 1 năm'],
                ['icon' => 'fa-solid fa-certificate', 'text' => 'Huy hiệu "Gia sư chuyên nghiệp"'],
                ['icon' => 'fa-solid fa-percent', 'text' => 'Tiết kiệm 20% so với gói tháng']
            ]),
            'is_active' => true
        ]);
    }
}
