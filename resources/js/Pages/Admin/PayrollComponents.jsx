import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout";
import { Head, useForm } from "@inertiajs/react";
import { useState } from "react";

export default function PayrollComponents({ components, flash }) {
    const [showModal, setShowModal] = useState(false);
    const [editingComponent, setEditingComponent] = useState(null);
    const { data, setData, post, put, delete: destroy, processing, reset } = useForm({
        name: "",
        code: "",
        type: "earning",
        calculation_method: "manual",
        is_taxable: true,
        is_active: true,
    });

    const openCreate = () => {
        setEditingComponent(null);
        reset();
        setData("type", "earning");
        setData("calculation_method", "manual");
        setData("is_taxable", true);
        setData("is_active", true);
        setShowModal(true);
    };

    const openEdit = (component) => {
        setEditingComponent(component);
        setData({
            name: component.name || "",
            code: component.code || "",
            type: component.type || "earning",
            calculation_method: component.calculation_method || "manual",
            is_taxable: component.is_taxable,
            is_active: component.is_active,
        });
        setShowModal(true);
    };

    const closeModal = () => {
        setShowModal(false);
        setEditingComponent(null);
        reset();
    };

    const submit = (event) => {
        event.preventDefault();
        if (editingComponent) {
            put(route("admin.payroll-components.update", editingComponent.id), { onSuccess: closeModal });
            return;
        }
        post(route("admin.payroll-components.store"), { onSuccess: closeModal });
    };

    return (
        <AuthenticatedLayout>
            <Head title="Payroll Components" />

            <section className="ui-card overflow-hidden">
                <div className="flex flex-col gap-5 p-6 lg:flex-row lg:items-end lg:justify-between lg:p-8">
                    <div>
                        <div className="ui-section-title">Payroll Component Master</div>
                        <h1 className="font-heading mt-3 text-3xl font-bold text-black">Komponen payroll</h1>
                    </div>
                    <button type="button" onClick={openCreate} className="ui-button-primary">Tambah Komponen</button>
                </div>
            </section>

            {flash?.success && <div className="mt-6 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-700">{flash.success}</div>}
            {flash?.error && <div className="mt-6 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm font-medium text-red-700">{flash.error}</div>}

            <section className="ui-card mt-6 overflow-hidden">
                <div className="overflow-x-auto">
                    <table className="min-w-full divide-y divide-[var(--border-line)] text-sm">
                        <thead className="bg-[var(--bg-subtle)] text-left text-[11px] font-semibold uppercase tracking-[0.14em] text-[var(--text-soft)]">
                            <tr>
                                <th className="px-6 py-4">Komponen</th>
                                <th className="px-6 py-4">Konfigurasi</th>
                                <th className="px-6 py-4">Pemakaian</th>
                                <th className="px-6 py-4 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody className="divide-y divide-[var(--border-line)]">
                            {components.map((component) => (
                                <tr key={component.id}>
                                    <td className="px-6 py-4">
                                        <div className="font-semibold text-[var(--text-main)]">{component.name}</div>
                                        <div className="mt-1 text-xs text-[var(--text-muted)]">{component.code}</div>
                                    </td>
                                    <td className="px-6 py-4 text-[var(--text-muted)]">
                                        <div>Type: {component.type}</div>
                                        <div className="mt-1">Method: {component.calculation_method}</div>
                                        <div className="mt-1">Taxable: {component.is_taxable ? "Yes" : "No"}</div>
                                    </td>
                                    <td className="px-6 py-4">
                                        <span className="ui-badge bg-indigo-50 text-[var(--primary-color)]">{component.items_count} item</span>
                                    </td>
                                    <td className="px-6 py-4">
                                        <div className="flex justify-end gap-2">
                                            <button type="button" onClick={() => openEdit(component)} className="ui-button-secondary px-3 py-2">Edit</button>
                                            <button type="button" onClick={() => destroy(route("admin.payroll-components.destroy", component.id))} className="rounded-md border border-red-200 px-3 py-2 text-sm font-semibold text-red-600 transition hover:bg-red-50">Hapus</button>
                                        </div>
                                    </td>
                                </tr>
                            ))}
                        </tbody>
                    </table>
                </div>
            </section>

            {showModal && (
                <div className="fixed inset-0 z-[70] flex items-center justify-center bg-slate-950/50 p-4 backdrop-blur-sm">
                    <div className="w-full max-w-2xl rounded-xl bg-white shadow-[var(--shadow-pop)]">
                        <div className="flex items-center justify-between border-b border-[var(--border-line)] px-6 py-4">
                            <h2 className="font-heading text-xl font-semibold">{editingComponent ? "Edit Komponen" : "Tambah Komponen"}</h2>
                            <button type="button" onClick={closeModal} className="text-sm text-[var(--text-muted)]">Tutup</button>
                        </div>
                        <form onSubmit={submit} className="grid gap-5 p-6 md:grid-cols-2">
                            <input value={data.name} onChange={(e) => setData("name", e.target.value)} className="ui-input" placeholder="Nama komponen" />
                            <input value={data.code} onChange={(e) => setData("code", e.target.value)} className="ui-input" placeholder="Kode" />
                            <select value={data.type} onChange={(e) => setData("type", e.target.value)} className="ui-input">
                                <option value="earning">Earning</option>
                                <option value="deduction">Deduction</option>
                            </select>
                            <select value={data.calculation_method} onChange={(e) => setData("calculation_method", e.target.value)} className="ui-input">
                                <option value="manual">Manual</option>
                                <option value="auto">Auto</option>
                            </select>
                            <label className="flex items-center gap-2 rounded-lg border border-[var(--border-line)] px-4 py-3"><input type="checkbox" checked={data.is_taxable} onChange={(e) => setData("is_taxable", e.target.checked)} /> Taxable</label>
                            <label className="flex items-center gap-2 rounded-lg border border-[var(--border-line)] px-4 py-3"><input type="checkbox" checked={data.is_active} onChange={(e) => setData("is_active", e.target.checked)} /> Aktif</label>
                            <div className="md:col-span-2 flex gap-3">
                                <button type="submit" disabled={processing} className="ui-button-primary">{processing ? "Menyimpan..." : "Simpan"}</button>
                                <button type="button" onClick={closeModal} className="ui-button-secondary">Batal</button>
                            </div>
                        </form>
                    </div>
                </div>
            )}
        </AuthenticatedLayout>
    );
}
