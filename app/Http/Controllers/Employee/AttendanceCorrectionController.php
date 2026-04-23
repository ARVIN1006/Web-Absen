<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\AttendanceCorrection;
use App\Models\User;
use App\Notifications\AttendanceCorrectionNotification;
use Illuminate\Http\Request;

class AttendanceCorrectionController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'attendance_id' => ['nullable', 'exists:attendances,id'],
            'attendance_date' => ['required', 'date'],
            'requested_check_in_at' => ['nullable', 'date'],
            'requested_check_out_at' => ['nullable', 'date'],
            'reason' => ['required', 'string'],
            'attachment_path' => ['nullable', 'string', 'max:255'],
        ]);

        if (!empty($validated['attendance_id'])) {
            $attendance = Attendance::findOrFail($validated['attendance_id']);
            abort_unless($attendance->user_id === auth()->id(), 403);
        }

        $attendanceCorrection = AttendanceCorrection::create([
            ...$validated,
            'user_id' => auth()->id(),
            'status' => 'pending',
        ]);

        User::query()
            ->where('role', 'admin')
            ->where('is_active', true)
            ->get()
            ->each(fn ($admin) => $admin->notify(new AttendanceCorrectionNotification($attendanceCorrection->load('user'), 'submitted')));

        return redirect()->route('profile.index')->with('success', 'Permintaan koreksi absensi berhasil dikirim.');
    }
}
