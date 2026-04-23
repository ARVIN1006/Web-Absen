<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AttendanceCorrection;
use App\Notifications\AttendanceCorrectionNotification;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AttendanceCorrectionController extends Controller
{
    public function index()
    {
        return Inertia::render('Admin/AttendanceCorrections', [
            'corrections' => AttendanceCorrection::with(['user:id,name,email', 'attendance', 'approver:id,name'])->latest()->get(),
        ]);
    }

    public function approve(Request $request, AttendanceCorrection $attendanceCorrection)
    {
        $validated = $request->validate([
            'admin_note' => ['nullable', 'string'],
        ]);

        $attendanceCorrection->update([
            'status' => 'approved',
            'admin_note' => $validated['admin_note'] ?? null,
            'approved_by' => auth()->id(),
            'approved_at' => now(),
        ]);

        app(\App\Services\ActivityLogService::class)->log(
            auth()->id(),
            $attendanceCorrection,
            'attendance_correction.approved',
            'Menyetujui koreksi absensi #' . $attendanceCorrection->id,
            [
                'user_id' => $attendanceCorrection->user_id,
                'attendance_date' => $attendanceCorrection->attendance_date?->toDateString(),
            ]
        );

        $attendanceCorrection->user?->notify(new AttendanceCorrectionNotification($attendanceCorrection->load('user'), 'approved'));

        return redirect()->route('admin.attendance-corrections.index')->with('success', 'Koreksi absensi berhasil disetujui.');
    }

    public function reject(Request $request, AttendanceCorrection $attendanceCorrection)
    {
        $validated = $request->validate([
            'admin_note' => ['required', 'string'],
        ]);

        $attendanceCorrection->update([
            'status' => 'rejected',
            'admin_note' => $validated['admin_note'],
            'approved_by' => auth()->id(),
            'approved_at' => now(),
        ]);

        app(\App\Services\ActivityLogService::class)->log(
            auth()->id(),
            $attendanceCorrection,
            'attendance_correction.rejected',
            'Menolak koreksi absensi #' . $attendanceCorrection->id,
            [
                'user_id' => $attendanceCorrection->user_id,
                'attendance_date' => $attendanceCorrection->attendance_date?->toDateString(),
                'admin_note' => $validated['admin_note'],
            ]
        );

        $attendanceCorrection->user?->notify(new AttendanceCorrectionNotification($attendanceCorrection->load('user'), 'rejected'));

        return redirect()->route('admin.attendance-corrections.index')->with('success', 'Koreksi absensi berhasil ditolak.');
    }
}
