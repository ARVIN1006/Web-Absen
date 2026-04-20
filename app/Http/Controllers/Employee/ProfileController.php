<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        return view('employee.profile', compact('user'));
    }

    public function update(Request $request)
    {
        $user = Auth::user();
        
        $request->validate([
            'phone_number' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'gender' => 'nullable|in:male,female',
            'birth_date' => 'nullable|date',
            'password' => 'nullable|min:6|confirmed'
        ]);

        $data = $request->only('phone_number', 'address', 'gender', 'birth_date');
        
        if ($request->filled('password')) {
            $data['password'] = bcrypt($request->password);
        }

        $user->update($data);

        return redirect()->back()->with('success', 'Profil berhasil diperbarui.');
    }
}
