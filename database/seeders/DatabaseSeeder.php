<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Branch;
use App\Models\Department;
use App\Models\EmploymentType;
use App\Models\EmployeeProfile;
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
        $branch = Branch::where('code', 'JKT-HO')->first();
        $employmentType = EmploymentType::where('code', 'PERM')->first();

        // 2. Create Admin User
        $admin = User::updateOrCreate(['email' => 'admin@gmail.com'], [
            'name'  => 'Administrator',
            'password' => bcrypt('password'),
            'role' => 'admin',
            'is_active' => true,
            'nik' => '1234567890123456',
            'phone_number' => '08123456789',
            'branch_id' => $branch?->id,
            'employment_type_id' => $employmentType?->id,
            'department_id' => $itDept->id ?? null,
            'position_id' => $managerPos->id ?? null,
            'joined_at' => now(),
        ]);

        // 3. Create Test Employee User
        $employee = User::updateOrCreate(['email' => 'test@gmail.com'], [
            'name'  => 'Test Employee',
            'password' => bcrypt('password'),
            'role' => 'employee',
            'is_active' => true,
            'nik' => '9876543210987654',
            'phone_number' => '08987654321',
            'gender' => 'male',
            'branch_id' => $branch?->id,
            'employment_type_id' => $employmentType?->id,
            'manager_id' => $admin->id,
            'department_id' => $itDept->id ?? null,
            'position_id' => $staffPos->id ?? null,
            'work_shift_id' => $defaultShift->id ?? null,
            'joined_at' => now(),
            'address' => 'Jl. Testing No. 123, Jakarta Selatan',
        ]);

        EmployeeProfile::updateOrCreate(
            ['user_id' => $admin->id],
            [
                'employee_code' => 'ADM-0001',
                'identity_number' => $admin->nik,
                'phone_number' => $admin->phone_number,
                'personal_email' => $admin->email,
                'joined_at' => now()->toDateString(),
                'employment_status' => 'active',
                'bank_name' => 'Bank Central Asia',
                'bank_account_number' => '123450001',
                'bank_account_name' => $admin->name,
            ]
        );

        EmployeeProfile::updateOrCreate(
            ['user_id' => $employee->id],
            [
                'employee_code' => 'EMP-0001',
                'identity_number' => $employee->nik,
                'phone_number' => $employee->phone_number,
                'personal_email' => $employee->email,
                'joined_at' => now()->toDateString(),
                'employment_status' => 'active',
                'bank_name' => 'Bank Mandiri',
                'bank_account_number' => '123450002',
                'bank_account_name' => $employee->name,
            ]
        );

        // 4. Seed Company Settings & Demo Data
        $this->call([
            CompanySettingSeeder::class,
            DemoDataSeeder::class,
            AttendanceDemoSeeder::class,
        ]);
    }
}
