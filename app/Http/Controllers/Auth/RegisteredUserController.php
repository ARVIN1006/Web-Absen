<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Department;
use App\Models\Position;
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
    public function create()
    {
        $departments = Department::orderBy('name')->get();
        $positions = Position::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name']);
        return \Inertia\Inertia::render('Auth/Register', [
            'departments' => $departments,
            'positions' => $positions
        ]);
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
            'position_id' => ['nullable', 'exists:positions,id'],
            'department_id' => ['nullable', 'exists:departments,id'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'face_data' => ['nullable', 'string'], // base64 string
            'face_descriptor' => ['nullable', 'array', 'size:128'],
            'face_descriptor.*' => ['numeric'],
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
            'position_id' => $request->position_id,
            'department_id' => $request->department_id,
            'work_shift_id' => WorkShift::where('is_default', true)->first()?->id,
            'password' => Hash::make($request->password),
            'face_reference_path' => $imagePath,
            'face_descriptor' => $request->face_descriptor ?: null,
        ]);

        event(new Registered($user));

        Auth::login($user);

        return redirect(route('dashboard', absolute: false));
    }
}
