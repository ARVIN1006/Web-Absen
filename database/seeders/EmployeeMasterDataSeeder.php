<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Department;
use App\Models\WorkShift;
use App\Models\Position;

class EmployeeMasterDataSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Seed Positions
        $positions = [
            ['name' => 'Manager', 'salary' => 15000000, 'overtime_rate' => 50000],
            ['name' => 'Supervisor', 'salary' => 10000000, 'overtime_rate' => 40000],
            ['name' => 'Staff IT', 'salary' => 8000000, 'overtime_rate' => 30000],
            ['name' => 'Staff HRD', 'salary' => 7000000, 'overtime_rate' => 25000],
            ['name' => 'Staff Finance', 'salary' => 7500000, 'overtime_rate' => 25000],
            ['name' => 'Marketing', 'salary' => 6500000, 'overtime_rate' => 20000],
            ['name' => 'Sales', 'salary' => 6000000, 'overtime_rate' => 20000],
            ['name' => 'Operator Production', 'salary' => 5500000, 'overtime_rate' => 15000],
            ['name' => 'Security', 'salary' => 5200000, 'overtime_rate' => 15000],
            ['name' => 'Cleaning Service', 'salary' => 4800000, 'overtime_rate' => 10000],
        ];
        foreach ($positions as $pos) {
            \App\Models\Position::updateOrCreate(['name' => $pos['name']], $pos);
        }

        // 2. Seed Departments
        $departments = [
            ['code' => 'IT', 'name' => 'Information Technology', 'description' => 'Department for IT support and development'],
            ['code' => 'HRD', 'name' => 'Human Resource Development', 'description' => 'Department for personnel management'],
            ['code' => 'FIN', 'name' => 'Finance', 'description' => 'Department for financial management'],
            ['code' => 'PROD', 'name' => 'Production', 'description' => 'Department for factory production'],
            ['code' => 'MKT', 'name' => 'Marketing', 'description' => 'Department for marketing and sales'],
        ];
        foreach ($departments as $dept) {
            Department::firstOrCreate(['code' => $dept['code']], $dept);
        }

        // 3. Seed Work Shifts
        $shifts = [
            ['name' => 'Shift Pagi', 'clock_in_time' => '08:00', 'clock_out_time' => '17:00', 'late_tolerance_minutes' => 15, 'is_default' => true],
            ['name' => 'Shift Sore', 'clock_in_time' => '14:00', 'clock_out_time' => '22:00', 'late_tolerance_minutes' => 15, 'is_default' => false],
            ['name' => 'Shift Malam', 'clock_in_time' => '22:00', 'clock_out_time' => '06:00', 'late_tolerance_minutes' => 15, 'is_default' => false],
        ];
        foreach ($shifts as $shift) {
            WorkShift::firstOrCreate(['name' => $shift['name']], $shift);
        }
    }
}
