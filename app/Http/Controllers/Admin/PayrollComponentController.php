<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PayrollComponent;
use Inertia\Inertia;
use Illuminate\Http\Request;

class PayrollComponentController extends Controller
{
    public function index()
    {
        return Inertia::render('Admin/PayrollComponents', [
            'components' => PayrollComponent::withCount('items')->latest()->get(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'code' => ['required', 'string', 'max:50', 'unique:payroll_components,code'],
            'type' => ['required', 'in:earning,deduction'],
            'calculation_method' => ['required', 'in:manual,auto'],
            'is_taxable' => ['required', 'boolean'],
            'is_active' => ['required', 'boolean'],
        ]);

        PayrollComponent::create($validated);

        return redirect()->route('admin.payroll-components.index')->with('success', 'Komponen payroll berhasil ditambahkan.');
    }

    public function update(Request $request, PayrollComponent $payrollComponent)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'code' => ['required', 'string', 'max:50', 'unique:payroll_components,code,' . $payrollComponent->id],
            'type' => ['required', 'in:earning,deduction'],
            'calculation_method' => ['required', 'in:manual,auto'],
            'is_taxable' => ['required', 'boolean'],
            'is_active' => ['required', 'boolean'],
        ]);

        $payrollComponent->update($validated);

        return redirect()->route('admin.payroll-components.index')->with('success', 'Komponen payroll berhasil diperbarui.');
    }

    public function destroy(PayrollComponent $payrollComponent)
    {
        if ($payrollComponent->items()->count() > 0) {
            return redirect()->route('admin.payroll-components.index')->with('error', 'Komponen payroll tidak bisa dihapus karena sudah dipakai.');
        }

        $payrollComponent->delete();

        return redirect()->route('admin.payroll-components.index')->with('success', 'Komponen payroll berhasil dihapus.');
    }
}
