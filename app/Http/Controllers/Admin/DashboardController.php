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

        return view('admin.dashboard', compact('totalEmployees', 'todayAttendancesCount', 'presentToday', 'absentToday', 'recentAttendances'));
    }
}
