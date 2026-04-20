<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Attendance;
use App\Models\User;
use Carbon\Carbon;

class AttendanceDemoSeeder extends Seeder
{
    public function run(): void
    {
        $employees = User::where('role', 'employee')->get();
        if ($employees->isEmpty()) return;

        // Loop for the last 14 days to have a rich chart
        for ($i = 13; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            
            // Randomly pick some employees to be present each day
            $dailyEmployees = $employees->random(rand(min(2, count($employees)), count($employees)));

            foreach ($dailyEmployees as $emp) {
                // Check-in (between 07:00 and 09:00)
                Attendance::create([
                    'user_id' => $emp->id,
                    'type' => 'in',
                    'image_path' => null, // Placeholder
                    'latitude' => -6.2088,
                    'longitude' => 106.8456,
                    'status' => 'valid',
                    'created_at' => $date->copy()->setHour(rand(7, 8))->setMinute(rand(0, 59)),
                ]);

                // Check-out (between 16:00 and 18:00)
                // 80% chance they checked out
                if (rand(1, 10) <= 8) {
                    Attendance::create([
                        'user_id' => $emp->id,
                        'type' => 'out',
                        'image_path' => null,
                        'latitude' => -6.2088,
                        'longitude' => 106.8456,
                        'status' => 'valid',
                        'created_at' => $date->copy()->setHour(rand(16, 17))->setMinute(rand(0, 59)),
                    ]);
                }
            }
        }

        // Create some pending leave requests
        $leaveTypes = \App\Models\LeaveType::all();
        if ($leaveTypes->isNotEmpty()) {
            foreach ($employees->take(3) as $emp) {
                \App\Models\LeaveRequest::create([
                    'user_id' => $emp->id,
                    'leave_type_id' => $leaveTypes->random()->id,
                    'start_date' => Carbon::today()->addDays(rand(1, 5)),
                    'end_date' => Carbon::today()->addDays(rand(6, 10)),
                    'reason' => 'Keperluan keluarga mendadak.',
                    'status' => 'pending',
                    'total_days' => 5,
                ]);
            }
        }
    }
}
