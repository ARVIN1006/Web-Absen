<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LeaveType;
use Illuminate\Http\Request;

class LeaveTypeController extends Controller
{
    public function index()
    {
        $leaveTypes = LeaveType::latest()->get();
        return view('admin.leave-types.index', compact('leaveTypes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'max_days_per_year' => 'required|integer|min:0',
            'requires_attachment' => 'boolean',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        LeaveType::create($request->all());

        return redirect()->route('admin.leave-types.index')->with('success', 'Tipe cuti berhasil ditambahkan.');
    }

    public function update(Request $request, LeaveType $leaveType)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'max_days_per_year' => 'required|integer|min:0',
            'requires_attachment' => 'boolean',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $leaveType->update($request->all());

        return redirect()->route('admin.leave-types.index')->with('success', 'Tipe cuti berhasil diupdate.');
    }

    public function destroy(LeaveType $leaveType)
    {
        $leaveType->delete();
        return redirect()->route('admin.leave-types.index')->with('success', 'Tipe cuti berhasil dihapus.');
    }
}
