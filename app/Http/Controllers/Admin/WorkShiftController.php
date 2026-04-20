<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\WorkShift;
use Illuminate\Http\Request;

class WorkShiftController extends Controller
{
    public function index()
    {
        $shifts = WorkShift::latest()->get();
        return view('admin.work-shifts.index', compact('shifts'));
    }

    public function create()
    {
        return view('admin.work-shifts.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'clock_in_time' => 'required|date_format:H:i',
            'clock_out_time' => 'required|date_format:H:i',
            'late_tolerance_minutes' => 'required|integer|min:0',
            'is_default' => 'boolean',
        ]);

        if ($request->has('is_default') && $request->is_default) {
            WorkShift::query()->update(['is_default' => false]);
        }

        WorkShift::create($request->all());

        return redirect()->route('admin.work-shifts.index')->with('success', 'Shift kerja berhasil ditambahkan.');
    }

    public function edit(WorkShift $workShift)
    {
        return view('admin.work-shifts.edit', compact('workShift'));
    }

    public function update(Request $request, WorkShift $workShift)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'clock_in_time' => 'required|date_format:H:i',
            'clock_out_time' => 'required|date_format:H:i',
            'late_tolerance_minutes' => 'required|integer|min:0',
            'is_default' => 'boolean',
        ]);

        if ($request->has('is_default') && $request->is_default) {
            WorkShift::where('id', '!=', $workShift->id)->update(['is_default' => false]);
        }

        $workShift->update($request->all());

        return redirect()->route('admin.work-shifts.index')->with('success', 'Shift kerja berhasil diupdate.');
    }

    public function destroy(WorkShift $workShift)
    {
        $workShift->delete();
        return redirect()->route('admin.work-shifts.index')->with('success', 'Shift kerja berhasil dihapus.');
    }
}
