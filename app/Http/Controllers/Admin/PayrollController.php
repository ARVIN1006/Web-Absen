<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payroll;
use App\Models\PayrollComponent;
use App\Models\PayrollComponentItem;
use App\Models\User;
use App\Models\Attendance;
use App\Notifications\PayrollNotification;
use Illuminate\Http\Request;
use Carbon\Carbon;

class PayrollController extends Controller
{
    public function index(Request $request)
    {
        $month = $request->input('month', now()->month);
        $year = $request->input('year', now()->year);

        $payrolls = Payroll::with(['user.position', 'componentItems.payrollComponent'])
            ->where('month', $month)
            ->where('year', $year)
            ->get();

        return \Inertia\Inertia::render('Admin/Payrolls', [
            'payrolls' => $payrolls,
            'month' => $month,
            'year' => $year,
            'components' => PayrollComponent::where('is_active', true)->orderBy('name')->get(),
        ]);
    }

    public function generate(Request $request)
    {
        $request->validate([
            'month' => 'required|integer|min:1|max:12',
            'year' => 'required|integer|min:2020',
        ]);

        $month = $request->month;
        $year = $request->year;

        $users = User::where('role', 'employee')->with('position')->get();
        $generatedCount = 0;

        foreach ($users as $user) {
            // Check if payroll already exists
            $exists = Payroll::where('user_id', $user->id)
                ->where('month', $month)
                ->where('year', $year)
                ->exists();

            if ($exists) continue;

            $basicSalary = $user->position->salary ?? 0;
            $allowance = $user->position->allowance ?? 0;
            $overtimeRate = $user->position->overtime_rate ?? 0;

            // Calculate total overtime minutes for the month
            $totalOvertimeMinutes = Attendance::where('user_id', $user->id)
                ->whereYear('created_at', $year)
                ->whereMonth('created_at', $month)
                ->sum('overtime_minutes');

            $overtimePay = ($totalOvertimeMinutes / 60) * $overtimeRate;
            $totalEarnings = $basicSalary + $allowance + $overtimePay;
            $totalDeductions = 0;
            $netSalary = $totalEarnings - $totalDeductions;

            $payroll = Payroll::create([
                'user_id' => $user->id,
                'payroll_number' => sprintf('PR-%04d-%02d-%d', $user->id, $month, $year),
                'month' => $month,
                'year' => $year,
                'attendance_days' => Attendance::where('user_id', $user->id)->whereYear('created_at', $year)->whereMonth('created_at', $month)->distinct('attendance_date')->count('attendance_date'),
                'basic_salary' => $basicSalary,
                'overtime_pay' => $overtimePay,
                'bonus' => 0,
                'deductions' => 0,
                'total_earnings' => $totalEarnings,
                'total_deductions' => $totalDeductions,
                'net_salary' => $netSalary,
                'status' => 'draft',
                'period_start' => Carbon::create($year, $month, 1)->startOfMonth(),
                'period_end' => Carbon::create($year, $month, 1)->endOfMonth(),
            ]);

            $baseComponent = PayrollComponent::firstOrCreate(
                ['code' => 'BASIC_SALARY'],
                ['name' => 'Gaji Pokok', 'type' => 'earning', 'calculation_method' => 'auto', 'is_taxable' => true, 'is_active' => true]
            );
            $allowanceComponent = PayrollComponent::firstOrCreate(
                ['code' => 'POSITION_ALLOWANCE'],
                ['name' => 'Tunjangan Jabatan', 'type' => 'earning', 'calculation_method' => 'auto', 'is_taxable' => true, 'is_active' => true]
            );
            $overtimeComponent = PayrollComponent::firstOrCreate(
                ['code' => 'OVERTIME_PAY'],
                ['name' => 'Lembur', 'type' => 'earning', 'calculation_method' => 'auto', 'is_taxable' => true, 'is_active' => true]
            );

            PayrollComponentItem::create([
                'payroll_id' => $payroll->id,
                'payroll_component_id' => $baseComponent->id,
                'amount' => $basicSalary,
                'notes' => 'Generated from position salary',
            ]);
            PayrollComponentItem::create([
                'payroll_id' => $payroll->id,
                'payroll_component_id' => $allowanceComponent->id,
                'amount' => $allowance,
                'notes' => 'Generated from position allowance',
            ]);
            PayrollComponentItem::create([
                'payroll_id' => $payroll->id,
                'payroll_component_id' => $overtimeComponent->id,
                'amount' => $overtimePay,
                'notes' => 'Generated from overtime minutes',
            ]);

            app(\App\Services\ActivityLogService::class)->log(
                auth()->id(),
                $payroll,
                'payroll.generated',
                'Generate payroll ' . $payroll->payroll_number . ' untuk ' . $user->name,
                [
                    'user_id' => $user->id,
                    'month' => $month,
                    'year' => $year,
                    'net_salary' => $netSalary,
                ]
            );

            // Notify the employee
            $user->notify(new PayrollNotification($payroll, 'generated'));
            $generatedCount++;
        }

        if ($generatedCount === 0) {
            return redirect()->back()->with('info', 'Semua payroll untuk periode ini sudah ada.');
        }

        return redirect()->back()->with('success', "Payroll berhasil digenerate untuk {$generatedCount} karyawan.");
    }

    public function updateStatus(Request $request, Payroll $payroll)
    {
        $payroll->update([
            'status' => 'paid',
            'paid_at' => now()
        ]);

        app(\App\Services\ActivityLogService::class)->log(
            auth()->id(),
            $payroll,
            'payroll.paid',
            'Menandai payroll ' . $payroll->payroll_number . ' sebagai lunas',
            [
                'user_id' => $payroll->user_id,
                'net_salary' => $payroll->net_salary,
                'paid_at' => optional($payroll->paid_at)->toDateTimeString(),
            ]
        );

        // Notify the employee
        $payroll->load('user');
        $payroll->user->notify(new PayrollNotification($payroll, 'paid'));

        return redirect()->back()->with('success', 'Payroll ditandai sebagai Lunas.');
    }
}
