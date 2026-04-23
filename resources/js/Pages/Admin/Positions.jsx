import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout";
import { Head, useForm } from "@inertiajs/react";
import { useState } from "react";

export default function Positions({ positions, flash }) {
    const [showModal, setShowModal] = useState(false);
    const [editingPosition, setEditingPosition] = useState(null);
    const { data, setData, post, put, delete: destroy, processing, errors, reset } = useForm({
        name: "",
        code: "",
        grade: "",
        salary: 0,
        allowance: 0,
        overtime_rate: 0,
        is_active: true,
    });

    const openCreate = () => {
        setEditingPosition(null);
        reset();
        setData("salary", 0);
        setData("allowance", 0);
        setData("overtime_rate", 0);
        setData("is_active", true);
        setShowModal(true);
    };

    const openEdit = (position) => {
        setEditingPosition(position);
        setData({
            name: position.name || "",
            code: position.code || "",
            grade: position.grade || "",
            salary: position.salary || 0,
            allowance: position.allowance || 0,
            overtime_rate: position.overtime_rate || 0,
            is_active: position.is_active,
        });
        setShowModal(true);
    };

    const closeModal = () => {
        setShowModal(false);
        setEditingPosition(null);
        reset();
    };

    const submit = (event) => {
        event.preventDefault();
        if (editingPosition) {
            put(route("admin.positions.update", editingPosition.id), { onSuccess: closeModal });
            return;
        }
        post(route("admin.positions.store"), { onSuccess: closeModal });
    };

    const handleDelete = (id) => {
        if (confirm("Hapus jabatan ini?")) {
            destroy(route("admin.positions.destroy", id));
        }
    };

    return (
        <AuthenticatedLayout>
            <Head title="Master Jabatan" />

            <section className="ui-card overflow-hidden">
                <div className="flex flex-col gap-5 p-6 lg:flex-row lg:items-end lg:justify-between lg:p-8">
                    <div>
                        <div className="ui-section-title">Position Master</div>
                        <h1 className="font-heading mt-3 text-3xl font-bold text-black">Jabatan dan kompensasi dasar</h1>
                        <p className="mt-3 max-w-2xl text-sm leading-6 text-[var(--text-muted)]">Master jabatan sekarang mendukung grade, salary, allowance, dan overtime rate untuk kebutuhan payroll.</p>
                    </div>
                    <button type="button" onClick={openCreate} className="ui-button-primary">Tambah Jabatan</button>
                </div>
            </section>

            {flash?.success && <div className="mt-6 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-700">{flash.success}</div>}
            {flash?.error && <div className="mt-6 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm font-medium text-red-700">{flash.error}</div>}

            <section className="ui-card mt-6 overflow-hidden">
                <div className="overflow-x-auto">
                    <table className="min-w-full divide-y divide-[var(--border-line)] text-sm">
                        <thead className="bg-[var(--bg-subtle)] text-left text-[11px] font-semibold uppercase tracking-[0.14em] text-[var(--text-soft)]">
                            <tr>
                                <th className="px-6 py-4">Jabatan</th>
                                <th className="px-6 py-4">Kompensasi</th>
                                <th className="px-6 py-4">Pemakaian</th>
                                <th className="px-6 py-4 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody className="divide-y divide-[var(--border-line)]">
                            {positions.map((position) => (
                                <tr key={position.id}>
                                    <td className="px-6 py-4">
                                        <div className="font-semibold text-[var(--text-main)]">{position.name}</div>
                                        <div className="mt-1 text-xs text-[var(--text-muted)]">{position.code || "-"} • Grade {position.grade || "-"}</div>
                                    </td>
                                    <td className="px-6 py-4 text-[var(--text-muted)]">
                                        <div>Salary: Rp {Number(position.salary || 0).toLocaleString("id-ID")}</div>
                                        <div className="mt-1">Allowance: Rp {Number(position.allowance || 0).toLocaleString("id-ID")}</div>
                                        <div className="mt-1">Overtime: Rp {Number(position.overtime_rate || 0).toLocaleString("id-ID")}</div>
                                    </td>
                                    <td className="px-6 py-4">
                                        <div className="flex flex-wrap gap-2">
                                            <span className="ui-badge bg-indigo-50 text-[var(--primary-color)]">{position.users_count} employee</span>
                                            <span className={`ui-badge ${position.is_active ? "bg-emerald-50 text-[var(--success-color)]" : "bg-slate-100 text-slate-500"}`}>{position.is_active ? "Aktif" : "Nonaktif"}</span>
                                        </div>
                                    </td>
                                    <td className="px-6 py-4">
                                        <div className="flex justify-end gap-2">
                                            <button type="button" onClick={() => openEdit(position)} className="ui-button-secondary px-3 py-2">Edit</button>
                                            <button type="button" onClick={() => handleDelete(position.id)} className="rounded-md border border-red-200 px-3 py-2 text-sm font-semibold text-red-600 transition hover:bg-red-50">Hapus</button>
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
                    <div className="w-full max-w-3xl rounded-xl bg-white shadow-[var(--shadow-pop)]">
                        <div className="flex items-center justify-between border-b border-[var(--border-line)] px-6 py-4">
                            <h2 className="font-heading text-xl font-semibold">{editingPosition ? "Edit Jabatan" : "Tambah Jabatan"}</h2>
                            <button type="button" onClick={closeModal} className="text-sm text-[var(--text-muted)]">Tutup</button>
                        </div>
                        <form onSubmit={submit} className="grid gap-5 p-6 md:grid-cols-2">
                            <div>
                                <label className="mb-2 block text-sm font-semibold">Nama</label>
                                <input value={data.name} onChange={(event) => setData("name", event.target.value)} className="ui-input" />
                            </div>
                            <div>
                                <label className="mb-2 block text-sm font-semibold">Kode</label>
                                <input value={data.code} onChange={(event) => setData("code", event.target.value)} className="ui-input" />
                            </div>
                            <div>
                                <label className="mb-2 block text-sm font-semibold">Grade</label>
                                <input value={data.grade} onChange={(event) => setData("grade", event.target.value)} className="ui-input" />
                            </div>
                            <label className="flex items-center gap-3 rounded-lg border border-[var(--border-line)] bg-[var(--bg-subtle)] px-4 py-3 text-sm font-medium">
                                <input type="checkbox" checked={data.is_active} onChange={(event) => setData("is_active", event.target.checked)} />
                                Jabatan aktif
                            </label>
                            <div>
                                <label className="mb-2 block text-sm font-semibold">Salary</label>
                                <input type="number" value={data.salary} onChange={(event) => setData("salary", event.target.value)} className="ui-input" />
                            </div>
                            <div>
                                <label className="mb-2 block text-sm font-semibold">Allowance</label>
                                <input type="number" value={data.allowance} onChange={(event) => setData("allowance", event.target.value)} className="ui-input" />
                            </div>
                            <div>
                                <label className="mb-2 block text-sm font-semibold">Overtime rate</label>
                                <input type="number" value={data.overtime_rate} onChange={(event) => setData("overtime_rate", event.target.value)} className="ui-input" />
                            </div>
                            <div className="md:col-span-2 flex gap-3 pt-2">
                                <button type="submit" disabled={processing} className="ui-button-primary">{processing ? "Menyimpan..." : editingPosition ? "Update Jabatan" : "Simpan Jabatan"}</button>
                                <button type="button" onClick={closeModal} className="ui-button-secondary">Batal</button>
                            </div>
                        </form>
                    </div>
                </div>
            )}
        </AuthenticatedLayout>
    );
}
