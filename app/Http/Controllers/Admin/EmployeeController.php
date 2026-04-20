<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class EmployeeController extends Controller
{
    public function index()
    {
        $employees = User::where('role', 'employee')->latest()->get();
        return view('admin.employees', compact('employees'));
    }

    public function create()
    {
        $departments = \App\Models\Department::all();
        $positions = \App\Models\Position::all();
        $workShifts = \App\Models\WorkShift::all();
        return view('admin.employee_create', compact('departments', 'positions', 'workShifts'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6',
            'position_id' => 'required|exists:positions,id',
            'department_id' => 'nullable|exists:departments,id',
            'work_shift_id' => 'nullable|exists:work_shifts,id',
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'position_id' => $request->position_id,
            'department_id' => $request->department_id,
            'work_shift_id' => $request->work_shift_id,
            'role' => 'employee',
        ]);

        return redirect()->route('admin.employees.index')->with('success', 'Karyawan berhasil ditambahkan!');
    }

    public function edit(User $employee)
    {
        $departments = \App\Models\Department::all();
        $positions = \App\Models\Position::all();
        $workShifts = \App\Models\WorkShift::all();
        return view('admin.employee_edit', compact('employee', 'departments', 'positions', 'workShifts'));
    }

    public function update(Request $request, User $employee)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users,email,' . $employee->id,
            'position_id' => 'required|exists:positions,id',
            'department_id' => 'nullable|exists:departments,id',
            'work_shift_id' => 'nullable|exists:work_shifts,id',
            'password' => 'nullable|min:6',
        ]);

        $data = $request->only('name', 'email', 'position_id', 'department_id', 'work_shift_id');
        
        if ($request->filled('password')) {
            $data['password'] = bcrypt($request->password);
        }

        $employee->update($data);

        return redirect()->route('admin.employees.index')->with('success', 'Data karyawan berhasil diupdate!');
    }

    public function destroy(User $employee)
    {
        $employee->delete();
        return redirect()->route('admin.employees.index')->with('success', 'Karyawan berhasil dihapus.');
    }
}
