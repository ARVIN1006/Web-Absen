<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LeaveBalance;
use App\Models\Holiday;
use App\Models\LeaveRequest;
use App\Models\User;
use App\Notifications\LeaveRequestNotification;
use Illuminate\Http\Request;

class LeaveRequestController extends Controller
{
    public function index()
    {
        $leaveRequests = LeaveRequest::with(['user', 'leaveType', 'approver'])->latest()->get();

        return \Inertia\Inertia::render('Admin/LeaveRequests', [
            'leaveRequests' => $leaveRequests,
            'holidayCount' => Holiday::count(),
        ]);
    }

    public function approve(Request $request, LeaveRequest $leaveRequest)
    {
        $request->validate([
            'admin_note' => 'nullable|string',
        ]);

        if ($leaveRequest->status !== 'pending') {
            return redirect()->route('admin.leave-requests.index')->with('error', 'Pengajuan cuti ini sudah diproses.');
        }

        $leaveRequest->update([
            'status' => 'approved',
            'admin_note' => $request->admin_note,
            'approved_by' => auth()->id(),
            'responded_at' => now(),
        ]);

        app(\App\Services\ActivityLogService::class)->log(
            auth()->id(),
            $leaveRequest,
            'leave.approved',
            'Menyetujui pengajuan cuti ' . ($leaveRequest->request_number ?: '#' . $leaveRequest->id),
            [
                'user_id' => $leaveRequest->user_id,
                'total_days' => $leaveRequest->total_days,
            ]
        );

        if ($leaveRequest->leaveType?->requires_balance) {
            $balance = LeaveBalance::firstOrCreate(
                [
                    'user_id' => $leaveRequest->user_id,
                    'leave_type_id' => $leaveRequest->leave_type_id,
                    'year' => (int) $leaveRequest->start_date->format('Y'),
                ],
                [
                    'allocated_days' => $leaveRequest->leaveType->max_days_per_year ?? 0,
                    'used_days' => 0,
                    'reserved_days' => 0,
                    'remaining_days' => $leaveRequest->leaveType->max_days_per_year ?? 0,
                ]
            );

            $used = (float) $balance->used_days + (float) $leaveRequest->total_days;
            $reserved = max(0, (float) $balance->reserved_days - (float) $leaveRequest->total_days);
            $balance->update([
                'used_days' => $used,
                'reserved_days' => $reserved,
                'remaining_days' => max(0, (float) $balance->allocated_days - $used - $reserved),
            ]);
        }

        // Notify the employee
        $leaveRequest->user->notify(new LeaveRequestNotification($leaveRequest, 'approved'));

        return redirect()->route('admin.leave-requests.index')->with('success', 'Pengajuan cuti disetujui.');
    }

    public function reject(Request $request, LeaveRequest $leaveRequest)
    {
        $request->validate([
            'admin_note' => 'required|string',
        ]);

        if ($leaveRequest->status !== 'pending') {
            return redirect()->route('admin.leave-requests.index')->with('error', 'Pengajuan cuti ini sudah diproses.');
        }

        $leaveRequest->update([
            'status' => 'rejected',
            'admin_note' => $request->admin_note,
            'approved_by' => auth()->id(),
            'responded_at' => now(),
        ]);

        app(\App\Services\ActivityLogService::class)->log(
            auth()->id(),
            $leaveRequest,
            'leave.rejected',
            'Menolak pengajuan cuti ' . ($leaveRequest->request_number ?: '#' . $leaveRequest->id),
            [
                'user_id' => $leaveRequest->user_id,
                'admin_note' => $request->admin_note,
            ]
        );

        if ($leaveRequest->leaveType?->requires_balance) {
            $balance = LeaveBalance::where('user_id', $leaveRequest->user_id)
                ->where('leave_type_id', $leaveRequest->leave_type_id)
                ->where('year', (int) $leaveRequest->start_date->format('Y'))
                ->first();

            if ($balance) {
                $reserved = max(0, (float) $balance->reserved_days - (float) $leaveRequest->total_days);
                $balance->update([
                    'reserved_days' => $reserved,
                    'remaining_days' => max(0, (float) $balance->allocated_days - (float) $balance->used_days - $reserved),
                ]);
            }
        }

        // Notify the employee
        $leaveRequest->user->notify(new LeaveRequestNotification($leaveRequest, 'rejected'));

        return redirect()->route('admin.leave-requests.index')->with('success', 'Pengajuan cuti ditolak.');
    }
}
