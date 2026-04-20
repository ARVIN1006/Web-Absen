<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Attendance;
use App\Models\CompanySetting;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class AttendanceController extends Controller
{
    public function index()
    {
        $locations = CompanySetting::all();
        return view('attendance.index', compact('locations'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'image' => 'required', // base64 image
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'type' => 'required|in:in,out',
        ]);

        $locations = CompanySetting::all();
        if ($locations->isEmpty()) {
            return response()->json(['success' => false, 'message' => 'Company settings not found.'], 404);
        }

        $minDistance = PHP_INT_MAX;
        $closestLocation = null;

        // Find the closest location
        foreach ($locations as $loc) {
            $distance = $this->calculateDistance(
                $request->latitude,
                $request->longitude,
                $loc->latitude,
                $loc->longitude
            );
            if ($distance < $minDistance) {
                $minDistance = $distance;
                $closestLocation = $loc;
            }
        }

        $status = 'valid';
        if ($minDistance > $closestLocation->radius) {
            return response()->json([
                'success' => false,
                'message' => 'Anda berada di luar area ' . $closestLocation->name . ' (' . round($minDistance) . 'm).',
                'distance' => $minDistance,
                'closestLocation' => $closestLocation->name
            ], 403);
        }

        // 2. Face Image Storage
        $img = $request->image;
        $img = str_replace('data:image/jpeg;base64,', '', $img);
        $img = str_replace(' ', '+', $img);
        $data = base64_decode($img);
        $fileName = 'attendance_' . auth()->id() . '_' . time() . '.jpg';
        $path = 'attendances/' . $fileName;
        Storage::disk('public')->put($path, $data);

        // 3. Late & Overtime Logic
        $user = auth()->user();
        $shift = $user->workShift;
        $lateMinutes = 0;
        $overtimeMinutes = 0;
        $now = Carbon::now();
        $today = $now->toDateString();

        if ($request->type == 'in' && $shift) {
            $shiftStart = Carbon::parse($today . ' ' . $shift->start_time);
            if ($now->greaterThan($shiftStart)) {
                $lateMinutes = $now->diffInMinutes($shiftStart);
            }
        } elseif ($request->type == 'out' && $shift) {
            $shiftEnd = Carbon::parse($today . ' ' . $shift->end_time);
            if ($now->greaterThan($shiftEnd)) {
                $overtimeMinutes = $now->diffInMinutes($shiftEnd);
            }
        }

        // 4. Save Attendance Record
        Attendance::create([
            'user_id' => auth()->id(),
            'type' => $request->type,
            'image_path' => $path,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'status' => $status,
            'late_minutes' => $lateMinutes,
            'overtime_minutes' => $overtimeMinutes,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Attendance ' . ($request->type == 'in' ? 'Check-in' : 'Check-out') . ' successful!',
        ]);
    }

    private function calculateDistance($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 6371000; // in meters

        $latDelta = deg2rad($lat2 - $lat1);
        $lonDelta = deg2rad($lon2 - $lon1);

        $a = sin($latDelta / 2) * sin($latDelta / 2) +
             cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
             sin($lonDelta / 2) * sin($lonDelta / 2);
        
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c;
    }
}
