<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Subject;
use App\Models\UserSubject;

class UserSubjectSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $userSubjects = [
            // Dr. Minh Nguyen
            [
                'user_id' => User::where('email', 'minh.nguyen@example.com')->first()->id,
                'subject_id' => Subject::where('name', 'Mathematics')->first()->id,
                'years_of_experience' => 8,
                'description' => 'PhD in Applied Mathematics with extensive teaching experience'
            ],
            [
                'user_id' => User::where('email', 'minh.nguyen@example.com')->first()->id,
                'subject_id' => Subject::where('name', 'Physics')->first()->id,
                'years_of_experience' => 6,
                'description' => 'Specialized in theoretical and applied physics'
            ],

            // Linh Tran
            [
                'user_id' => User::where('email', 'linh.tran@example.com')->first()->id,
                'subject_id' => Subject::where('name', 'English')->first()->id,
                'years_of_experience' => 5,
                'description' => 'IELTS trainer and English language specialist'
            ],
            [
                'user_id' => User::where('email', 'linh.tran@example.com')->first()->id,
                'subject_id' => Subject::where('name', 'Literature')->first()->id,
                'years_of_experience' => 4,
                'description' => 'Focus on English literature and creative writing'
            ],

            // Tuan Pham
            [
                'user_id' => User::where('email', 'tuan.pham@example.com')->first()->id,
                'subject_id' => Subject::where('name', 'Chemistry')->first()->id,
                'years_of_experience' => 4,
                'description' => 'Specialized in organic chemistry and lab work'
            ],
            [
                'user_id' => User::where('email', 'tuan.pham@example.com')->first()->id,
                'subject_id' => Subject::where('name', 'Biology')->first()->id,
                'years_of_experience' => 3,
                'description' => 'Focus on molecular biology and genetics'
            ],

            // Hoa Le
            [
                'user_id' => User::where('email', 'hoa.le@example.com')->first()->id,
                'subject_id' => Subject::where('name', 'History')->first()->id,
                'years_of_experience' => 5,
                'description' => 'Expertise in Vietnamese and World History'
            ],
            [
                'user_id' => User::where('email', 'hoa.le@example.com')->first()->id,
                'subject_id' => Subject::where('name', 'Geography')->first()->id,
                'years_of_experience' => 4,
                'description' => 'Specialized in human and physical geography'
            ],

            // Quang Vo
            [
                'user_id' => User::where('email', 'quang.vo@example.com')->first()->id,
                'subject_id' => Subject::where('name', 'Computer Science')->first()->id,
                'years_of_experience' => 7,
                'description' => 'Full-stack developer and programming instructor'
            ],
            [
                'user_id' => User::where('email', 'quang.vo@example.com')->first()->id,
                'subject_id' => Subject::where('name', 'Mathematics')->first()->id,
                'years_of_experience' => 5,
                'description' => 'Focus on discrete mathematics and algorithms'
            ],

            // Mai Nguyen
            [
                'user_id' => User::where('email', 'mai.nguyen@example.com')->first()->id,
                'subject_id' => Subject::where('name', 'Foreign Languages')->first()->id,
                'years_of_experience' => 6,
                'description' => 'Multilingual instructor (French, Japanese, Korean)'
            ],
            [
                'user_id' => User::where('email', 'mai.nguyen@example.com')->first()->id,
                'subject_id' => Subject::where('name', 'English')->first()->id,
                'years_of_experience' => 5,
                'description' => 'TOEFL and IELTS preparation specialist'
            ]
        ];

        foreach ($userSubjects as $userSubject) {
            UserSubject::create($userSubject);
        }
    }
}
