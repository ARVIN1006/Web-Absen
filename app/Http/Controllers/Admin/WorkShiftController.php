<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\WorkShift;
use Illuminate\Http\Request;
use Inertia\Inertia;

class WorkShiftController extends Controller
{
    public function index()
    {
        return Inertia::render('Admin/WorkShifts', [
            'shifts' => WorkShift::with(['branch:id,name'])->latest()->get(),
            'branches' => Branch::where('is_active', true)->orderBy('name')->get(['id', 'name']),
        ]);
    }

    public function create()
    {
        return Inertia::render('Admin/WorkShiftCreate', [
            'branches' => Branch::where('is_active', true)->orderBy('name')->get(['id', 'name']),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'branch_id' => 'nullable|exists:branches,id',
            'name' => 'required|string|max:255',
            'clock_in_time' => 'required|date_format:H:i',
            'clock_out_time' => 'required|date_format:H:i',
            'break_start_time' => 'nullable|date_format:H:i',
            'break_end_time' => 'nullable|date_format:H:i',
            'late_tolerance_minutes' => 'required|integer|min:0',
            'work_days' => 'nullable|array',
            'work_days.*' => 'in:mon,tue,wed,thu,fri,sat,sun',
            'is_default' => 'required|boolean',
        ]);

        if ($validated['is_default']) {
            WorkShift::query()->update(['is_default' => false]);
        }

        WorkShift::create($validated);

        return redirect()->route('admin.work-shifts.index')->with('success', 'Shift kerja berhasil ditambahkan.');
    }

    public function edit(WorkShift $workShift)
    {
        return Inertia::render('Admin/WorkShiftEdit', [
            'workShift' => $workShift->load('branch:id,name'),
            'branches' => Branch::where('is_active', true)->orderBy('name')->get(['id', 'name']),
        ]);
    }

    public function update(Request $request, WorkShift $workShift)
    {
        $validated = $request->validate([
            'branch_id' => 'nullable|exists:branches,id',
            'name' => 'required|string|max:255',
            'clock_in_time' => 'required|date_format:H:i',
            'clock_out_time' => 'required|date_format:H:i',
            'break_start_time' => 'nullable|date_format:H:i',
            'break_end_time' => 'nullable|date_format:H:i',
            'late_tolerance_minutes' => 'required|integer|min:0',
            'work_days' => 'nullable|array',
            'work_days.*' => 'in:mon,tue,wed,thu,fri,sat,sun',
            'is_default' => 'required|boolean',
        ]);

        if ($validated['is_default']) {
            WorkShift::where('id', '!=', $workShift->id)->update(['is_default' => false]);
        }

        $workShift->update($validated);

        return redirect()->route('admin.work-shifts.index')->with('success', 'Shift kerja berhasil diperbarui.');
    }

    public function destroy(WorkShift $workShift)
    {
        if ($workShift->users()->count() > 0) {
            return redirect()->route('admin.work-shifts.index')->with('error', 'Shift tidak bisa dihapus karena masih dipakai karyawan.');
        }

        $workShift->delete();

        return redirect()->route('admin.work-shifts.index')->with('success', 'Shift kerja berhasil dihapus.');
    }
}
