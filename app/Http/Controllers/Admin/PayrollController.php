<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payroll;
use App\Models\User;
use App\Models\Attendance;
use Illuminate\Http\Request;
use Carbon\Carbon;

class PayrollController extends Controller
{
    public function index(Request $request)
    {
        $month = $request->get('month', now()->month);
        $year = $request->get('year', now()->year);

        $payrolls = Payroll::with('user.position')
            ->where('month', $month)
            ->where('year', $year)
            ->get();

        return view('admin.payrolls.index', compact('payrolls', 'month', 'year'));
    }

    public function generate(Request $request)
    {
        $month = $request->month;
        $year = $request->year;

        $users = User::where('role', 'employee')->with('position')->get();

        foreach ($users as $user) {
            // Check if payroll already exists
            $exists = Payroll::where('user_id', $user->id)
                ->where('month', $month)
                ->where('year', $year)
                ->exists();

            if ($exists) continue;

            $basicSalary = $user->position->salary ?? 0;
            $overtimeRate = $user->position->overtime_rate ?? 0;

            // Calculate total overtime minutes for the month
            $totalOvertimeMinutes = Attendance::where('user_id', $user->id)
                ->whereYear('created_at', $year)
                ->whereMonth('created_at', $month)
                ->sum('overtime_minutes');

            $overtimePay = ($totalOvertimeMinutes / 60) * $overtimeRate;
            $netSalary = $basicSalary + $overtimePay;

            Payroll::create([
                'user_id' => $user->id,
                'month' => $month,
                'year' => $year,
                'basic_salary' => $basicSalary,
                'overtime_pay' => $overtimePay,
                'net_salary' => $netSalary,
                'status' => 'draft'
            ]);
        }

        return redirect()->back()->with('success', 'Payroll berhasil digenerate untuk periode ini.');
    }

    public function updateStatus(Request $request, Payroll $payroll)
    {
        $payroll->update([
            'status' => 'paid',
            'paid_at' => now()
        ]);

        return redirect()->back()->with('success', 'Payroll ditandai sebagai Lunas.');
    }
}
