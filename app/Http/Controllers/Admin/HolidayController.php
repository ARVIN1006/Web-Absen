<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Holiday;
use Illuminate\Http\Request;
use Inertia\Inertia;

class HolidayController extends Controller
{
    public function index()
    {
        return Inertia::render('Admin/Holidays', [
            'holidays' => Holiday::with('branch:id,name')->orderBy('holiday_date')->get(),
            'branches' => Branch::where('is_active', true)->orderBy('name')->get(['id', 'name']),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'holiday_date' => ['required', 'date'],
            'type' => ['required', 'in:national,company,branch,collective_leave'],
            'branch_id' => ['nullable', 'exists:branches,id'],
            'is_recurring' => ['required', 'boolean'],
            'notes' => ['nullable', 'string'],
        ]);

        Holiday::create($validated);

        return redirect()->route('admin.holidays.index')->with('success', 'Hari libur berhasil ditambahkan.');
    }

    public function update(Request $request, Holiday $holiday)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'holiday_date' => ['required', 'date'],
            'type' => ['required', 'in:national,company,branch,collective_leave'],
            'branch_id' => ['nullable', 'exists:branches,id'],
            'is_recurring' => ['required', 'boolean'],
            'notes' => ['nullable', 'string'],
        ]);

        $holiday->update($validated);

        return redirect()->route('admin.holidays.index')->with('success', 'Hari libur berhasil diperbarui.');
    }

    public function destroy(Holiday $holiday)
    {
        $holiday->delete();

        return redirect()->route('admin.holidays.index')->with('success', 'Hari libur berhasil dihapus.');
    }
}
