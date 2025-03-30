<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\UserTimeSlot;
use Carbon\Carbon;

class UserTimeSlotSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::all();
        $startDate = Carbon::now()->startOfWeek();
        $endDate = $startDate->copy()->addDays(30);

        foreach ($users as $user) {
            $currentDate = $startDate->copy();

            while ($currentDate <= $endDate) {
                // Morning slots (8:00 AM - 12:00 PM)
                if ($currentDate->dayOfWeek !== Carbon::SUNDAY) {
                    for ($hour = 8; $hour < 12; $hour++) {
                        UserTimeSlot::create([
                            'user_id' => $user->id,
                            'start_time' => sprintf('%02d:00:00', $hour),
                            'end_time' => sprintf('%02d:00:00', $hour + 1),
                            'is_active' => rand(0, 1),
                        ]);
                    }
                }

                // Afternoon slots (2:00 PM - 6:00 PM)
                if ($currentDate->dayOfWeek !== Carbon::SATURDAY) {
                    for ($hour = 14; $hour < 18; $hour++) {
                        UserTimeSlot::create([
                            'user_id' => $user->id,
                            'start_time' => sprintf('%02d:00:00', $hour),
                            'end_time' => sprintf('%02d:00:00', $hour + 1),
                            'is_active' => rand(0, 1),
                        ]);
                    }
                }

                // Evening slots (7:00 PM - 9:00 PM)
                if ($currentDate->dayOfWeek !== Carbon::SUNDAY) {
                    for ($hour = 19; $hour < 21; $hour++) {
                        UserTimeSlot::create([
                            'user_id' => $user->id,
                            'start_time' => sprintf('%02d:00:00', $hour),
                            'end_time' => sprintf('%02d:00:00', $hour + 1),
                            'is_active' => rand(0, 1),
                        ]);
                    }
                }

                $currentDate->addDay();
            }
        }
    }
}
