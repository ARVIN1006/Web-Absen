<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EmploymentType;
use Illuminate\Http\Request;
use Inertia\Inertia;

class EmploymentTypeController extends Controller
{
    public function index()
    {
        return Inertia::render('Admin/EmploymentTypes', [
            'employmentTypes' => EmploymentType::withCount('users')->latest()->get(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:employment_types,code',
            'category' => 'required|string|max:50',
            'description' => 'nullable|string',
            'is_active' => 'required|boolean',
        ]);

        EmploymentType::create($validated);

        return redirect()->route('admin.employment-types.index')->with('success', 'Employment type berhasil ditambahkan.');
    }

    public function update(Request $request, EmploymentType $employmentType)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:employment_types,code,' . $employmentType->id,
            'category' => 'required|string|max:50',
            'description' => 'nullable|string',
            'is_active' => 'required|boolean',
        ]);

        $employmentType->update($validated);

        return redirect()->route('admin.employment-types.index')->with('success', 'Employment type berhasil diperbarui.');
    }

    public function destroy(EmploymentType $employmentType)
    {
        if ($employmentType->users()->count() > 0) {
            return redirect()->route('admin.employment-types.index')->with('error', 'Employment type tidak bisa dihapus karena masih dipakai.');
        }

        $employmentType->delete();

        return redirect()->route('admin.employment-types.index')->with('success', 'Employment type berhasil dihapus.');
    }
}
