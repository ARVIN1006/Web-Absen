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
        $company = CompanySetting::first();
        return view('attendance.index', compact('company'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'image' => 'required', // base64 image
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'type' => 'required|in:in,out',
        ]);

        $company = CompanySetting::first();
        if (!$company) {
            return response()->json(['success' => false, 'message' => 'Company settings not found.'], 404);
        }

        // 1. Geolocation Verification (Haversine Formula)
        $distance = $this->calculateDistance(
            $request->latitude,
            $request->longitude,
            $company->latitude,
            $company->longitude
        );

        $status = 'valid';
        if ($distance > $company->radius) {
            return response()->json([
                'success' => false,
                'message' => 'You are outside the company radius ('.round($distance).'m). Attendance is not valid.',
                'distance' => $distance
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

        // 3. Save Attendance Record
        Attendance::create([
            'user_id' => auth()->id(),
            'type' => $request->type,
            'image_path' => $path,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'status' => $status,
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
