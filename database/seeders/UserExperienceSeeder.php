<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\UserExperience;

class UserExperienceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $experiences = [
            // Dr. Minh Nguyen
            [
                'user_id' => User::where('email', 'minh.nguyen@example.com')->first()->id,
                'name' => 'Hanoi-Amsterdam High School',
                'position' => 'Mathematics Teacher',
                'start_date' => '2014-09-01',
                'end_date' => '2018-06-30',
                'description' => 'Taught advanced mathematics to gifted students'
            ],
            [
                'user_id' => User::where('email', 'minh.nguyen@example.com')->first()->id,
                'name' => 'Vietnam National University',
                'position' => 'Lecturer',
                'start_date' => '2020-09-01',
                'end_date' => null,
                'description' => 'Teaching mathematics and physics at university level'
            ],

            // Linh Tran
            [
                'user_id' => User::where('email', 'linh.tran@example.com')->first()->id,
                'name' => 'British Council',
                'position' => 'English Teacher',
                'start_date' => '2016-01-01',
                'end_date' => '2019-12-31',
                'description' => 'IELTS instructor and general English teacher'
            ],
            [
                'user_id' => User::where('email', 'linh.tran@example.com')->first()->id,
                'name' => 'IDP Education',
                'position' => 'Senior IELTS Trainer',
                'start_date' => '2020-01-01',
                'end_date' => null,
                'description' => 'Preparing students for IELTS examination'
            ],

            // Tuan Pham
            [
                'user_id' => User::where('email', 'tuan.pham@example.com')->first()->id,
                'name' => 'Le Quy Don High School',
                'position' => 'Chemistry Teacher',
                'start_date' => '2018-08-01',
                'end_date' => '2020-07-31',
                'description' => 'Teaching chemistry to high school students'
            ],
            [
                'user_id' => User::where('email', 'tuan.pham@example.com')->first()->id,
                'name' => 'Da Nang University of Science and Technology',
                'position' => 'Laboratory Instructor',
                'start_date' => '2020-08-01',
                'end_date' => null,
                'description' => 'Conducting chemistry and biology laboratory sessions'
            ],

            // Hoa Le
            [
                'user_id' => User::where('email', 'hoa.le@example.com')->first()->id,
                'name' => 'Nguyen Hue High School',
                'position' => 'History Teacher',
                'start_date' => '2017-08-01',
                'end_date' => '2020-07-31',
                'description' => 'Teaching history and geography'
            ],
            [
                'user_id' => User::where('email', 'hoa.le@example.com')->first()->id,
                'name' => 'Hue University of Education',
                'position' => 'Lecturer',
                'start_date' => '2020-08-01',
                'end_date' => null,
                'description' => 'Teaching history and cultural studies'
            ],

            // Quang Vo
            [
                'user_id' => User::where('email', 'quang.vo@example.com')->first()->id,
                'name' => 'FPT Software',
                'position' => 'Senior Developer',
                'start_date' => '2016-06-01',
                'end_date' => '2019-12-31',
                'description' => 'Full-stack development and team leadership'
            ],
            [
                'user_id' => User::where('email', 'quang.vo@example.com')->first()->id,
                'name' => 'CodeGym',
                'position' => 'Lead Instructor',
                'start_date' => '2020-01-01',
                'end_date' => null,
                'description' => 'Teaching programming and mentoring students'
            ],

            // Mai Nguyen
            [
                'user_id' => User::where('email', 'mai.nguyen@example.com')->first()->id,
                'name' => 'Japan Foundation',
                'position' => 'Language Instructor',
                'start_date' => '2017-07-01',
                'end_date' => '2020-06-30',
                'description' => 'Teaching Japanese language and culture'
            ],
            [
                'user_id' => User::where('email', 'mai.nguyen@example.com')->first()->id,
                'name' => 'Alliance Française',
                'position' => 'French Teacher',
                'start_date' => '2020-07-01',
                'end_date' => null,
                'description' => 'Teaching French language at all levels'
            ]
        ];

        foreach ($experiences as $experience) {
            UserExperience::create($experience);
        }
    }
}
