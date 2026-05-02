<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\KpiScore;
use App\Models\User;
use Illuminate\Http\Request;

class KpiController extends Controller
{
    public function index(Request $request)
    {
        $query = KpiScore::with(['user.position'])->latest();

        if ($request->filled('period')) {
            $query->where('period', 'like', '%' . $request->period . '%');
        }

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        $kpis = $query->get();
        $employees = User::where('role', 'employee')->orderBy('name')->get(['id', 'name']);

        return \Inertia\Inertia::render('Admin/Kpi', [
            'kpis' => $kpis,
            'employees' => $employees,
            'filters' => $request->only(['period', 'user_id']),
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
            'feedback' => 'nullable|string',
        ]);

        KpiScore::create($request->only([
            'user_id',
            'period',
            'attendance_score',
            'performance_score',
            'attitude_score',
            'feedback',
        ]));

        return redirect()->back()->with('success', 'Skor KPI berhasil disimpan.');
    }

    public function update(Request $request, KpiScore $kpiScore)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'period' => 'required|string|max:100',
            'attendance_score' => 'required|integer|min:0|max:100',
            'performance_score' => 'required|integer|min:0|max:100',
            'attitude_score' => 'required|integer|min:0|max:100',
            'feedback' => 'nullable|string',
        ]);

        $kpiScore->update($request->only([
            'user_id',
            'period',
            'attendance_score',
            'performance_score',
            'attitude_score',
            'feedback',
        ]));

        return redirect()->back()->with('success', 'Skor KPI berhasil diperbarui.');
    }

    public function destroy(KpiScore $kpiScore)
    {
        $kpiScore->delete();

        return redirect()->back()->with('success', 'Skor KPI berhasil dihapus.');
    }
}
