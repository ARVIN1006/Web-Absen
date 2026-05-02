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
        $query = Reimbursement::with(['user.position', 'approver'])->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $reimbursements = $query->get();
        return \Inertia\Inertia::render('Admin/Reimbursements', [
            'reimbursements' => $reimbursements,
            'filters' => $request->only(['status']),
        ]);
    }

    public function updateStatus(Request $request, Reimbursement $reimbursement)
    {
        $request->validate([
            'status' => 'required|in:approved,rejected,paid',
            'admin_note' => 'nullable|string'
        ]);

        if ($request->status === 'paid' && !in_array($reimbursement->status, ['approved', 'paid'], true)) {
            return redirect()->back()->with('error', 'Reimbursement harus disetujui sebelum ditandai dibayar.');
        }

        $payload = [
            'status' => $request->status,
            'admin_note' => $request->admin_note,
            'approved_by' => auth()->id(),
            'approved_at' => now(),
            'reimbursed_at' => $request->status === 'paid' ? now()->toDateString() : null,
        ];

        if ($request->status === 'rejected') {
            $payload['reimbursed_at'] = null;
        }

        if ($request->status === 'paid') {
            $payload['approved_by'] = $reimbursement->approved_by ?: auth()->id();
            $payload['approved_at'] = $reimbursement->approved_at ?: now();
            $payload['admin_note'] = $request->admin_note ?: $reimbursement->admin_note;
        }

        $reimbursement->update($payload);

        app(\App\Services\ActivityLogService::class)->log(
            auth()->id(),
            $reimbursement,
            'reimbursement.' . $request->status,
            match ($request->status) {
                'approved' => 'Menyetujui reimbursement ',
                'paid' => 'Menandai reimbursement dibayar ',
                default => 'Menolak reimbursement ',
            } . ($reimbursement->request_number ?: '#' . $reimbursement->id),
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
