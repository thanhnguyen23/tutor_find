<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\UserEducation;

class UserEducationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $educations = [
            // Dr. Minh Nguyen
            [
                'user_id' => User::where('email', 'minh.nguyen@example.com')->first()->id,
                'school_name' => 'Hanoi University of Science',
                'major' => 'Mathematics',
                'start_date' => '2010-09-01',
                'end_date' => '2014-06-15',
                'description' => 'Graduated with High Honors'
            ],
            [
                'user_id' => User::where('email', 'minh.nguyen@example.com')->first()->id,
                'school_name' => 'Vietnam National University',
                'major' => 'Applied Mathematics',
                'start_date' => '2014-09-01',
                'end_date' => '2016-06-15',
                'description' => 'Research focus on Mathematical Modeling'
            ],
            [
                'user_id' => User::where('email', 'minh.nguyen@example.com')->first()->id,
                'school_name' => 'Vietnam National University',
                'major' => 'Applied Mathematics',
                'start_date' => '2016-09-01',
                'end_date' => '2020-06-15',
                'description' => 'Dissertation on Advanced Mathematical Analysis'
            ],

            // Linh Tran
            [
                'user_id' => User::where('email', 'linh.tran@example.com')->first()->id,
                'school_name' => 'University of Languages and International Studies',
                'major' => 'English Language and Literature',
                'start_date' => '2012-09-01',
                'end_date' => '2016-06-15',
                'description' => 'IELTS 8.5, CELTA Certified'
            ],
            [
                'user_id' => User::where('email', 'linh.tran@example.com')->first()->id,
                'school_name' => 'University of Melbourne',
                'major' => 'Teaching English to Speakers of Other Languages',
                'start_date' => '2016-09-01',
                'end_date' => '2018-06-15',
                'description' => 'Specialized in Language Assessment'
            ],

            // Tuan Pham
            [
                'user_id' => User::where('email', 'tuan.pham@example.com')->first()->id,
                'school_name' => 'Ho Chi Minh City University of Science',
                'major' => 'Chemistry',
                'start_date' => '2014-09-01',
                'end_date' => '2018-06-15',
                'description' => 'Research Assistant in Organic Chemistry Lab'
            ],
            [
                'user_id' => User::where('email', 'tuan.pham@example.com')->first()->id,
                'school_name' => 'Ho Chi Minh City University of Science',
                'major' => 'Biochemistry',
                'start_date' => '2018-09-01',
                'end_date' => '2020-06-15',
                'description' => 'Focus on Molecular Biology'
            ],

            // Hoa Le
            [
                'user_id' => User::where('email', 'hoa.le@example.com')->first()->id,
                'school_name' => 'University of Social Sciences and Humanities',
                'major' => 'History',
                'start_date' => '2013-09-01',
                'end_date' => '2017-06-15',
                'description' => 'Minor in Geography'
            ],
            [
                'user_id' => User::where('email', 'hoa.le@example.com')->first()->id,
                'school_name' => 'University of Social Sciences and Humanities',
                'major' => 'Vietnamese Studies',
                'start_date' => '2017-09-01',
                'end_date' => '2019-06-15',
                'description' => 'Research on Vietnamese History and Culture'
            ],

            // Quang Vo
            [
                'user_id' => User::where('email', 'quang.vo@example.com')->first()->id,
                'school_name' => 'FPT University',
                'major' => 'Software Engineering',
                'start_date' => '2012-09-01',
                'end_date' => '2016-06-15',
                'description' => 'Full Stack Development Focus'
            ],
            [
                'user_id' => User::where('email', 'quang.vo@example.com')->first()->id,
                'school_name' => 'RMIT University',
                'major' => 'Computer Science',
                'start_date' => '2016-09-01',
                'end_date' => '2018-06-15',
                'description' => 'Specialized in AI and Machine Learning'
            ],

            // Mai Nguyen
            [
                'user_id' => User::where('email', 'mai.nguyen@example.com')->first()->id,
                'school_name' => 'University of Languages and International Studies',
                'major' => 'International Studies',
                'start_date' => '2013-09-01',
                'end_date' => '2017-06-15',
                'description' => 'Triple Major in English, French, and Japanese'
            ],
            [
                'user_id' => User::where('email', 'mai.nguyen@example.com')->first()->id,
                'school_name' => 'Waseda University',
                'major' => 'International Relations',
                'start_date' => '2017-09-01',
                'end_date' => '2019-06-15',
                'description' => 'JLPT N1, DELF C1, TOPIK Level 6'
            ]
        ];

        foreach ($educations as $education) {
            UserEducation::create($education);
        }
    }
}
