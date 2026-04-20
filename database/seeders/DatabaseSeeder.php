<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create default admin user
        User::updateOrCreate(['email' => 'admin@admin.com'], [
            'name'  => 'Admin System',
            'password' => bcrypt('password'),
            'role' => 'admin',
        ]);

        // Create default test user
        User::updateOrCreate(['email' => 'test@example.com'], [
            'name'  => 'Test User',
            'password' => bcrypt('password'),
            'role' => 'employee',
        ]);

        // Seed master data
        $this->call([
            EmployeeMasterDataSeeder::class,
            CompanySettingSeeder::class,
            DemoDataSeeder::class,
        ]);
    }
}
