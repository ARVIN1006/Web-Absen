<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DepartmentAndShiftSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \App\Models\Department::create(['name' => 'Information Technology', 'code' => 'IT', 'description' => 'IT Department']);
        \App\Models\Department::create(['name' => 'Human Resources', 'code' => 'HR', 'description' => 'HR Department']);
        \App\Models\Department::create(['name' => 'Finance', 'code' => 'FIN', 'description' => 'Finance Department']);

        \App\Models\WorkShift::create([
            'name' => 'Shift Pagi (Default)',
            'clock_in_time' => '08:00',
            'clock_out_time' => '17:00',
            'late_tolerance_minutes' => 15,
            'is_default' => true,
        ]);
        
        \App\Models\WorkShift::create([
            'name' => 'Shift Siang',
            'clock_in_time' => '13:00',
            'clock_out_time' => '22:00',
            'late_tolerance_minutes' => 15,
            'is_default' => false,
        ]);
    }
}
