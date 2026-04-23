<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payroll;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function laborCost(Request $request)
    {
        $year = $request->input('year', now()->year);

        $monthlyCosts = Payroll::select(
                'month',
                DB::raw('SUM(basic_salary) as total_basic'),
                DB::raw('SUM(overtime_pay) as total_overtime'),
                DB::raw('SUM(net_salary) as total_net')
            )
            ->where('year', $year)
            ->where('status', 'paid')
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        return \Inertia\Inertia::render('Admin/Reports/LaborCost', [
            'monthlyCosts' => $monthlyCosts,
            'year' => $year
        ]);
    }

    public function performanceHeatmap()
    {
        $departments = \App\Models\Department::with('users.kpiScores')->get();
        $heatData = [];

        foreach ($departments as $dept) {
            $totalAvg = 0;
            $count = 0;

            foreach ($dept->users as $user) {
                if ($user->kpiScores->isNotEmpty()) {
                    $userAvg = $user->kpiScores->map(function($kpi) {
                        return ($kpi->attendance_score + $kpi->performance_score + $kpi->attitude_score) / 3;
                    })->avg();
                    
                    $totalAvg += $userAvg;
                    $count++;
                }
            }

            $heatData[] = [
                'dept' => $dept->name,
                'avg' => $count > 0 ? $totalAvg / $count : 0,
                'count' => $count
            ];
        }

        return \Inertia\Inertia::render('Admin/Reports/PerformanceHeatmap', [
            'heatData' => $heatData
        ]);
    }
}
