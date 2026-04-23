<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\AttendanceCorrection;
use App\Models\EmployeeDocument;
use App\Models\Location;
use App\Models\User;

class DashboardController extends Controller
{
    public function index()
    {
        $totalEmployees = User::where('role', 'employee')->count();
        $todayAttendancesCount = Attendance::whereDate('created_at', now()->toDateString())->count();
        
        // At least 1 IN = present
        $presentToday = Attendance::whereDate('created_at', now()->toDateString())
                                  ->where('type', 'in')
                                  ->distinct('user_id')
                                  ->count();
                                  
        $absentToday = max(0, $totalEmployees - $presentToday);
        
        $totalDepartments = \App\Models\Department::count();
        $totalPositions = \App\Models\Position::count();
        $totalShifts = \App\Models\WorkShift::count();
        $totalLocations = Location::count();
        $pendingLeaves = \App\Models\LeaveRequest::where('status', 'pending')->count();
        $pendingCorrections = AttendanceCorrection::where('status', 'pending')->count();
        $expiringDocumentsCount = EmployeeDocument::whereNotNull('expired_at')
            ->whereDate('expired_at', '>=', now()->toDateString())
            ->whereDate('expired_at', '<=', now()->addDays(30)->toDateString())
            ->count();
        $expiredDocumentsCount = EmployeeDocument::whereNotNull('expired_at')
            ->whereDate('expired_at', '<', now()->toDateString())
            ->count();
        
        $expiringContractsCount = User::where('role', 'employee')
            ->whereNotNull('contract_end_at')
            ->whereBetween('contract_end_at', [now(), now()->addDays(30)])
            ->count();
        
        $recentAttendances = Attendance::with('user')->latest()->take(10)->get();

        // Chart Data: last 7 days 'in' count
        $chartLabels = [];
        $chartData = [];
        $chartDataOut = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $chartLabels[] = $date->translatedFormat('D, d M');
            $chartData[] = Attendance::whereDate('created_at', $date->toDateString())->where('type', 'in')->distinct('user_id')->count();
            $chartDataOut[] = Attendance::whereDate('created_at', $date->toDateString())->where('type', 'out')->distinct('user_id')->count();
        }

        return \Inertia\Inertia::render('Admin/Dashboard', [
            'totalEmployees' => $totalEmployees, 
            'todayAttendancesCount' => $todayAttendancesCount, 
            'presentToday' => $presentToday, 
            'absentToday' => $absentToday, 
            'totalDepartments' => $totalDepartments,
            'totalPositions' => $totalPositions,
            'totalShifts' => $totalShifts,
            'totalLocations' => $totalLocations,
            'pendingLeaves' => $pendingLeaves,
            'pendingCorrections' => $pendingCorrections,
            'expiringDocumentsCount' => $expiringDocumentsCount,
            'expiredDocumentsCount' => $expiredDocumentsCount,
            'expiringContractsCount' => $expiringContractsCount,
            'recentAttendances' => $recentAttendances, 
            'chartLabels' => $chartLabels, 
            'chartData' => $chartData,
            'chartDataOut' => $chartDataOut,
        ]);
    }
}
