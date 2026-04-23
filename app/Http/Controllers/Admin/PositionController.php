<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Position;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PositionController extends Controller
{
    public function index()
    {
        return Inertia::render('Admin/Positions', [
            'positions' => Position::withCount('users')->latest()->get(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:50',
            'grade' => 'nullable|string|max:50',
            'salary' => 'required|numeric|min:0',
            'allowance' => 'required|numeric|min:0',
            'overtime_rate' => 'required|numeric|min:0',
            'is_active' => 'required|boolean',
        ]);

        Position::create($validated);

        return redirect()->route('admin.positions.index')->with('success', 'Jabatan berhasil ditambahkan.');
    }

    public function update(Request $request, Position $position)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:50',
            'grade' => 'nullable|string|max:50',
            'salary' => 'required|numeric|min:0',
            'allowance' => 'required|numeric|min:0',
            'overtime_rate' => 'required|numeric|min:0',
            'is_active' => 'required|boolean',
        ]);

        $position->update($validated);

        return redirect()->route('admin.positions.index')->with('success', 'Jabatan berhasil diperbarui.');
    }

    public function destroy(Position $position)
    {
        if ($position->users()->count() > 0) {
            return redirect()->route('admin.positions.index')->with('error', 'Jabatan tidak bisa dihapus karena masih digunakan oleh karyawan.');
        }

        $position->delete();

        return redirect()->route('admin.positions.index')->with('success', 'Jabatan berhasil dihapus.');
    }
}
