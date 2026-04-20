<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\User;
use Illuminate\Http\Request;
use Carbon\Carbon;

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
        
        $recentAttendances = Attendance::with('user')->latest()->take(10)->get();

        // Chart Data: last 7 days 'in' count
        $chartLabels = [];
        $chartData = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $chartLabels[] = $date->format('D, d M');
            $chartData[] = Attendance::whereDate('created_at', $date->toDateString())->where('type', 'in')->distinct('user_id')->count();
        }

        return view('admin.dashboard', compact('totalEmployees', 'todayAttendancesCount', 'presentToday', 'absentToday', 'recentAttendances', 'chartLabels', 'chartData'));
    }
}
