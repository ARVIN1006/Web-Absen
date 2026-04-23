<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Department;
use Illuminate\Http\Request;

class DirectoryController extends Controller
{
    public function index(Request $request)
    {
        $query = User::where('role', 'employee')->with(['position', 'department']);
        
        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }
        if ($request->filled('dept')) {
            $query->where('department_id', $request->dept);
        }
        
        $employees = $query->get();
        $departments = Department::all();

        return \Inertia\Inertia::render('Employee/Directory', [
            'employees' => $employees,
            'departments' => $departments,
            'filters' => $request->only(['search', 'dept'])
        ]);
    }
}
