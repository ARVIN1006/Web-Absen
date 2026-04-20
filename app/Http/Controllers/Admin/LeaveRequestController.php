<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LeaveRequest;
use Illuminate\Http\Request;

class LeaveRequestController extends Controller
{
    public function index()
    {
        $leaveRequests = LeaveRequest::with(['user', 'leaveType', 'approver'])->latest()->get();
        return view('admin.leave-requests.index', compact('leaveRequests'));
    }

    public function approve(Request $request, LeaveRequest $leaveRequest)
    {
        $request->validate([
            'admin_note' => 'nullable|string',
        ]);

        $leaveRequest->update([
            'status' => 'approved',
            'admin_note' => $request->admin_note,
            'approved_by' => auth()->id(),
            'responded_at' => now(),
        ]);

        return redirect()->route('admin.leave-requests.index')->with('success', 'Pengajuan cuti disetujui.');
    }

    public function reject(Request $request, LeaveRequest $leaveRequest)
    {
        $request->validate([
            'admin_note' => 'required|string',
        ]);

        $leaveRequest->update([
            'status' => 'rejected',
            'admin_note' => $request->admin_note,
            'approved_by' => auth()->id(),
            'responded_at' => now(),
        ]);

        return redirect()->route('admin.leave-requests.index')->with('success', 'Pengajuan cuti ditolak.');
    }
}
