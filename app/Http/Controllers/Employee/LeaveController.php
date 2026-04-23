<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\Holiday;
use App\Models\LeaveRequest;
use App\Models\LeaveType;
use App\Models\User;
use App\Notifications\LeaveRequestNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class LeaveController extends Controller
{
    public function index()
    {
        $leaveRequests = LeaveRequest::where('user_id', auth()->id())
            ->with(['leaveType', 'approver'])
            ->latest()
            ->get();
        return \Inertia\Inertia::render('Employee/LeaveRequests', [
            'leaveRequests' => $leaveRequests
        ]);
    }

    public function create()
    {
        $leaveTypes = LeaveType::where('is_active', true)->get();

        return \Inertia\Inertia::render('Employee/LeaveRequestCreate', [
            'leaveTypes' => $leaveTypes,
        ]);
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
        $holidayDates = Holiday::query()
            ->where(function ($query) {
                $query->whereNull('branch_id')
                    ->orWhere('branch_id', auth()->user()->branch_id);
            })
            ->get()
            ->filter(function ($holiday) use ($startDate, $endDate) {
                $comparisonDate = $holiday->is_recurring
                    ? Carbon::create($startDate->year, $holiday->holiday_date->month, $holiday->holiday_date->day)
                    : $holiday->holiday_date->copy();

                return $comparisonDate->between($startDate, $endDate);
            })
            ->map(fn ($holiday) => ($holiday->is_recurring
                ? Carbon::create($startDate->year, $holiday->holiday_date->month, $holiday->holiday_date->day)
                : $holiday->holiday_date->copy())->toDateString())
            ->unique()
            ->values();

        $totalDays = 0;
        $cursor = $startDate->copy();

        while ($cursor->lte($endDate)) {
            if (!$cursor->isWeekend() && !$holidayDates->contains($cursor->toDateString())) {
                $totalDays++;
            }

            $cursor->addDay();
        }

        if ($totalDays <= 0) {
            return back()->withErrors([
                'start_date' => 'Rentang tanggal yang dipilih hanya berisi weekend atau hari libur.',
            ])->withInput();
        }

        // Check quota
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

        $leaveRequest = LeaveRequest::create([
            'user_id' => auth()->id(),
            'leave_type_id' => $request->leave_type_id,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'total_days' => $totalDays,
            'request_number' => 'LV-' . now()->format('YmdHis') . '-' . auth()->id(),
            'reason' => $request->reason,
            'attachment_path' => $attachmentPath,
        ]);

        // Notify all admins
        $admins = User::where('role', 'admin')->get();
        foreach ($admins as $admin) {
            $admin->notify(new LeaveRequestNotification($leaveRequest, 'submitted'));
        }

        return redirect()->route('employee.leave-requests.index')->with('success', 'Pengajuan cuti berhasil dikirim.');
    }
}
