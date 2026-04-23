<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\KpiScore;
use App\Models\User;
use Illuminate\Http\Request;

class KpiController extends Controller
{
    public function index()
    {
        $kpis = KpiScore::with('user')->latest()->get();
        $employees = User::where('role', 'employee')->get();
        return \Inertia\Inertia::render('Admin/Kpi', [
            'kpis' => $kpis,
            'employees' => $employees
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'period' => 'required',
            'attendance_score' => 'required|integer|min:0|max:100',
            'performance_score' => 'required|integer|min:0|max:100',
            'attitude_score' => 'required|integer|min:0|max:100',
        ]);

        KpiScore::create($request->all());

        return redirect()->back()->with('success', 'Skor KPI berhasil disimpan.');
    }
}
