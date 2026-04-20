<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Department;
use App\Models\Position;
use App\Models\Announcement;
use App\Models\Reimbursement;
use App\Models\KpiScore;
use App\Models\WorkShift;
use Carbon\Carbon;

class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Get Master Data
        Announcement::truncate();
        Reimbursement::truncate();
        KpiScore::truncate();

        $depts = Department::all();
        $positions = Position::all();
        $shift = WorkShift::where('is_default', true)->first();

        // 2. Create Employees
        $employeesData = [
            ['name' => 'Budi Santoso', 'email' => 'budi@example.com', 'dept' => 'IT', 'pos' => 'Staff IT', 'phone' => '081234567890'],
            ['name' => 'Siti Aminah', 'email' => 'siti@example.com', 'dept' => 'HRD', 'pos' => 'Staff HRD', 'phone' => '081234567891'],
            ['name' => 'Agus Wijaya', 'email' => 'agus@example.com', 'dept' => 'FIN', 'pos' => 'Staff Finance', 'phone' => '081234567892'],
            ['name' => 'Dewi Lestari', 'email' => 'dewi@example.com', 'dept' => 'MKT', 'pos' => 'Marketing', 'phone' => '081234567893'],
            ['name' => 'Eko Prasetyo', 'email' => 'eko@example.com', 'dept' => 'PROD', 'pos' => 'Operator Production', 'phone' => '081234567894'],
        ];

        foreach ($employeesData as $data) {
            $dept = Department::where('code', $data['dept'])->first();
            $pos = Position::where('name', $data['pos'])->first();

            User::updateOrCreate(['email' => $data['email']], [
                'name' => $data['name'],
                'password' => bcrypt('password'),
                'role' => 'employee',
                'department_id' => $dept->id,
                'position_id' => $pos->id,
                'work_shift_id' => $shift->id,
                'phone_number' => $data['phone'],
                'address' => 'Jl. Kebangsaan No. ' . rand(1, 100),
                'nik' => '3201' . rand(100000000000, 999999999999),
                'joined_at' => Carbon::now()->subMonths(rand(6, 24)),
                'contract_end_at' => Carbon::now()->addDays(rand(10, 365)),
            ]);
        }

        // 3. Seed Announcements
        $announcements = [
            ['title' => 'Update Aturan Shift Kerja', 'content' => 'Mulai minggu depan, toleransi keterlambatan dikurangi menjadi 10 menit.', 'type' => 'info', 'is_active' => true],
            ['title' => 'Libur Idul Fitri 2026', 'content' => 'Kantor akan libur mulai tanggal 25 Maret hingga 2 April 2026.', 'type' => 'warning', 'is_active' => true],
        ];
        foreach ($announcements as $ann) {
            Announcement::updateOrCreate(['title' => $ann['title']], $ann);
        }

        // 4. Seed Pending Reimbursements
        $users = User::where('role', 'employee')->get();
        foreach ($users as $user) {
            Reimbursement::create([
                'user_id' => $user->id,
                'title' => 'Klaim Medis - ' . $user->name,
                'description' => 'Pembelian obat-obatan di Apotek Kimia Farma.',
                'amount' => rand(100000, 500000),
                'type' => 'medical',
                'status' => 'pending',
                'attachment_path' => 'demo/receipt.jpg', // Placeholder
            ]);
        }

        // 5. Seed KPI Scores
        foreach ($users as $user) {
            KpiScore::create([
                'user_id' => $user->id,
                'period' => 'Q1 2024',
                'attendance_score' => rand(70, 100),
                'performance_score' => rand(60, 100),
                'attitude_score' => rand(80, 100),
                'feedback' => 'Pertahankan kinerjamu di kuartal berikutnya!',
            ]);
        }
    }
}
