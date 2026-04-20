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
    [
        'name'       => 'TRANS 7 & TRANS TV (Menara Bank Mega)',
        'latitude'   => -6.240667,
        'longitude'  => 106.824083,
        'radius'     => 150, // Area perkantoran dan parkir luas
        'created_at' => now(),
        'updated_at' => now(),
    ],
    [
        'name'       => 'Studio TRANS 7',
        'latitude'   => -6.240222,
        'longitude'  => 106.824583,
        'radius'     => 100, // Fokus pada area studio
        'created_at' => now(),
        'updated_at' => now(),
    ],
    [
        'name'       => 'Sheraton Jakarta Soekarno Hatta Airport',
        'latitude'   => -6.115639,
        'longitude'  => 106.674639,
        'radius'     => 200, // Area resort dan danau sekitarnya
        'created_at' => now(),
        'updated_at' => now(),
    ],
    [
        'name'       => 'RS Puri Cinere',
        'latitude'   => -6.317778,
        'longitude'  => 106.785278,
        'radius'     => 80, // Area bangunan rumah sakit terintegrasi
        'created_at' => now(),
        'updated_at' => now(),
    ],
    [
        'name'       => 'Lippo Mall Puri',
        'latitude'   => -6.188389,
        'longitude'  => 106.738944,
        'radius'     => 250, // Mall sangat luas mencakup St. Moritz
        'created_at' => now(),
        'updated_at' => now(),
    ],
    [
        'name'       => 'Lippo Mall Nusantara (Plaza Semanggi)',
        'latitude'   => -6.219722,
        'longitude'  => 106.814444,
        'radius'     => 150, // Cakupan gedung dan area drop-off
        'created_at' => now(),
        'updated_at' => now(),
    ],
    [
        'name'       => 'Cibubur Junction',
        'latitude'   => -6.369611,
        'longitude'  => 106.894194,
        'radius'     => 120, // Area mall dan parkir terbuka
        'created_at' => now(),
        'updated_at' => now(),
    ],
    [
        'name'       => 'RS Karya Bhakti Pratiwi',
        'latitude'   => -6.587639,
        'longitude'  => 106.740472,
        'radius'     => 70, // Area bangunan medis utama
        'created_at' => now(),
        'updated_at' => now(),
    ],
    [
        'name'       => 'Apartment Brooklyn Alam Sutera',
        'latitude'   => -6.245944,
        'longitude'  => 106.656444,
        'radius'     => 100, // Dua tower utama dan podium
        'created_at' => now(),
        'updated_at' => now(),
    ],
    [
        'name'       => 'Collins Boulevard',
        'latitude'   => -6.232528,
        'longitude'  => 106.649972,
        'radius'     => 130, // Area mix-used development
        'created_at' => now(),
        'updated_at' => now(),
    ],
    [
        'name'       => 'Area Demo (Global Access)',
        'latitude'   => -6.2088, // Pusat Jakarta
        'longitude'  => 106.8456,
        'radius'     => 20000000, // 20.000 KM (Cakup Seluruh Dunia)
        'created_at' => now(),
        'updated_at' => now(),
    ],
]);
    }
}
