<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\LeaveRequest;
use App\Models\LeaveType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class LeaveController extends Controller
{
    public function index()
    {
        $leaveRequests = LeaveRequest::where('user_id', auth()->id())->latest()->get();
        return view('employee.leave-requests.index', compact('leaveRequests'));
    }

    public function create()
    {
        $leaveTypes = LeaveType::where('is_active', true)->get();
        return view('employee.leave-requests.create', compact('leaveTypes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'leave_type_id' => 'required|exists:leave_types,id',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after_or_equal:start_date',
            'reason' => 'required|string',
            'attachment' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ]);

        $leaveType = LeaveType::find($request->leave_type_id);
        if ($leaveType->requires_attachment && !$request->hasFile('attachment')) {
            return back()->withErrors(['attachment' => 'Tipe cuti ini mewajibkan lampiran.'])->withInput();
        }

        $startDate = Carbon::parse($request->start_date);
        $endDate = Carbon::parse($request->end_date);
        $totalDays = $startDate->diffInDays($endDate) + 1; // Simplistic calculation, ideally skip weekends

        // Check quota if needed (simplified for now)
        $usedDays = LeaveRequest::where('user_id', auth()->id())
            ->where('leave_type_id', $leaveType->id)
            ->where('status', 'approved')
            ->whereYear('created_at', now()->year)
            ->sum('total_days');

        if (($usedDays + $totalDays) > $leaveType->max_days_per_year) {
            return back()->withErrors(['start_date' => 'Kuota cuti tidak mencukupi. Sisa: ' . ($leaveType->max_days_per_year - $usedDays) . ' hari.'])->withInput();
        }

        $attachmentPath = null;
        if ($request->hasFile('attachment')) {
            $attachmentPath = $request->file('attachment')->store('leave_attachments', 'public');
        }

        LeaveRequest::create([
            'user_id' => auth()->id(),
            'leave_type_id' => $request->leave_type_id,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'total_days' => $totalDays,
            'reason' => $request->reason,
            'attachment_path' => $attachmentPath,
        ]);

        return redirect()->route('employee.leave-requests.index')->with('success', 'Pengajuan cuti berhasil dikirim.');
    }
}
