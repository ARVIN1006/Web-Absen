<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Department;
use App\Models\WorkShift;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        $departments = Department::orderBy('name')->get();
        $positions = [
            'Manager',
            'Supervisor',
            'Staff IT',
            'Staff HRD',
            'Staff Finance',
            'Staff Marketing',
            'Admin',
            'Security',
            'Driver',
            'Office Boy'
        ];
        return view('auth.register', compact('departments', 'positions'));
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'position' => ['required', 'string', 'max:100'],
            'department_id' => ['required', 'exists:departments,id'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'face_data' => ['required', 'string'], // base64 string
        ]);

        $imagePath = null;
        if ($request->filled('face_data')) {
            $image_parts = explode(";base64,", $request->face_data);
            if (count($image_parts) == 2) {
                $image_base64 = base64_decode($image_parts[1]);
                $fileName = 'registered_face_' . time() . '_' . uniqid() . '.jpg';
                \Illuminate\Support\Facades\Storage::disk('public')->put('faces/' . $fileName, $image_base64);
                $imagePath = 'faces/' . $fileName;
            }
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'position' => $request->position,
            'department_id' => $request->department_id,
            'work_shift_id' => WorkShift::where('is_default', true)->first()?->id,
            'password' => Hash::make($request->password),
            'face_reference_path' => $imagePath,
        ]);

        event(new Registered($user));

        Auth::login($user);

        return redirect(route('dashboard', absolute: false));
    }
}
