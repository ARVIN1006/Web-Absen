<?php

use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ProfileController;
use App\Models\Attendance;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    if (auth()->check()) {
        return redirect()->route(auth()->user()->isAdmin() ? 'admin.dashboard' : 'dashboard');
    }

    return \Inertia\Inertia::render('Auth/Login');
});

Route::get('/panduan', function () {
    return \Inertia\Inertia::render('Guide');
})->name('guide');

// Dashboard route handled by Controller
Route::get('/dashboard', [\App\Http\Controllers\DashboardController::class, 'index'])->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware(['auth'])->group(function () {
    // Attendance
    Route::get('/attendance',  [AttendanceController::class, 'index'])->name('attendance.index');
    Route::post('/attendance', [AttendanceController::class, 'store'])->name('attendance.store');
    Route::post('/attendance/demo-sync', [AttendanceController::class, 'demoSync'])->name('attendance.demo-sync');
    Route::post('/attendance/demo-reset-today', [AttendanceController::class, 'demoResetToday'])->name('attendance.demo-reset-today');

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
    Route::get('/employees/{employee}', [\App\Http\Controllers\Admin\EmployeeController::class, 'show'])->name('employees.show');
    Route::get('/employees/{employee}/edit', [\App\Http\Controllers\Admin\EmployeeController::class, 'edit'])->name('employees.edit');
    Route::put('/employees/{employee}', [\App\Http\Controllers\Admin\EmployeeController::class, 'update'])->name('employees.update');
    Route::delete('/employees/{employee}', [\App\Http\Controllers\Admin\EmployeeController::class, 'destroy'])->name('employees.destroy');
    Route::post('/employees/{employee}/emergency-contacts', [\App\Http\Controllers\Admin\EmployeeController::class, 'storeEmergencyContact'])->name('employees.emergency-contacts.store');
    Route::put('/employees/{employee}/emergency-contacts/{contact}', [\App\Http\Controllers\Admin\EmployeeController::class, 'updateEmergencyContact'])->name('employees.emergency-contacts.update');
    Route::delete('/employees/{employee}/emergency-contacts/{contact}', [\App\Http\Controllers\Admin\EmployeeController::class, 'destroyEmergencyContact'])->name('employees.emergency-contacts.destroy');
    Route::post('/employees/{employee}/documents', [\App\Http\Controllers\Admin\EmployeeController::class, 'storeDocument'])->name('employees.documents.store');
    Route::put('/employees/{employee}/documents/{document}', [\App\Http\Controllers\Admin\EmployeeController::class, 'updateDocument'])->name('employees.documents.update');
    Route::delete('/employees/{employee}/documents/{document}', [\App\Http\Controllers\Admin\EmployeeController::class, 'destroyDocument'])->name('employees.documents.destroy');
    Route::post('/employees/{employee}/educations', [\App\Http\Controllers\Admin\EmployeeController::class, 'storeEducation'])->name('employees.educations.store');
    Route::put('/employees/{employee}/educations/{education}', [\App\Http\Controllers\Admin\EmployeeController::class, 'updateEducation'])->name('employees.educations.update');
    Route::delete('/employees/{employee}/educations/{education}', [\App\Http\Controllers\Admin\EmployeeController::class, 'destroyEducation'])->name('employees.educations.destroy');
    Route::post('/employees/{employee}/career-histories', [\App\Http\Controllers\Admin\EmployeeController::class, 'storeCareerHistory'])->name('employees.career-histories.store');
    Route::put('/employees/{employee}/career-histories/{careerHistory}', [\App\Http\Controllers\Admin\EmployeeController::class, 'updateCareerHistory'])->name('employees.career-histories.update');
    Route::delete('/employees/{employee}/career-histories/{careerHistory}', [\App\Http\Controllers\Admin\EmployeeController::class, 'destroyCareerHistory'])->name('employees.career-histories.destroy');

    // Departments
    Route::resource('departments', \App\Http\Controllers\Admin\DepartmentController::class)->except(['show']);

    // Branches
    Route::resource('branches', \App\Http\Controllers\Admin\BranchController::class)->except(['show', 'create', 'edit']);

    // Employment Types
    Route::resource('employment-types', \App\Http\Controllers\Admin\EmploymentTypeController::class)->except(['show', 'create', 'edit']);

    // Positions
    Route::resource('positions', \App\Http\Controllers\Admin\PositionController::class)->except(['show', 'create', 'edit']);

    // Work Shifts
    Route::resource('work-shifts', \App\Http\Controllers\Admin\WorkShiftController::class)->except(['show']);

    // Leave Balances
    Route::resource('leave-balances', \App\Http\Controllers\Admin\LeaveBalanceController::class)->except(['show', 'create', 'edit']);

    // Holidays
    Route::resource('holidays', \App\Http\Controllers\Admin\HolidayController::class)->except(['show', 'create', 'edit']);

    // Attendance Corrections
    Route::get('attendance-corrections', [\App\Http\Controllers\Admin\AttendanceCorrectionController::class, 'index'])->name('attendance-corrections.index');
    Route::post('attendance-corrections/{attendanceCorrection}/approve', [\App\Http\Controllers\Admin\AttendanceCorrectionController::class, 'approve'])->name('attendance-corrections.approve');
    Route::post('attendance-corrections/{attendanceCorrection}/reject', [\App\Http\Controllers\Admin\AttendanceCorrectionController::class, 'reject'])->name('attendance-corrections.reject');

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
        return \Inertia\Inertia::render('Admin/OrgChart', [
            'departments' => $departments
        ]);
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
    Route::resource('payroll-components', \App\Http\Controllers\Admin\PayrollComponentController::class)->except(['show', 'create', 'edit']);

    // Reports
    Route::get('/reports/labor-cost', [\App\Http\Controllers\Admin\ReportController::class, 'laborCost'])->name('reports.labor-cost');
    Route::get('/reports/performance-heatmap', [\App\Http\Controllers\Admin\ReportController::class, 'performanceHeatmap'])->name('reports.performance-heatmap');
    Route::get('/activity-logs', [\App\Http\Controllers\Admin\ActivityLogController::class, 'index'])->name('activity-logs.index');

    // Performance (KPI)
    Route::get('kpi', [\App\Http\Controllers\Admin\KpiController::class, 'index'])->name('kpi.index');
    Route::post('kpi', [\App\Http\Controllers\Admin\KpiController::class, 'store'])->name('kpi.store');

    // Announcements
    Route::patch('announcements/{announcement}/toggle', [\App\Http\Controllers\Admin\AnnouncementController::class, 'toggle'])->name('announcements.toggle');
    Route::resource('announcements', \App\Http\Controllers\Admin\AnnouncementController::class)->except(['show', 'create', 'edit']);

    // Centralized Approvals
    Route::get('/approvals', function() {
        $leaveRequests = \App\Models\LeaveRequest::where('status', 'pending')->with(['user', 'leaveType'])->get();
        $reimbursements = \App\Models\Reimbursement::where('status', 'pending')->with('user')->get();
        $attendanceCorrections = \App\Models\AttendanceCorrection::where('status', 'pending')
            ->with(['user', 'attendance'])
            ->latest()
            ->get();

        return \Inertia\Inertia::render('Admin/Approvals', [
            'leaveRequests' => $leaveRequests,
            'reimbursements' => $reimbursements,
            'attendanceCorrections' => $attendanceCorrections,
        ]);
    })->name('approvals');
});

