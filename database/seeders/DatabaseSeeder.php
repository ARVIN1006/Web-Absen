<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Department;
use App\Models\Position;
use App\Models\WorkShift;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Seed Master Data First
        $this->call([
            EmployeeMasterDataSeeder::class,
        ]);

        // Get some IDs for user seeding
        $itDept = Department::where('code', 'IT')->first();
        $staffPos = Position::where('name', 'Staff IT')->first();
        $managerPos = Position::where('name', 'Manager')->first();
        $defaultShift = WorkShift::where('is_default', true)->first();

        // 2. Create Admin User
        User::updateOrCreate(['email' => 'admin@gmail.com'], [
            'name'  => 'Administrator',
            'password' => bcrypt('password'),
            'role' => 'admin',
            'nik' => '1234567890123456',
            'phone_number' => '08123456789',
            'department_id' => $itDept->id ?? null,
            'position_id' => $managerPos->id ?? null,
            'joined_at' => now(),
        ]);

        // 3. Create Test Employee User
        User::updateOrCreate(['email' => 'test@gmail.com'], [
            'name'  => 'Test Employee',
            'password' => bcrypt('password'),
            'role' => 'employee',
            'nik' => '9876543210987654',
            'phone_number' => '08987654321',
            'gender' => 'male',
            'department_id' => $itDept->id ?? null,
            'position_id' => $staffPos->id ?? null,
            'work_shift_id' => $defaultShift->id ?? null,
            'joined_at' => now(),
            'address' => 'Jl. Testing No. 123, Jakarta Selatan',
        ]);

        // 4. Seed other settings if they exist
        if (class_exists(CompanySettingSeeder::class)) {
            $this->call(CompanySettingSeeder::class);
        }
    }
}
