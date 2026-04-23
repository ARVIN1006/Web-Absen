<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\Reimbursement;
use App\Models\User;
use App\Notifications\ReimbursementNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ReimbursementController extends Controller
{
    public function index()
    {
        $reimbursements = Auth::user()->reimbursements()->latest()->get();
        return \Inertia\Inertia::render('Employee/Reimbursements', [
            'reimbursements' => $reimbursements
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'type' => 'required|string',
            'attachment' => 'required|image|max:2048',
            'description' => 'nullable|string'
        ]);

        $path = null;
        if ($request->hasFile('attachment')) {
            $path = $request->file('attachment')->store('reimbursements', 'public');
        }

        $reimbursement = Reimbursement::create([
            'user_id' => Auth::id(),
            'title' => $request->title,
            'amount' => $request->amount,
            'type' => $request->type,
            'description' => $request->description,
            'attachment_path' => $path,
            'status' => 'pending'
        ]);

        // Notify all admins
        $admins = User::where('role', 'admin')->get();
        foreach ($admins as $admin) {
            $admin->notify(new ReimbursementNotification($reimbursement, 'submitted'));
        }

        return redirect()->route('reimbursements.index')->with('success', 'Klaim reimbursement berhasil diajukan.');
    }
}
