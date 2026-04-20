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

    public function edit(User $employee)
    {
        $departments = \App\Models\Department::all();
        $workShifts = \App\Models\WorkShift::all();
        return view('admin.employee_edit', compact('employee', 'departments', 'workShifts'));
    }

    public function update(Request $request, User $employee)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users,email,' . $employee->id,
            'position' => 'required',
            'department_id' => 'nullable|exists:departments,id',
            'work_shift_id' => 'nullable|exists:work_shifts,id',
        ]);

        $employee->update($request->only('name', 'email', 'position', 'department_id', 'work_shift_id'));

        return redirect()->route('admin.employees.index')->with('success', 'Data karyawan berhasil diupdate!');
    }

    public function destroy(User $employee)
    {
        $employee->delete();
        return redirect()->route('admin.employees.index')->with('success', 'Karyawan berhasil dihapus.');
    }
}
