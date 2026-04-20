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
        
        // Redirect admin to admin dashboard
        if ($user->isAdmin()) {
            return redirect()->route('admin.dashboard');
        }

        $activeAnnouncements = Announcement::where('is_active', true)->latest()->take(5)->get();
        
        $todayAttendance = Attendance::where('user_id', $user->id)
            ->whereDate('created_at', now())
            ->get();

        return view('dashboard', compact('activeAnnouncements', 'todayAttendance'));
    }
}
