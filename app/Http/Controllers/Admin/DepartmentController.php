<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Department;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DepartmentController extends Controller
{
    public function index()
    {
        $departments = Department::query()
            ->with(['head:id,name', 'branch:id,name', 'parent:id,name'])
            ->withCount('users')
            ->latest()
            ->get();

        return Inertia::render('Admin/Departments', [
            'departments' => $departments,
            'users' => User::where('role', 'employee')->orderBy('name')->get(['id', 'name']),
            'branches' => Branch::where('is_active', true)->orderBy('name')->get(['id', 'name']),
            'parents' => Department::orderBy('name')->get(['id', 'name']),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:departments,code',
            'description' => 'nullable|string',
            'head_id' => 'nullable|exists:users,id',
            'parent_id' => 'nullable|exists:departments,id',
            'branch_id' => 'nullable|exists:branches,id',
            'is_active' => 'required|boolean',
        ]);

        Department::create($validated);

        return redirect()->route('admin.departments.index')->with('success', 'Departemen berhasil ditambahkan.');
    }

    public function update(Request $request, Department $department)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:departments,code,' . $department->id,
            'description' => 'nullable|string',
            'head_id' => 'nullable|exists:users,id',
            'parent_id' => 'nullable|exists:departments,id|different:id',
            'branch_id' => 'nullable|exists:branches,id',
            'is_active' => 'required|boolean',
        ]);

        $department->update($validated);

        return redirect()->route('admin.departments.index')->with('success', 'Departemen berhasil diperbarui.');
    }

    public function destroy(Department $department)
    {
        $department->delete();

        return redirect()->route('admin.departments.index')->with('success', 'Departemen berhasil dihapus.');
    }
}
