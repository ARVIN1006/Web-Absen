<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Reimbursement;
use App\Notifications\ReimbursementNotification;
use Illuminate\Http\Request;

class ReimbursementController extends Controller
{
    public function index(Request $request)
    {
        $query = Reimbursement::with('user')->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $reimbursements = $query->get();
        return \Inertia\Inertia::render('Admin/Reimbursements', [
            'reimbursements' => $reimbursements
        ]);
    }

    public function updateStatus(Request $request, Reimbursement $reimbursement)
    {
        $request->validate([
            'status' => 'required|in:approved,rejected',
            'admin_note' => 'nullable|string'
        ]);

        $reimbursement->update([
            'status' => $request->status,
            'admin_note' => $request->admin_note,
            'approved_by' => auth()->id(),
            'approved_at' => now(),
            'reimbursed_at' => $request->status === 'approved' ? now()->toDateString() : null,
        ]);

        app(\App\Services\ActivityLogService::class)->log(
            auth()->id(),
            $reimbursement,
            'reimbursement.' . $request->status,
            ($request->status === 'approved' ? 'Menyetujui' : 'Menolak') . ' reimbursement ' . ($reimbursement->request_number ?: '#' . $reimbursement->id),
            [
                'user_id' => $reimbursement->user_id,
                'amount' => $reimbursement->amount,
                'admin_note' => $request->admin_note,
            ]
        );

        // Notify the employee
        $reimbursement->load('user');
        $reimbursement->user->notify(new ReimbursementNotification($reimbursement, $request->status));

        return redirect()->back()->with('success', 'Status reimbursement berhasil diperbarui.');
    }
}
