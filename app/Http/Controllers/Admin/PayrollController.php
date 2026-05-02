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
            $baseComponent = PayrollComponent::firstOrCreate(
                ['code' => 'BASIC_SALARY'],
                ['name' => 'Gaji Pokok', 'type' => 'earning', 'calculation_method' => 'auto', 'default_amount' => 0, 'is_taxable' => true, 'is_active' => true]
            );
            $allowanceComponent = PayrollComponent::firstOrCreate(
                ['code' => 'POSITION_ALLOWANCE'],
                ['name' => 'Tunjangan Jabatan', 'type' => 'earning', 'calculation_method' => 'auto', 'default_amount' => 0, 'is_taxable' => true, 'is_active' => true]
            );
            $overtimeComponent = PayrollComponent::firstOrCreate(
                ['code' => 'OVERTIME_PAY'],
                ['name' => 'Lembur', 'type' => 'earning', 'calculation_method' => 'auto', 'default_amount' => 0, 'is_taxable' => true, 'is_active' => true]
            );

            $items = [
                [
                    'component' => $baseComponent,
                    'amount' => $basicSalary,
                    'notes' => 'Generated from position salary',
                ],
                [
                    'component' => $allowanceComponent,
                    'amount' => $allowance,
                    'notes' => 'Generated from position allowance',
                ],
                [
                    'component' => $overtimeComponent,
                    'amount' => $overtimePay,
                    'notes' => 'Generated from overtime minutes',
                ],
            ];

            PayrollComponent::where('is_active', true)
                ->where('calculation_method', 'manual')
                ->whereNotIn('code', ['BASIC_SALARY', 'POSITION_ALLOWANCE', 'OVERTIME_PAY'])
                ->orderBy('name')
                ->get()
                ->each(function (PayrollComponent $component) use (&$items) {
                    $items[] = [
                        'component' => $component,
                        'amount' => $component->default_amount ?? 0,
                        'notes' => 'Generated from active manual payroll component',
                    ];
                });

            $totalEarnings = collect($items)
                ->filter(fn ($item) => $item['component']->type === 'earning')
                ->sum('amount');
            $totalDeductions = collect($items)
                ->filter(fn ($item) => $item['component']->type === 'deduction')
                ->sum('amount');
            $netSalary = $totalEarnings - $totalDeductions;

            $payroll = Payroll::create([
                'user_id' => $user->id,
                'payroll_number' => sprintf('PR-%04d-%02d-%d', $user->id, $month, $year),
                'month' => $month,
                'year' => $year,
                'attendance_days' => Attendance::where('user_id', $user->id)->whereYear('created_at', $year)->whereMonth('created_at', $month)->distinct('attendance_date')->count('attendance_date'),
                'basic_salary' => $basicSalary,
                'overtime_pay' => $overtimePay,
                'bonus' => collect($items)->filter(fn ($item) => $item['component']->code === 'BONUS')->sum('amount'),
                'deductions' => $totalDeductions,
                'total_earnings' => $totalEarnings,
                'total_deductions' => $totalDeductions,
                'net_salary' => $netSalary,
                'status' => 'draft',
                'period_start' => Carbon::create($year, $month, 1)->startOfMonth(),
                'period_end' => Carbon::create($year, $month, 1)->endOfMonth(),
            ]);

            foreach ($items as $item) {
                PayrollComponentItem::create([
                    'payroll_id' => $payroll->id,
                    'payroll_component_id' => $item['component']->id,
                    'amount' => $item['amount'],
                    'notes' => $item['notes'],
                ]);
            }

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
        $validated = $request->validate([
            'status' => ['required', 'in:draft,reviewed,paid'],
        ]);

        $payload = ['status' => $validated['status']];
        if ($validated['status'] === 'paid') {
            $payload['paid_at'] = now();
        }
        if ($validated['status'] !== 'paid') {
            $payload['paid_at'] = null;
        }

        $payroll->update($payload);

        app(\App\Services\ActivityLogService::class)->log(
            auth()->id(),
            $payroll,
            'payroll.' . $payroll->status,
            'Mengubah status payroll ' . $payroll->payroll_number . ' menjadi ' . $payroll->status,
            [
                'user_id' => $payroll->user_id,
                'net_salary' => $payroll->net_salary,
                'status' => $payroll->status,
                'paid_at' => optional($payroll->paid_at)->toDateTimeString(),
            ]
        );

        // Notify the employee
        $payroll->load('user');
        if ($payroll->status === 'paid') {
            $payroll->user->notify(new PayrollNotification($payroll, 'paid'));
        }

        return redirect()->back()->with('success', 'Status payroll berhasil diperbarui.');
    }

    public function update(Request $request, Payroll $payroll)
    {
        $validated = $request->validate([
            'items' => ['required', 'array'],
            'items.*.id' => ['required', 'exists:payroll_component_items,id'],
            'items.*.amount' => ['required', 'numeric', 'min:0'],
            'items.*.notes' => ['nullable', 'string'],
            'status' => ['nullable', 'in:draft,reviewed,paid'],
        ]);

        $payroll->load('componentItems.payrollComponent');

        foreach ($validated['items'] as $itemPayload) {
            $item = $payroll->componentItems->firstWhere('id', (int) $itemPayload['id']);
            if (!$item) {
                continue;
            }

            $item->update([
                'amount' => $itemPayload['amount'],
                'notes' => $itemPayload['notes'] ?? null,
            ]);
        }

        $payroll->load('componentItems.payrollComponent');
        $earnings = $payroll->componentItems
            ->filter(fn ($item) => $item->payrollComponent?->type === 'earning')
            ->sum('amount');
        $deductions = $payroll->componentItems
            ->filter(fn ($item) => $item->payrollComponent?->type === 'deduction')
            ->sum('amount');

        $baseSalary = optional($payroll->componentItems->first(fn ($item) => $item->payrollComponent?->code === 'BASIC_SALARY'))->amount ?? $payroll->basic_salary;
        $overtimePay = optional($payroll->componentItems->first(fn ($item) => $item->payrollComponent?->code === 'OVERTIME_PAY'))->amount ?? $payroll->overtime_pay;

        $status = $validated['status'] ?? $payroll->status;

        $payroll->update([
            'basic_salary' => $baseSalary,
            'overtime_pay' => $overtimePay,
            'deductions' => $deductions,
            'total_earnings' => $earnings,
            'total_deductions' => $deductions,
            'net_salary' => $earnings - $deductions,
            'status' => $status,
            'paid_at' => $status === 'paid' ? ($payroll->paid_at ?? now()) : null,
        ]);

        app(\App\Services\ActivityLogService::class)->log(
            auth()->id(),
            $payroll,
            'payroll.updated',
            'Memperbarui payroll ' . $payroll->payroll_number,
            [
                'user_id' => $payroll->user_id,
                'net_salary' => $payroll->net_salary,
                'status' => $payroll->status,
            ]
        );

        return redirect()->back()->with('success', 'Payroll berhasil diperbarui.');
    }
}
