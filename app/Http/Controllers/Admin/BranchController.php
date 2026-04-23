<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use Inertia\Inertia;
use Illuminate\Http\Request;

class BranchController extends Controller
{
    public function index()
    {
        return Inertia::render('Admin/Branches', [
            'branches' => Branch::withCount(['users', 'departments', 'locations'])->latest()->get(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:branches,code',
            'phone_number' => 'nullable|string|max:30',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string',
            'city' => 'nullable|string|max:100',
            'province' => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:20',
            'is_head_office' => 'required|boolean',
            'is_active' => 'required|boolean',
        ]);

        if ($validated['is_head_office']) {
            Branch::query()->update(['is_head_office' => false]);
        }

        Branch::create($validated);

        return redirect()->route('admin.branches.index')->with('success', 'Cabang berhasil ditambahkan.');
    }

    public function update(Request $request, Branch $branch)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:branches,code,' . $branch->id,
            'phone_number' => 'nullable|string|max:30',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string',
            'city' => 'nullable|string|max:100',
            'province' => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:20',
            'is_head_office' => 'required|boolean',
            'is_active' => 'required|boolean',
        ]);

        if ($validated['is_head_office']) {
            Branch::where('id', '!=', $branch->id)->update(['is_head_office' => false]);
        }

        $branch->update($validated);

        return redirect()->route('admin.branches.index')->with('success', 'Cabang berhasil diperbarui.');
    }

    public function destroy(Branch $branch)
    {
        if ($branch->users()->count() > 0 || $branch->departments()->count() > 0 || $branch->locations()->count() > 0) {
            return redirect()->route('admin.branches.index')->with('error', 'Cabang tidak bisa dihapus karena masih dipakai data lain.');
        }

        $branch->delete();

        return redirect()->route('admin.branches.index')->with('success', 'Cabang berhasil dihapus.');
    }
}
