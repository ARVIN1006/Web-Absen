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
        return \Inertia\Inertia::render('Admin/LeaveTypes', [
            'leaveTypes' => $leaveTypes
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:leave_types,code',
            'max_days_per_year' => 'required|integer|min:0',
            'is_paid' => 'boolean',
            'requires_attachment' => 'boolean',
            'requires_balance' => 'boolean',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        LeaveType::create([
            ...$validated,
            'is_paid' => $request->boolean('is_paid'),
            'requires_attachment' => $request->boolean('requires_attachment'),
            'requires_balance' => $request->boolean('requires_balance'),
            'is_active' => $request->boolean('is_active', true),
        ]);

        return redirect()->route('admin.leave-types.index')->with('success', 'Tipe cuti berhasil ditambahkan.');
    }

    public function update(Request $request, LeaveType $leaveType)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:leave_types,code,' . $leaveType->id,
            'max_days_per_year' => 'required|integer|min:0',
            'is_paid' => 'boolean',
            'requires_attachment' => 'boolean',
            'requires_balance' => 'boolean',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $leaveType->update([
            ...$validated,
            'is_paid' => $request->boolean('is_paid'),
            'requires_attachment' => $request->boolean('requires_attachment'),
            'requires_balance' => $request->boolean('requires_balance'),
            'is_active' => $request->boolean('is_active', true),
        ]);

        return redirect()->route('admin.leave-types.index')->with('success', 'Tipe cuti berhasil diupdate.');
    }

    public function destroy(LeaveType $leaveType)
    {
        $leaveType->delete();
        return redirect()->route('admin.leave-types.index')->with('success', 'Tipe cuti berhasil dihapus.');
    }
}
