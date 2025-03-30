<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\UserWeeklyTimeSlot;

class UserWeeklyTimeSlotSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::all();
        $weekDays = [1, 2, 3, 4, 5, 6]; // Monday to Saturday

        foreach ($users as $user) {
            // Morning slots (8:00 AM - 12:00 PM) - Monday to Saturday
            foreach ($weekDays as $day) {
                for ($hour = 8; $hour < 12; $hour++) {
                    UserWeeklyTimeSlot::create([
                        'user_id' => $user->id,
                        'day_of_week' => $day,
                        'start_time' => sprintf('%02d:00:00', $hour),
                        'end_time' => sprintf('%02d:00:00', $hour + 1),
                        'is_active' => rand(0, 1),
                    ]);
                }
            }

            // Afternoon slots (2:00 PM - 6:00 PM) - Monday to Friday
            for ($day = 1; $day <= 5; $day++) {
                for ($hour = 14; $hour < 18; $hour++) {
                    UserWeeklyTimeSlot::create([
                        'user_id' => $user->id,
                        'day_of_week' => $day,
                        'start_time' => sprintf('%02d:00:00', $hour),
                        'end_time' => sprintf('%02d:00:00', $hour + 1),
                        'is_active' => rand(0, 1),
                    ]);
                }
            }

            // Evening slots (7:00 PM - 9:00 PM) - Monday to Friday
            for ($day = 1; $day <= 5; $day++) {
                for ($hour = 19; $hour < 21; $hour++) {
                    UserWeeklyTimeSlot::create([
                        'user_id' => $user->id,
                        'day_of_week' => $day,
                        'start_time' => sprintf('%02d:00:00', $hour),
                        'end_time' => sprintf('%02d:00:00', $hour + 1),
                        'is_active' => rand(0, 1),
                    ]);
                }
            }
        }
    }
}
