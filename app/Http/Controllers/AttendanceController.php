<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Location;
use App\Services\FaceVerificationService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;

class AttendanceController extends Controller
{
    public function __construct(private readonly FaceVerificationService $faceVerificationService)
    {
    }

    public function index()
    {
        $locations = Location::where('is_active', true)->get();
        $todayAttendances = Attendance::where('user_id', auth()->id())
            ->whereDate('created_at', Carbon::today())
            ->latest()
            ->get();

        return \Inertia\Inertia::render('Attendance', [
            'locations' => $locations,
            'todayAttendances' => $todayAttendances,
            'demoMode' => config('app.demo_mode'),
            'attendanceRules' => [
                'bypass_face_verification' => config('app.demo_bypass_face_verification'),
                'bypass_geofence' => config('app.demo_bypass_geofence'),
            ],
        ]);
    }

    public function demoSync(Request $request)
    {
        $request->validate([
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
        ]);

        $location = Location::where('code', 'JKT-HO-MAIN')->first()
            ?: Location::where('is_active', true)->first()
            ?: Location::first();

        if (!$location) {
            $location = new Location();
            $location->code = 'DEMO';
            $location->enforce_face_verification = true;
        }

        $location->name = 'Lokasi Demo - Posisi Client';
        $location->latitude = $request->latitude;
        $location->longitude = $request->longitude;
        $location->radius = max((int) ($location->radius ?: 150), 150);
        $location->is_active = true;
        $location->save();

        return response()->json([
            'success' => true,
            'message' => 'Lokasi kantor telah disesuaikan ke posisi Anda.',
            'location' => $location->fresh(),
        ]);
    }

    public function demoResetToday(): RedirectResponse
    {
        if (!config('app.demo_mode') && !app()->environment('local')) {
            abort(403);
        }

        Attendance::where('user_id', auth()->id())
            ->whereDate('created_at', Carbon::today())
            ->delete();

        return back()->with('success', 'Status presensi hari ini untuk akun ini sudah direset. Silakan ulangi demo Clock-in/Clock-out.');
    }

