<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create tutors
        $tutors = [
            [
                'name' => 'Dr. Minh Nguyen',
                'email' => 'minh.nguyen@example.com',
                'phone' => '0901234567',
                'address' => 'Hanoi',
                'avatar' => null,
                'price' => 250000,
                'is_active' => true,
            ],
            [
                'name' => 'Linh Tran',
                'email' => 'linh.tran@example.com',
                'phone' => '0901234568',
                'address' => 'Ho Chi Minh City',
                'avatar' => null,
                'price' => 200000,
                'is_active' => true,
            ],
            [
                'name' => 'Tuan Pham',
                'email' => 'tuan.pham@example.com',
                'phone' => '0901234569',
                'address' => 'Da Nang',
                'avatar' => null,
                'price' => 220000,
                'is_active' => true,
            ],
            [
                'name' => 'Hoa Le',
                'email' => 'hoa.le@example.com',
                'phone' => '0901234570',
                'address' => 'Hue',
                'avatar' => null,
                'price' => 180000,
                'is_active' => true,
            ],
            [
                'name' => 'Quang Vo',
                'email' => 'quang.vo@example.com',
                'phone' => '0901234571',
                'address' => 'Hanoi',
                'avatar' => null,
                'price' => 300000,
                'is_active' => true,
            ],
            [
                'name' => 'Mai Nguyen',
                'email' => 'mai.nguyen@example.com',
                'phone' => '0901234572',
                'address' => 'Ho Chi Minh City',
                'avatar' => null,
                'price' => 250000,
                'is_active' => true,
            ]
        ];

        // Create students
        $students = [
            [
                'name' => 'Lan Pham',
                'email' => 'lan.pham@example.com',
                'phone' => '0901234573',
                'address' => 'Hanoi',
                'avatar' => null,
                'is_active' => true
            ],
            [
                'name' => 'Vinh Tran',
                'email' => 'vinh.tran@example.com',
                'phone' => '0901234574',
                'address' => 'Ho Chi Minh City',
                'avatar' => null,
                'is_active' => true
            ],
            [
                'name' => 'Huong Nguyen',
                'email' => 'huong.nguyen@example.com',
                'phone' => '0901234575',
                'address' => 'Da Nang',
                'avatar' => null,
                'is_active' => true
            ]
        ];

        // Insert tutors
        foreach ($tutors as $tutor) {
            User::create($tutor);
        }

        // Insert students
        foreach ($students as $student) {
            User::create($student);
        }
    }
}
