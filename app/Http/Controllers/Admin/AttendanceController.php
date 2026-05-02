<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\AttendanceCorrection;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    public function index(Request $request)
    {
        $query = Attendance::with(['user.position', 'user.department', 'location'])->latest();

        // Filtering
        if ($request->filled('start_date')) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }
        if ($request->filled('search')) {
            $query->whereHas('user', function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%');
            });
        }

        $attendances = $query->paginate(20)->withQueryString();

        // Stats for Today
        $todayStats = [
            'total' => \App\Models\User::where('role', 'employee')->count(),
            'present' => Attendance::whereDate('created_at', today())->distinct('user_id')->count(),
            'late' => Attendance::whereDate('created_at', today())->where('late_status', 'late')->distinct('user_id')->count(),
        ];
        $todayStats['absent'] = $todayStats['total'] - $todayStats['present'];

        return \Inertia\Inertia::render('Admin/Attendances', [
            'attendances' => $attendances,
            'todayStats' => $todayStats,
            'pendingCorrections' => AttendanceCorrection::where('status', 'pending')->count(),
            'filters' => $request->only(['start_date', 'end_date', 'search']),
        ]);
    }

    public function destroy(Attendance $attendance)
    {
        $attendance->delete();
        return redirect()->route('admin.attendances.index')->with('success', 'Data absensi berhasil dihapus.');
    }
}