    public function store(Request $request)
    {
        $request->validate([
            'image' => 'required',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'type' => 'required|in:in,out',
            'face_descriptor' => 'nullable|array|size:128',
            'face_descriptor.*' => 'numeric',
        ]);

        $user = auth()->user();
        $today = Carbon::today()->toDateString();
        $faceReferencePath = $user->profile?->face_reference_path ?: $user->face_reference_path;
        $faceReferenceDescriptor = $user->profile?->face_descriptor ?: $user->face_descriptor;
        $demoBypassFace = config('app.demo_mode') && config('app.demo_bypass_face_verification');
        $demoBypassGeofence = config('app.demo_mode') && config('app.demo_bypass_geofence');

        if (!$faceReferencePath && !$faceReferenceDescriptor && !$demoBypassFace) {
            return $this->attendanceError('Foto referensi wajah akun belum tersedia. Hubungi admin HR untuk memperbarui data biometrik Anda.');
        }

        $alreadyExists = Attendance::where('user_id', $user->id)
            ->whereDate('created_at', $today)
            ->where('type', $request->type)
            ->exists();

        if ($alreadyExists) {
            $label = $request->type === 'in' ? 'Clock-in' : 'Clock-out';

            return $this->attendanceError("Anda sudah melakukan {$label} hari ini.");
        }

        if ($request->type === 'out') {
            $hasClockIn = Attendance::where('user_id', $user->id)
                ->whereDate('created_at', $today)
                ->where('type', 'in')
                ->exists();

            if (!$hasClockIn) {
                return $this->attendanceError('Anda belum melakukan Clock-in hari ini.');
            }
        }

        $locations = Location::where('is_active', true)->get();

        if ($locations->isEmpty()) {
            return $this->attendanceError('Lokasi kantor belum dikonfigurasi.');
        }

        $minDistance = PHP_INT_MAX;
        $closestLocation = null;

        foreach ($locations as $location) {
            $distance = $this->calculateDistance(
                $request->latitude,
                $request->longitude,
                $location->latitude,
                $location->longitude
            );

            if ($distance < $minDistance) {
                $minDistance = $distance;
                $closestLocation = $location;
            }
        }

        $roundedDistance = (int) round($minDistance);

        if ((!$closestLocation || $minDistance > $closestLocation->radius) && !$demoBypassGeofence) {
            return $this->attendanceError('Anda berada di luar area ' . ($closestLocation?->name ?? 'kantor') . ' (' . $roundedDistance . 'm).');
        }

        if ($demoBypassFace) {
            $faceVerification = [
                'matched' => true,
                'score' => 100,
                'threshold' => 75,
                'reason' => 'Demo mode bypass aktif.',
            ];
        } elseif ($faceReferenceDescriptor && $request->filled('face_descriptor')) {
            $faceVerification = $this->faceVerificationService->verifyDescriptor($faceReferenceDescriptor, $request->face_descriptor);
        } elseif ($faceReferencePath) {
            $faceVerification = $this->faceVerificationService->verify($faceReferencePath, $request->image);
        } else {
            return $this->attendanceError('Descriptor wajah presensi belum tersedia. Ambil ulang foto dengan wajah terlihat jelas.');
        }

        if ($closestLocation->enforce_face_verification && !$faceVerification['matched']) {
            return $this->attendanceError($faceVerification['reason'] . ' Skor kemiripan: ' . $faceVerification['score'] . '%.');
        }

        $img = str_replace('data:image/jpeg;base64,', '', $request->image);
        $img = str_replace(' ', '+', $img);
        $data = base64_decode($img);

        $fileName = 'attendance_' . $user->id . '_' . time() . '.jpg';
        $path = 'attendances/' . $fileName;

        $optimizedImage = ImageManager::gd()->read($data)
            ->scale(width: 640)
            ->toJpeg(quality: 75);

        Storage::disk('public')->put($path, (string) $optimizedImage);

        $shift = $user->workShift;
        $lateStatus = 'on_time';
        $lateMinutes = 0;
        $overtimeMinutes = 0;
        $now = Carbon::now();

        if ($request->type === 'in' && $shift) {
            $shiftStart = Carbon::parse($today . ' ' . $shift->clock_in_time);
            $tolerance = $shift->late_tolerance_minutes ?? 0;
            $deadlineTime = $shiftStart->copy()->addMinutes($tolerance);

            if ($now->greaterThan($deadlineTime)) {
                $lateStatus = 'late';
                $lateMinutes = (int) $now->diffInMinutes($shiftStart);
            }
        } elseif ($request->type === 'out' && $shift) {
            $shiftEnd = Carbon::parse($today . ' ' . $shift->clock_out_time);

            if ($now->greaterThan($shiftEnd)) {
                $overtimeMinutes = (int) $now->diffInMinutes($shiftEnd);
            }
        }

        Attendance::create([
            'user_id' => $user->id,
            'attendance_date' => $today,
            'type' => $request->type,
            'check_in_at' => $request->type === 'in' ? $now : null,
            'check_out_at' => $request->type === 'out' ? $now : null,
            'image_path' => $path,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'location_id' => $closestLocation->id,
            'status' => 'valid',
            'face_verified' => $faceVerification['matched'],
            'face_match_score' => $faceVerification['score'],
            'distance_meters' => $demoBypassGeofence ? 0 : $roundedDistance,
            'late_status' => $request->type === 'in' ? $lateStatus : null,
            'late_minutes' => $lateMinutes,
            'overtime_minutes' => $overtimeMinutes,
            'notes' => 'Lokasi: ' . $closestLocation->name . ' | Face score: ' . $faceVerification['score'] . '%' . ($demoBypassFace || $demoBypassGeofence ? ' | Demo mode aktif' : ''),
        ]);

        $message = $request->type === 'in'
            ? 'Check-in berhasil!' . ($lateStatus === 'late' ? " (Terlambat {$lateMinutes} menit)" : ' (Tepat waktu)')
            : 'Check-out berhasil!' . ($overtimeMinutes > 0 ? " (Lembur {$overtimeMinutes} menit)" : '');

        return back()->with('success', $message . ' Verifikasi wajah ' . $faceVerification['score'] . '%.' . ($demoBypassFace || $demoBypassGeofence ? ' Demo mode aktif untuk presentasi.' : ''));
    }

    private function attendanceError(string $message): RedirectResponse
    {
        return back()->withErrors(['message' => $message])->withInput();
    }

    private function calculateDistance($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 6371000;

        $latDelta = deg2rad($lat2 - $lat1);
        $lonDelta = deg2rad($lon2 - $lon1);

        $a = sin($latDelta / 2) * sin($latDelta / 2) +
             cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
             sin($lonDelta / 2) * sin($lonDelta / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c;
    }
}
