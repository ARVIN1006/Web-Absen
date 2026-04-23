<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Attendance;
use App\Models\Location;
use App\Models\User;
use Carbon\Carbon;

class AttendanceDemoSeeder extends Seeder
{
    public function run(): void
    {
        $employees = User::where('role', 'employee')->get();
        if ($employees->isEmpty()) return;
        $location = Location::where('is_active', true)->first();
        if (!$location) return;

        // Loop for the last 14 days to have a rich chart
        for ($i = 13; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            
            // Randomly pick some employees to be present each day
            $dailyEmployees = $employees->random(rand(min(2, count($employees)), count($employees)));

            foreach ($dailyEmployees as $emp) {
                // Check-in (between 07:00 and 09:00)
                Attendance::create([
                    'user_id' => $emp->id,
                    'attendance_date' => $date->toDateString(),
                    'type' => 'in',
                    'check_in_at' => $date->copy()->setHour(rand(7, 8))->setMinute(rand(0, 59)),
                    'image_path' => 'demo/attendance-placeholder.jpg',
                    'latitude' => -6.2088,
                    'longitude' => 106.8456,
                    'location_id' => $location->id,
                    'status' => 'valid',
                    'face_verified' => true,
                    'face_match_score' => rand(88, 100),
                    'distance_meters' => rand(0, 25),
                    'late_status' => rand(1, 10) > 8 ? 'late' : 'on_time',
                    'late_minutes' => rand(0, 12),
                    'notes' => 'Seeded demo attendance check-in',
                    'created_at' => $date->copy()->setHour(8)->setMinute(0),
                ]);

                // Check-out (between 16:00 and 18:00)
                // 80% chance they checked out
                if (rand(1, 10) <= 8) {
                    Attendance::create([
                        'user_id' => $emp->id,
                        'attendance_date' => $date->toDateString(),
                        'type' => 'out',
                        'check_out_at' => $date->copy()->setHour(rand(16, 17))->setMinute(rand(0, 59)),
                        'image_path' => 'demo/attendance-placeholder.jpg',
                        'latitude' => -6.2088,
                        'longitude' => 106.8456,
                        'location_id' => $location->id,
                        'status' => 'valid',
                        'face_verified' => true,
                        'face_match_score' => rand(88, 100),
                        'distance_meters' => rand(0, 25),
                        'overtime_minutes' => rand(0, 45),
                        'notes' => 'Seeded demo attendance check-out',
                        'created_at' => $date->copy()->setHour(17)->setMinute(0),
                    ]);
                }
            }
        }
    }
}
