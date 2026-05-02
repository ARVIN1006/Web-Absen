<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\AttendanceCorrection;
use App\Models\LeaveBalance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class ProfileController extends Controller
{
    public function index()
    {
        $user = Auth::user()->load([
            'profile',
            'position',
            'department',
            'branch',
            'workShift',
            'employmentType',
            'leaveBalances.leaveType',
            'attendanceCorrections' => fn ($query) => $query->latest()->limit(5),
            'leaveRequests.leaveType' => fn ($query) => $query->latest()->limit(5),
            'payrolls' => fn ($query) => $query->latest()->limit(3),
        ]);

        $todayAttendance = $user->attendances()->whereDate('created_at', today())->latest()->first();

        return Inertia::render('Employee/Profile', [
            'employee' => $user,
            'workShift' => $user->workShift,
            'todayAttendance' => $todayAttendance,
            'leaveBalances' => $user->leaveBalances,
            'attendanceCorrections' => $user->attendanceCorrections,
            'recentLeaveRequests' => $user->leaveRequests,
            'recentPayrolls' => $user->payrolls,
        ]);
    }

    public function update(Request $request)
    {
        $user = Auth::user();
        
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'string', 'lowercase', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'phone_number' => ['nullable', 'string', 'max:30'],
            'address' => ['nullable', 'string', 'max:1000'],
            'gender' => ['nullable', 'in:male,female'],
            'birth_date' => ['nullable', 'date'],
            'place_of_birth' => ['nullable', 'string', 'max:100'],
            'personal_email' => ['nullable', 'email', 'max:255'],
            'alternate_phone_number' => ['nullable', 'string', 'max:30'],
            'current_address' => ['nullable', 'string', 'max:1000'],
            'domicile_address' => ['nullable', 'string', 'max:1000'],
            'marital_status' => ['nullable', 'string', 'max:50'],
            'religion' => ['nullable', 'string', 'max:50'],
            'nationality' => ['nullable', 'string', 'max:80'],
            'bank_name' => ['nullable', 'string', 'max:100'],
            'bank_account_number' => ['nullable', 'string', 'max:50'],
            'bank_account_name' => ['nullable', 'string', 'max:100'],
            'password' => ['nullable', 'min:6', 'confirmed'],
        ]);

        $userData = [
            'name' => $validated['name'],
            'email' => $validated['email'] ?? $user->email,
            'phone_number' => $validated['phone_number'] ?? null,
            'address' => $validated['address'] ?? null,
            'gender' => $validated['gender'] ?? null,
            'birth_date' => $validated['birth_date'] ?? null,
        ];

        if (array_key_exists('email', $validated) && $validated['email'] !== $user->email) {
            $userData['email_verified_at'] = null;
        }

        if (!empty($validated['password'])) {
            $userData['password'] = Hash::make($validated['password']);
        }

        $profileData = [
            'employee_code' => $user->profile?->employee_code ?: sprintf('EMP-%05d', $user->id),
            'phone_number' => $validated['phone_number'] ?? null,
            'current_address' => $validated['current_address'] ?? ($validated['address'] ?? null),
            'gender' => $validated['gender'] ?? null,
            'birth_date' => $validated['birth_date'] ?? null,
            'place_of_birth' => $validated['place_of_birth'] ?? null,
            'personal_email' => $validated['personal_email'] ?? null,
            'alternate_phone_number' => $validated['alternate_phone_number'] ?? null,
            'domicile_address' => $validated['domicile_address'] ?? null,
            'marital_status' => $validated['marital_status'] ?? null,
            'religion' => $validated['religion'] ?? null,
            'nationality' => $validated['nationality'] ?? null,
            'bank_name' => $validated['bank_name'] ?? null,
            'bank_account_number' => $validated['bank_account_number'] ?? null,
            'bank_account_name' => $validated['bank_account_name'] ?? null,
        ];

        $user->update($userData);
        $user->profile()->updateOrCreate(['user_id' => $user->id], $profileData);

        return redirect()->route('profile.index')->with('success', 'Profil berhasil diperbarui.');
    }

    public function destroy(Request $request)
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = Auth::user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
