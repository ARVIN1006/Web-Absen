<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\CompanySetting;

class CompanySettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \DB::table('company_settings')->insert([
            'name'       => 'PT Serunting Sakti Jaya',
            'latitude'   => -6.4837465,
            'longitude'  => 106.7330865,
            'radius'     => 100, // 100 meters
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
