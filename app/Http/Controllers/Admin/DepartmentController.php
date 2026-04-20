<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\User;
use Illuminate\Http\Request;

class DepartmentController extends Controller
{
    public function index()
    {
        $departments = Department::with('head')->latest()->get();
        return view('admin.departments.index', compact('departments'));
    }

    public function create()
    {
        $users = User::where('role', 'employee')->get();
        return view('admin.departments.create', compact('users'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:departments,code',
            'description' => 'nullable|string',
            'head_id' => 'nullable|exists:users,id',
            'is_active' => 'boolean',
        ]);

        Department::create($request->all());

        return redirect()->route('admin.departments.index')->with('success', 'Departemen berhasil ditambahkan.');
    }

    public function edit(Department $department)
    {
        $users = User::where('role', 'employee')->get();
        return view('admin.departments.edit', compact('department', 'users'));
    }

    public function update(Request $request, Department $department)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:departments,code,' . $department->id,
            'description' => 'nullable|string',
            'head_id' => 'nullable|exists:users,id',
            'is_active' => 'boolean',
        ]);

        $department->update($request->all());

        return redirect()->route('admin.departments.index')->with('success', 'Departemen berhasil diupdate.');
    }

    public function destroy(Department $department)
    {
        $department->delete();
        return redirect()->route('admin.departments.index')->with('success', 'Departemen berhasil dihapus.');
    }
}
