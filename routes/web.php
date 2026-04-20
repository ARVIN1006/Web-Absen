<?php

use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\ProfileController;
use App\Models\Attendance;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/panduan', function () {
    return view('guide');
})->name('guide');

// Dashboard route handled by Controller
Route::get('/dashboard', [\App\Http\Controllers\DashboardController::class, 'index'])->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware(['auth'])->group(function () {
    // Attendance
    Route::get('/attendance',  [AttendanceController::class, 'index'])->name('attendance.index');
    Route::post('/attendance', [AttendanceController::class, 'store'])->name('attendance.store');
    Route::post('/attendance/demo-sync', [AttendanceController::class, 'demoSync'])->name('attendance.demo-sync');

    // Profile (Handled in Employee section below)


    // Employee Leave Requests
    Route::prefix('employee')->name('employee.')->group(function () {
        Route::resource('leave-requests', \App\Http\Controllers\Employee\LeaveController::class)->only(['index', 'create', 'store']);
    });
});

// Admin Routes
Route::prefix('admin')->name('admin.')->middleware(['auth', 'admin'])->group(function () {
    Route::get('/', [\App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('dashboard');
    
    // Employees
    Route::get('/employees', [\App\Http\Controllers\Admin\EmployeeController::class, 'index'])->name('employees.index');
    Route::get('/employees/create', [\App\Http\Controllers\Admin\EmployeeController::class, 'create'])->name('employees.create');
    Route::post('/employees', [\App\Http\Controllers\Admin\EmployeeController::class, 'store'])->name('employees.store');
    Route::get('/employees/{employee}/edit', [\App\Http\Controllers\Admin\EmployeeController::class, 'edit'])->name('employees.edit');
    Route::put('/employees/{employee}', [\App\Http\Controllers\Admin\EmployeeController::class, 'update'])->name('employees.update');
    Route::delete('/employees/{employee}', [\App\Http\Controllers\Admin\EmployeeController::class, 'destroy'])->name('employees.destroy');

    // Departments
    Route::resource('departments', \App\Http\Controllers\Admin\DepartmentController::class)->except(['show']);

    // Positions
    Route::resource('positions', \App\Http\Controllers\Admin\PositionController::class)->except(['show', 'create', 'edit']);

    // Work Shifts
    Route::resource('work-shifts', \App\Http\Controllers\Admin\WorkShiftController::class)->except(['show']);

    // Leave Types
    Route::resource('leave-types', \App\Http\Controllers\Admin\LeaveTypeController::class)->except(['show', 'create', 'edit']);

    // Leave Requests
    Route::get('leave-requests', [\App\Http\Controllers\Admin\LeaveRequestController::class, 'index'])->name('leave-requests.index');
    Route::post('leave-requests/{leaveRequest}/approve', [\App\Http\Controllers\Admin\LeaveRequestController::class, 'approve'])->name('leave-requests.approve');
    Route::post('leave-requests/{leaveRequest}/reject', [\App\Http\Controllers\Admin\LeaveRequestController::class, 'reject'])->name('leave-requests.reject');

    // Locations
    Route::get('/locations', [\App\Http\Controllers\Admin\LocationController::class, 'index'])->name('locations.index');
    Route::post('/locations', [\App\Http\Controllers\Admin\LocationController::class, 'store'])->name('locations.store');
    Route::put('/locations/{location}', [\App\Http\Controllers\Admin\LocationController::class, 'update'])->name('locations.update');
    Route::delete('/locations/{location}', [\App\Http\Controllers\Admin\LocationController::class, 'destroy'])->name('locations.destroy');

    // Org Chart
    Route::get('/org-chart', function() {
        $departments = \App\Models\Department::with(['users.position'])->get();
        return view('admin.org-chart', compact('departments'));
    })->name('org-chart');

    // Attendances
    Route::get('attendances', [\App\Http\Controllers\Admin\AttendanceController::class, 'index'])->name('attendances.index');
    Route::delete('attendances/{attendance}', [\App\Http\Controllers\Admin\AttendanceController::class, 'destroy'])->name('attendances.destroy');
    Route::get('/export/csv', [\App\Http\Controllers\Admin\ExportController::class, 'csv'])->name('export.csv');
    Route::get('/export/pdf', [\App\Http\Controllers\Admin\ExportController::class, 'pdf'])->name('export.pdf');

    // Reimbursements
    Route::get('reimbursements', [\App\Http\Controllers\Admin\ReimbursementController::class, 'index'])->name('reimbursements.index');
    Route::patch('reimbursements/{reimbursement}/status', [\App\Http\Controllers\Admin\ReimbursementController::class, 'updateStatus'])->name('reimbursements.updateStatus');

    // Payrolls
    Route::get('payrolls', [\App\Http\Controllers\Admin\PayrollController::class, 'index'])->name('payrolls.index');
    Route::post('payrolls/generate', [\App\Http\Controllers\Admin\PayrollController::class, 'generate'])->name('payrolls.generate');
    Route::patch('payrolls/{payroll}/status', [\App\Http\Controllers\Admin\PayrollController::class, 'updateStatus'])->name('payrolls.updateStatus');

    // Reports
    Route::get('/reports/labor-cost', [\App\Http\Controllers\Admin\ReportController::class, 'laborCost'])->name('reports.labor-cost');
    Route::get('/reports/performance-heatmap', [\App\Http\Controllers\Admin\ReportController::class, 'performanceHeatmap'])->name('reports.performance-heatmap');

    // Performance (KPI)
    Route::get('kpi', [\App\Http\Controllers\Admin\KpiController::class, 'index'])->name('kpi.index');
    Route::post('kpi', [\App\Http\Controllers\Admin\KpiController::class, 'store'])->name('kpi.store');

    // Announcements
    Route::resource('announcements', \App\Http\Controllers\Admin\AnnouncementController::class)->except(['show', 'create', 'edit']);

    // Centralized Approvals
    Route::get('/approvals', function() {
        $leaveRequests = \App\Models\LeaveRequest::where('status', 'pending')->with(['user', 'leaveType'])->get();
        $reimbursements = \App\Models\Reimbursement::where('status', 'pending')->with('user')->get();
        return view('admin.approvals', compact('leaveRequests', 'reimbursements'));
    })->name('approvals');
});

// Employee Routes
Route::middleware(['auth'])->group(function () {
    // Dashboard already defined outside

    
    // Employee Self-Service
    Route::get('/profile', [\App\Http\Controllers\Employee\ProfileController::class, 'index'])->name('profile.index');
    Route::put('/profile', [\App\Http\Controllers\Employee\ProfileController::class, 'update'])->name('profile.update');

    Route::get('/my-reimbursements', [\App\Http\Controllers\Employee\ReimbursementController::class, 'index'])->name('reimbursements.index');
    Route::post('/my-reimbursements', [\App\Http\Controllers\Employee\ReimbursementController::class, 'store'])->name('reimbursements.store');

    Route::get('/my-payrolls', [\App\Http\Controllers\Employee\PayrollController::class, 'index'])->name('payrolls.user_index');
    Route::get('/my-payrolls/{payroll}', [\App\Http\Controllers\Employee\PayrollController::class, 'show'])->name('payrolls.show');

    Route::get('/directory', function(\Illuminate\Http\Request $request) {
        $query = \App\Models\User::where('role', 'employee')->with(['position', 'department']);
        
        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }
        if ($request->filled('dept')) {
            $query->where('department_id', $request->dept);
        }
        
        $employees = $query->get();
        $departments = \App\Models\Department::all();
        return view('employee.directory', compact('employees', 'departments'));
    })->name('company-directory');
});

Route::get('/guide', function() {
    return view('guide');
})->name('guide');

require __DIR__.'/auth.php';
