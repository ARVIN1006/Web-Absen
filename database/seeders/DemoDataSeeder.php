<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ActivityLog;
use App\Models\AttendanceCorrection;
use App\Models\Branch;
use App\Models\EmployeeProfile;
use App\Models\EmploymentType;
use App\Models\Holiday;
use App\Models\LeaveBalance;
use App\Models\LeaveRequest;
use App\Models\LeaveType;
use App\Models\Location;
use App\Models\Payroll;
use App\Models\PayrollComponent;
use App\Models\PayrollComponentItem;
use App\Models\User;
use App\Models\Department;
use App\Models\Position;
use App\Models\Announcement;
use App\Models\Reimbursement;
use App\Models\KpiScore;
use App\Models\WorkShift;
use App\Notifications\AnnouncementPublishedNotification;
use App\Notifications\AttendanceCorrectionNotification;
use App\Notifications\LeaveRequestNotification;
use App\Notifications\PayrollNotification;
use App\Notifications\ReimbursementNotification;
use Carbon\Carbon;

class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Reset demo slices
        Announcement::truncate();
        Reimbursement::truncate();
        KpiScore::truncate();
        AttendanceCorrection::truncate();
        LeaveRequest::truncate();
        LeaveBalance::truncate();
        PayrollComponentItem::truncate();
        Payroll::truncate();
        PayrollComponent::truncate();
        Holiday::truncate();
        ActivityLog::truncate();
        EmployeeProfile::truncate();

        $shift = WorkShift::where('is_default', true)->first();
        $branch = Branch::where('code', 'JKT-HO')->first();
        $employmentPermanent = EmploymentType::where('code', 'PERM')->first();
        $employmentContract = EmploymentType::where('code', 'CONT')->first();

        $hqLocation = Location::updateOrCreate(
            ['code' => 'JKT-HO-MAIN'],
            [
                'branch_id' => $branch?->id,
                'name' => 'Jakarta HQ Main Office',
                'latitude' => -6.2088000,
                'longitude' => 106.8456000,
                'radius' => 150,
                'address' => 'Gedung Demo HRIS Lt. 12, Jakarta Selatan',
                'is_active' => true,
                'enforce_face_verification' => true,
            ]
        );

        // 2. Create Employees
        $employeesData = [
            ['name' => 'Budi Santoso', 'email' => 'budi@example.com', 'dept' => 'IT', 'pos' => 'Staff IT', 'phone' => '081234567890', 'gender' => 'male', 'employment' => 'PERM'],
            ['name' => 'Siti Aminah', 'email' => 'siti@example.com', 'dept' => 'HRD', 'pos' => 'Staff HRD', 'phone' => '081234567891', 'gender' => 'female', 'employment' => 'PERM'],
            ['name' => 'Agus Wijaya', 'email' => 'agus@example.com', 'dept' => 'FIN', 'pos' => 'Staff Finance', 'phone' => '081234567892', 'gender' => 'male', 'employment' => 'PERM'],
            ['name' => 'Dewi Lestari', 'email' => 'dewi@example.com', 'dept' => 'MKT', 'pos' => 'Marketing', 'phone' => '081234567893', 'gender' => 'female', 'employment' => 'CONT'],
            ['name' => 'Eko Prasetyo', 'email' => 'eko@example.com', 'dept' => 'PROD', 'pos' => 'Operator Production', 'phone' => '081234567894', 'gender' => 'male', 'employment' => 'CONT'],
        ];

        $employees = collect();

        foreach ($employeesData as $data) {
            $dept = Department::where('code', $data['dept'])->first();
            $pos = Position::where('name', $data['pos'])->first();

            $employee = User::updateOrCreate(['email' => $data['email']], [
                'name' => $data['name'],
                'password' => bcrypt('password'),
                'role' => 'employee',
                'branch_id' => $branch?->id,
                'department_id' => $dept->id,
                'position_id' => $pos->id,
                'work_shift_id' => $shift->id,
                'employment_type_id' => $data['employment'] === 'PERM' ? $employmentPermanent?->id : $employmentContract?->id,
                'phone_number' => $data['phone'],
                'address' => 'Jl. Kebangsaan No. ' . rand(1, 100),
                'nik' => '3201' . rand(100000000000, 999999999999),
                'joined_at' => Carbon::now()->subMonths(rand(6, 24)),
                'contract_end_at' => Carbon::now()->addDays(rand(10, 365)),
                'gender' => $data['gender'],
                'is_active' => true,
            ]);

            EmployeeProfile::updateOrCreate(
                ['user_id' => $employee->id],
                [
                    'employee_code' => 'EMP-' . str_pad((string) $employee->id, 4, '0', STR_PAD_LEFT),
                    'identity_number' => $employee->nik,
                    'tax_number' => '09.111.222.' . str_pad((string) $employee->id, 3, '0', STR_PAD_LEFT),
                    'place_of_birth' => 'Jakarta',
                    'birth_date' => Carbon::now()->subYears(rand(24, 34))->toDateString(),
                    'gender' => $data['gender'],
                    'marital_status' => 'single',
                    'religion' => 'Islam',
                    'nationality' => 'Indonesia',
                    'current_address' => $employee->address,
                    'domicile_address' => $employee->address,
                    'phone_number' => $employee->phone_number,
                    'personal_email' => $employee->email,
                    'joined_at' => optional($employee->joined_at)->toDateString(),
                    'contract_start_at' => optional($employee->joined_at)->toDateString(),
                    'contract_end_at' => optional($employee->contract_end_at)->toDateString(),
                    'employment_status' => 'active',
                    'blood_type' => 'O',
                    'shirt_size' => 'L',
                    'bank_name' => 'Bank Central Asia',
                    'bank_account_number' => '123456' . str_pad((string) $employee->id, 4, '0', STR_PAD_LEFT),
                    'bank_account_name' => $employee->name,
                ]
            );

            $employees->push($employee);
        }

        // 3. Seed Announcements
        $announcements = [
            ['title' => 'Kickoff HRIS Demo Week', 'content' => 'Seluruh tim diminta mencoba alur ESS, approval, payroll, dan notifikasi untuk kebutuhan presentasi client minggu ini.', 'type' => 'info', 'is_active' => true, 'audience' => 'all', 'published_at' => now()],
            ['title' => 'Libur Nasional dan Kalender Cuti 2026', 'content' => 'Tim HR telah mempublikasikan kalender hari libur nasional, cuti bersama, serta aturan pengajuan cuti tahunan.', 'type' => 'warning', 'is_active' => true, 'audience' => 'all', 'published_at' => now()->subDay()],
        ];
        foreach ($announcements as $ann) {
            Announcement::updateOrCreate(['title' => $ann['title']], $ann);
        }

        Holiday::updateOrCreate(
            ['name' => 'Hari Buruh Internasional', 'holiday_date' => Carbon::create(2026, 5, 1)->toDateString()],
            ['type' => 'national', 'branch_id' => null, 'is_recurring' => true, 'notes' => 'Demo holiday']
        );

        Holiday::updateOrCreate(
            ['name' => 'Family Day Jakarta HQ', 'holiday_date' => Carbon::today()->addWeeks(2)->toDateString()],
            ['type' => 'company', 'branch_id' => $branch?->id, 'is_recurring' => false, 'notes' => 'Internal branch event']
        );

        // 4. Seed Pending Reimbursements
        $users = User::where('role', 'employee')->get();
        foreach ($users as $user) {
            Reimbursement::create([
                'user_id' => $user->id,
                'request_number' => 'RBM-' . now()->format('Ym') . '-' . str_pad((string) $user->id, 4, '0', STR_PAD_LEFT),
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
                'period' => 'Q1 2026',
                'attendance_score' => rand(70, 100),
                'performance_score' => rand(60, 100),
                'attitude_score' => rand(80, 100),
                'feedback' => 'Pertahankan kinerjamu di kuartal berikutnya!',
            ]);
        }

        $annualLeave = LeaveType::where('name', 'Cuti Tahunan')->first();

        foreach ($users as $index => $user) {
            if ($annualLeave) {
                LeaveBalance::updateOrCreate(
                    [
                        'user_id' => $user->id,
                        'leave_type_id' => $annualLeave->id,
                        'year' => now()->year,
                    ],
                    [
                        'allocated_days' => 12,
                        'used_days' => $index,
                        'reserved_days' => 1,
                        'remaining_days' => 11 - $index,
                    ]
                );
            }
        }

        $admin = User::where('role', 'admin')->first();
        $leaveDates = [
            [Carbon::today()->addDays(3), Carbon::today()->addDays(4), 'approved'],
            [Carbon::today()->addDays(6), Carbon::today()->addDays(8), 'pending'],
            [Carbon::today()->subDays(4), Carbon::today()->subDays(2), 'rejected'],
        ];

        foreach ($users->take(3) as $offset => $user) {
            [$startDate, $endDate, $status] = $leaveDates[$offset];

            LeaveRequest::create([
                'user_id' => $user->id,
                'leave_type_id' => $annualLeave?->id,
                'request_number' => 'LVE-' . now()->format('Ym') . '-' . str_pad((string) ($offset + 1), 4, '0', STR_PAD_LEFT),
                'start_date' => $startDate->toDateString(),
                'end_date' => $endDate->toDateString(),
                'total_days' => max(1, $startDate->diffInWeekdays($endDate) + 1),
                'days_unit' => 'day',
                'reason' => 'Keperluan keluarga dan personal planning.',
                'contact_number' => $user->phone_number,
                'status' => $status,
                'approved_by' => $status === 'pending' ? null : $admin?->id,
                'responded_at' => $status === 'pending' ? null : now()->subDay(),
                'admin_note' => $status === 'rejected' ? 'Periode tim sedang high season operasional.' : null,
            ]);
        }

        PayrollComponent::updateOrCreate(
            ['code' => 'BASIC'],
            ['name' => 'Gaji Pokok', 'type' => 'earning', 'calculation_method' => 'fixed', 'is_taxable' => true, 'is_active' => true]
        );
        PayrollComponent::updateOrCreate(
            ['code' => 'MEAL'],
            ['name' => 'Tunjangan Makan', 'type' => 'earning', 'calculation_method' => 'manual', 'is_taxable' => false, 'is_active' => true]
        );
        PayrollComponent::updateOrCreate(
            ['code' => 'BPJS'],
            ['name' => 'Potongan BPJS', 'type' => 'deduction', 'calculation_method' => 'manual', 'is_taxable' => false, 'is_active' => true]
        );

        $basicComponent = PayrollComponent::where('code', 'BASIC')->first();
        $mealComponent = PayrollComponent::where('code', 'MEAL')->first();
        $bpjsComponent = PayrollComponent::where('code', 'BPJS')->first();

        foreach ($users->take(4) as $user) {
            $basicSalary = (float) optional($user->position)->salary ?: 6500000;
            $mealAllowance = 350000;
            $bpjsDeduction = 150000;
            $netSalary = $basicSalary + $mealAllowance - $bpjsDeduction;

            $payroll = Payroll::create([
                'user_id' => $user->id,
                'payroll_number' => 'PAY-' . now()->format('Ym') . '-' . str_pad((string) $user->id, 4, '0', STR_PAD_LEFT),
                'month' => now()->subMonth()->month,
                'year' => now()->subMonth()->year,
                'attendance_days' => rand(20, 22),
                'basic_salary' => $basicSalary,
                'overtime_pay' => rand(150000, 450000),
                'bonus' => rand(0, 350000),
                'deductions' => $bpjsDeduction,
                'total_earnings' => $basicSalary + $mealAllowance,
                'total_deductions' => $bpjsDeduction,
                'net_salary' => $netSalary,
                'status' => $user->id % 2 === 0 ? 'paid' : 'draft',
                'paid_at' => $user->id % 2 === 0 ? now()->subDays(5)->toDateString() : null,
                'period_start' => now()->subMonthNoOverflow()->startOfMonth()->toDateString(),
                'period_end' => now()->subMonthNoOverflow()->endOfMonth()->toDateString(),
            ]);

            if ($basicComponent) {
                PayrollComponentItem::create([
                    'payroll_id' => $payroll->id,
                    'payroll_component_id' => $basicComponent->id,
                    'amount' => $basicSalary,
                    'notes' => 'Komponen otomatis demo',
                ]);
            }

            if ($mealComponent) {
                PayrollComponentItem::create([
                    'payroll_id' => $payroll->id,
                    'payroll_component_id' => $mealComponent->id,
                    'amount' => $mealAllowance,
                    'notes' => 'Allowance demo',
                ]);
            }

            if ($bpjsComponent) {
                PayrollComponentItem::create([
                    'payroll_id' => $payroll->id,
                    'payroll_component_id' => $bpjsComponent->id,
                    'amount' => $bpjsDeduction,
                    'notes' => 'Deduction demo',
                ]);
            }
        }

        foreach ($users->take(2) as $user) {
            AttendanceCorrection::create([
                'attendance_id' => null,
                'user_id' => $user->id,
                'attendance_date' => Carbon::today()->subDays(rand(1, 5))->toDateString(),
                'requested_check_in_at' => Carbon::today()->subDays(1)->setTime(8, rand(1, 25)),
                'requested_check_out_at' => Carbon::today()->subDays(1)->setTime(17, rand(5, 45)),
                'reason' => 'Lupa check-in karena meeting dengan client.',
                'status' => 'pending',
            ]);
        }

        foreach ($users->take(4) as $user) {
            ActivityLog::create([
                'actor_id' => $admin?->id,
                'subject_type' => User::class,
                'subject_id' => $user->id,
                'event' => 'employee.reviewed',
                'description' => 'Profil karyawan ditinjau untuk kebutuhan HRIS demo.',
                'properties' => [
                    'employee_code' => 'EMP-' . str_pad((string) $user->id, 4, '0', STR_PAD_LEFT),
                    'location' => $hqLocation->name,
                ],
            ]);
        }

        if ($admin) {
            $pendingLeave = LeaveRequest::where('status', 'pending')->first();
            $pendingReimbursement = Reimbursement::where('status', 'pending')->first();
            $pendingCorrection = AttendanceCorrection::where('status', 'pending')->first();

            if ($pendingLeave) {
                $admin->notify(new LeaveRequestNotification($pendingLeave->load('user'), 'submitted'));
            }

            if ($pendingReimbursement) {
                $admin->notify(new ReimbursementNotification($pendingReimbursement->load('user'), 'submitted'));
            }

            if ($pendingCorrection) {
                $admin->notify(new AttendanceCorrectionNotification($pendingCorrection->load('user'), 'submitted'));
            }
        }

        foreach ($users->take(2) as $user) {
            $latestAnnouncement = Announcement::latest()->first();
            $latestPayroll = Payroll::where('user_id', $user->id)->latest()->first();
            $approvedLeave = LeaveRequest::where('user_id', $user->id)->where('status', 'approved')->latest()->first();

            if ($latestAnnouncement) {
                $user->notify(new AnnouncementPublishedNotification($latestAnnouncement));
            }

            if ($latestPayroll) {
                $user->notify(new PayrollNotification($latestPayroll, $latestPayroll->status === 'paid' ? 'paid' : 'generated'));
            }

            if ($approvedLeave) {
                $user->notify(new LeaveRequestNotification($approvedLeave->load('user'), 'approved'));
            }
        }
    }
}
