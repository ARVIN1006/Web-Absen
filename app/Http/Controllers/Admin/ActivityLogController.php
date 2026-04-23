<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use Inertia\Inertia;

class ActivityLogController extends Controller
{
    public function index()
    {
        return Inertia::render('Admin/ActivityLogs', [
            'logs' => ActivityLog::with('actor:id,name,email')
                ->latest()
                ->limit(200)
                ->get(),
        ]);
    }
}
