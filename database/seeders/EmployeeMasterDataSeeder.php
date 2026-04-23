<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Branch;
use App\Models\Department;
use App\Models\EmploymentType;
use App\Models\WorkShift;
use App\Models\Position;
use App\Models\LeaveType;

class EmployeeMasterDataSeeder extends Seeder
{
    public function run(): void
    {
        $headOffice = Branch::updateOrCreate(
            ['code' => 'JKT-HO'],
            [
                'name' => 'Jakarta Head Office',
                'phone_number' => '021500100',
                'email' => 'ho@demo-hris.local',
                'address' => 'Jl. Jenderal Sudirman Kav. 88',
                'city' => 'Jakarta Selatan',
                'province' => 'DKI Jakarta',
                'postal_code' => '12190',
                'is_head_office' => true,
                'is_active' => true,
            ]
        );

        EmploymentType::updateOrCreate(
            ['code' => 'PERM'],
            [
                'name' => 'Karyawan Tetap',
                'category' => 'permanent',
                'description' => 'Karyawan tetap penuh waktu',
                'is_active' => true,
            ]
        );

        EmploymentType::updateOrCreate(
            ['code' => 'CONT'],
            [
                'name' => 'Karyawan Kontrak',
                'category' => 'contract',
                'description' => 'Karyawan kontrak dengan masa kerja tertentu',
                'is_active' => true,
            ]
        );

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
            \App\Models\Position::updateOrCreate(
                ['name' => $pos['name']],
                [
                    ...$pos,
                    'code' => str($pos['name'])->upper()->replace(' ', '_')->toString(),
                    'grade' => str_contains($pos['name'], 'Manager') ? 'M1' : 'S1',
                    'allowance' => $pos['salary'] * 0.15,
                    'is_active' => true,
                ]
            );
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
            Department::updateOrCreate(
                ['code' => $dept['code']],
                [
                    ...$dept,
                    'branch_id' => $headOffice->id,
                    'is_active' => true,
                ]
            );
        }

        // 3. Seed Work Shifts
        $shifts = [
            ['name' => 'Shift Pagi', 'clock_in_time' => '08:00', 'clock_out_time' => '17:00', 'late_tolerance_minutes' => 15, 'is_default' => true],
            ['name' => 'Shift Sore', 'clock_in_time' => '14:00', 'clock_out_time' => '22:00', 'late_tolerance_minutes' => 15, 'is_default' => false],
            ['name' => 'Shift Malam', 'clock_in_time' => '22:00', 'clock_out_time' => '06:00', 'late_tolerance_minutes' => 15, 'is_default' => false],
        ];
        foreach ($shifts as $shift) {
            WorkShift::updateOrCreate(
                ['name' => $shift['name']],
                [
                    ...$shift,
                    'branch_id' => $headOffice->id,
                    'work_days' => ['monday', 'tuesday', 'wednesday', 'thursday', 'friday'],
                    'break_start_time' => '12:00',
                    'break_end_time' => '13:00',
                ]
            );
        }

        // 4. Seed Leave Types
        $leaveTypes = [
            ['name' => 'Cuti Tahunan', 'max_days_per_year' => 12, 'requires_attachment' => false, 'description' => 'Cuti tahunan untuk karyawan tetap'],
            ['name' => 'Cuti Sakit', 'max_days_per_year' => 14, 'requires_attachment' => true, 'description' => 'Cuti sakit dengan surat keterangan dokter'],
            ['name' => 'Cuti Melahirkan', 'max_days_per_year' => 90, 'requires_attachment' => true, 'description' => 'Cuti melahirkan untuk karyawan wanita'],
            ['name' => 'Cuti Menikah', 'max_days_per_year' => 3, 'requires_attachment' => true, 'description' => 'Cuti untuk pernikahan karyawan'],
            ['name' => 'Cuti Duka', 'max_days_per_year' => 3, 'requires_attachment' => false, 'description' => 'Cuti karena keluarga meninggal dunia'],
        ];
        foreach ($leaveTypes as $lt) {
            LeaveType::updateOrCreate(
                ['name' => $lt['name']],
                [
                    ...$lt,
                    'code' => str($lt['name'])->upper()->replace(' ', '_')->toString(),
                    'is_paid' => !str_contains($lt['name'], 'Duka'),
                    'requires_balance' => !str_contains($lt['name'], 'Sakit'),
                    'is_active' => true,
                ]
            );
        }
    }
}