// Employee Routes
Route::middleware(['auth'])->group(function () {
    // Dashboard already defined outside

    
    // Employee Self-Service
    Route::get('/profile', [\App\Http\Controllers\Employee\ProfileController::class, 'index'])->name('profile.index');
    Route::match(['put', 'patch'], '/profile', [\App\Http\Controllers\Employee\ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [\App\Http\Controllers\Employee\ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/my-reimbursements', [\App\Http\Controllers\Employee\ReimbursementController::class, 'index'])->name('reimbursements.index');
    Route::post('/my-reimbursements', [\App\Http\Controllers\Employee\ReimbursementController::class, 'store'])->name('reimbursements.store');

    Route::get('/my-payrolls', [\App\Http\Controllers\Employee\PayrollController::class, 'index'])->name('payrolls.user_index');
    Route::get('/my-payrolls/{payroll}', [\App\Http\Controllers\Employee\PayrollController::class, 'show'])->name('payrolls.show');

    Route::get('/directory', [\App\Http\Controllers\Employee\DirectoryController::class, 'index'])->name('company-directory');
    Route::post('/attendance-corrections', [\App\Http\Controllers\Employee\AttendanceCorrectionController::class, 'store'])->name('attendance-corrections.store');
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/{notification}/read', [NotificationController::class, 'markAsRead'])->name('notifications.read');
    Route::post('/notifications/read-all', [NotificationController::class, 'readAll'])->name('notifications.read-all');
});

require __DIR__.'/auth.php';
