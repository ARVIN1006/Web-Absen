<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Reimbursement;
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
        return view('admin.reimbursements.index', compact('reimbursements'));
    }

    public function updateStatus(Request $request, Reimbursement $reimbursement)
    {
        $request->validate([
            'status' => 'required|in:approved,rejected',
            'admin_note' => 'nullable|string'
        ]);

        $reimbursement->update([
            'status' => $request->status,
            'admin_note' => $request->admin_note
        ]);

        return redirect()->back()->with('success', 'Status reimbursement berhasil diperbarui.');
    }
}
