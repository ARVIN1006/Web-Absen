<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\Payroll;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PayrollController extends Controller
{
    public function index()
    {
        $payrolls = Auth::user()->payrolls()->latest()->get();
        return \Inertia\Inertia::render('Employee/Payrolls', [
            'payrolls' => $payrolls
        ]);
    }

    public function show(Payroll $payroll)
    {
        // Ensure user can only see their own payroll
        if ($payroll->user_id !== Auth::id()) {
            abort(403);
        }

        return \Inertia\Inertia::render('Employee/PayrollShow', [
            'payroll' => $payroll->load(['user.position', 'componentItems.payrollComponent'])
        ]);
    }
}
