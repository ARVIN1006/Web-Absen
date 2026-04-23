<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use App\Models\Attendance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        $activeAnnouncements = Announcement::where('is_active', true)->latest()->take(5)->get();
        
        $attendances = Attendance::where('user_id', $user->id)
            ->whereDate('created_at', now())
            ->get();

        $todayAttendance = $attendances; // Send both names for compatibility

        return \Inertia\Inertia::render('Dashboard', [
            'activeAnnouncements' => $activeAnnouncements,
            'attendances' => $attendances,
            'todayAttendance' => $todayAttendance
        ]);
    }
}
