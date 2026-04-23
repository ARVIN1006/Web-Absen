<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LeaveBalance;
use App\Models\LeaveType;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;

class LeaveBalanceController extends Controller
{
    public function index()
    {
        return Inertia::render('Admin/LeaveBalances', [
            'leaveBalances' => LeaveBalance::with(['user:id,name,email', 'leaveType:id,name,code'])->latest()->get(),
            'employees' => User::where('role', 'employee')->orderBy('name')->get(['id', 'name']),
            'leaveTypes' => LeaveType::where('is_active', true)->orderBy('name')->get(['id', 'name']),
            'currentYear' => now()->year,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => ['required', 'exists:users,id'],
            'leave_type_id' => ['required', 'exists:leave_types,id'],
            'year' => ['required', 'integer', 'min:2000', 'max:2100'],
            'allocated_days' => ['required', 'numeric', 'min:0'],
            'used_days' => ['required', 'numeric', 'min:0'],
            'reserved_days' => ['required', 'numeric', 'min:0'],
            'remaining_days' => ['required', 'numeric', 'min:0'],
        ]);

        LeaveBalance::updateOrCreate(
            [
                'user_id' => $validated['user_id'],
                'leave_type_id' => $validated['leave_type_id'],
                'year' => $validated['year'],
            ],
            $validated
        );

        return redirect()->route('admin.leave-balances.index')->with('success', 'Saldo cuti berhasil disimpan.');
    }

    public function update(Request $request, LeaveBalance $leaveBalance)
    {
        $validated = $request->validate([
            'user_id' => ['required', 'exists:users,id'],
            'leave_type_id' => ['required', 'exists:leave_types,id'],
            'year' => ['required', 'integer', 'min:2000', 'max:2100'],
            'allocated_days' => ['required', 'numeric', 'min:0'],
            'used_days' => ['required', 'numeric', 'min:0'],
            'reserved_days' => ['required', 'numeric', 'min:0'],
            'remaining_days' => ['required', 'numeric', 'min:0'],
        ]);

        $leaveBalance->update($validated);

        return redirect()->route('admin.leave-balances.index')->with('success', 'Saldo cuti berhasil diperbarui.');
    }

    public function destroy(LeaveBalance $leaveBalance)
    {
        $leaveBalance->delete();

        return redirect()->route('admin.leave-balances.index')->with('success', 'Saldo cuti berhasil dihapus.');
    }
}
