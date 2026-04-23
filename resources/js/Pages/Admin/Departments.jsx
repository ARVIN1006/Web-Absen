import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout";
import { Head, useForm } from "@inertiajs/react";
import { useState } from "react";

export default function Departments({ departments, users, branches, parents, flash }) {
    const [showModal, setShowModal] = useState(false);
    const [editingDepartment, setEditingDepartment] = useState(null);
    const { data, setData, post, put, delete: destroy, processing, errors, reset } = useForm({
        name: "",
        code: "",
        description: "",
        head_id: "",
        parent_id: "",
        branch_id: "",
        is_active: true,
    });

    const openCreate = () => {
        setEditingDepartment(null);
        reset();
        setData("is_active", true);
        setShowModal(true);
    };

    const openEdit = (department) => {
        setEditingDepartment(department);
        setData({
            name: department.name || "",
            code: department.code || "",
            description: department.description || "",
            head_id: department.head_id || "",
            parent_id: department.parent_id || "",
            branch_id: department.branch_id || "",
            is_active: department.is_active,
        });
        setShowModal(true);
    };

    const closeModal = () => {
        setShowModal(false);
        setEditingDepartment(null);
        reset();
    };

    const submit = (event) => {
        event.preventDefault();

        if (editingDepartment) {
            put(route("admin.departments.update", editingDepartment.id), { onSuccess: closeModal });
            return;
        }

        post(route("admin.departments.store"), { onSuccess: closeModal });
    };

    const handleDelete = (id) => {
        if (confirm("Hapus departemen ini?")) {
            destroy(route("admin.departments.destroy", id));
        }
    };

    return (
        <AuthenticatedLayout>
            <Head title="Master Departemen" />

            <section className="ui-card overflow-hidden">
                <div className="flex flex-col gap-5 p-6 lg:flex-row lg:items-end lg:justify-between lg:p-8">
                    <div>
                        <div className="ui-section-title">Department Master</div>
                        <h1 className="font-heading mt-3 text-3xl font-bold text-black">Struktur departemen perusahaan</h1>
                        <p className="mt-3 max-w-2xl text-sm leading-6 text-[var(--text-muted)]">Sekarang departemen mendukung branch dan parent department agar struktur organisasi lebih lengkap.</p>
                    </div>
                    <button type="button" onClick={openCreate} className="ui-button-primary">Tambah Departemen</button>
                </div>
            </section>

            {flash?.success && (
                <div className="mt-6 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-700">
                    {flash.success}
                </div>
            )}

            <section className="ui-card mt-6 overflow-hidden">
                <div className="overflow-x-auto">
                    <table className="min-w-full divide-y divide-[var(--border-line)] text-sm">
                        <thead className="bg-[var(--bg-subtle)] text-left text-[11px] font-semibold uppercase tracking-[0.14em] text-[var(--text-soft)]">
                            <tr>
                                <th className="px-6 py-4">Kode</th>
                                <th className="px-6 py-4">Departemen</th>
                                <th className="px-6 py-4">Struktur</th>
                                <th className="px-6 py-4">Status</th>
                                <th className="px-6 py-4 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody className="divide-y divide-[var(--border-line)]">
                            {departments.map((department) => (
                                <tr key={department.id}>
                                    <td className="px-6 py-4 font-data">{department.code}</td>
                                    <td className="px-6 py-4">
                                        <div className="font-semibold text-[var(--text-main)]">{department.name}</div>
                                        <div className="mt-1 text-xs text-[var(--text-muted)]">{department.description || "Tanpa deskripsi"}</div>
                                    </td>
                                    <td className="px-6 py-4 text-[var(--text-muted)]">
                                        <div>Cabang: {department.branch?.name || "-"}</div>
                                        <div className="mt-1">Parent: {department.parent?.name || "-"}</div>
                                        <div className="mt-1">Head: {department.head?.name || "-"}</div>
                                        <div className="mt-1">Staff: {department.users_count}</div>
                                    </td>
                                    <td className="px-6 py-4">
                                        <span className={`ui-badge ${department.is_active ? "bg-emerald-50 text-[var(--success-color)]" : "bg-slate-100 text-slate-500"}`}>
                                            {department.is_active ? "Aktif" : "Nonaktif"}
                                        </span>
                                    </td>
                                    <td className="px-6 py-4">
                                        <div className="flex justify-end gap-2">
                                            <button type="button" onClick={() => openEdit(department)} className="ui-button-secondary px-3 py-2">Edit</button>
                                            <button type="button" onClick={() => handleDelete(department.id)} className="rounded-md border border-red-200 px-3 py-2 text-sm font-semibold text-red-600 transition hover:bg-red-50">Hapus</button>
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
                            <h2 className="font-heading text-xl font-semibold">{editingDepartment ? "Edit Departemen" : "Tambah Departemen"}</h2>
                            <button type="button" onClick={closeModal} className="text-sm text-[var(--text-muted)]">Tutup</button>
                        </div>
                        <form onSubmit={submit} className="grid gap-5 p-6 md:grid-cols-2">
                            <div>
                                <label className="mb-2 block text-sm font-semibold">Nama</label>
                                <input value={data.name} onChange={(event) => setData("name", event.target.value)} className="ui-input" />
                                {errors.name && <p className="mt-1 text-sm text-red-600">{errors.name}</p>}
                            </div>
                            <div>
                                <label className="mb-2 block text-sm font-semibold">Kode</label>
                                <input value={data.code} onChange={(event) => setData("code", event.target.value)} className="ui-input" />
                                {errors.code && <p className="mt-1 text-sm text-red-600">{errors.code}</p>}
                            </div>
                            <div>
                                <label className="mb-2 block text-sm font-semibold">Cabang</label>
                                <select value={data.branch_id} onChange={(event) => setData("branch_id", event.target.value)} className="ui-input">
                                    <option value="">Pilih cabang</option>
                                    {branches.map((branch) => <option key={branch.id} value={branch.id}>{branch.name}</option>)}
                                </select>
                            </div>
                            <div>
                                <label className="mb-2 block text-sm font-semibold">Parent department</label>
                                <select value={data.parent_id} onChange={(event) => setData("parent_id", event.target.value)} className="ui-input">
                                    <option value="">Tidak ada</option>
                                    {parents.filter((item) => item.id !== editingDepartment?.id).map((parent) => <option key={parent.id} value={parent.id}>{parent.name}</option>)}
                                </select>
                            </div>
                            <div>
                                <label className="mb-2 block text-sm font-semibold">Head department</label>
                                <select value={data.head_id} onChange={(event) => setData("head_id", event.target.value)} className="ui-input">
                                    <option value="">Pilih head</option>
                                    {users.map((user) => <option key={user.id} value={user.id}>{user.name}</option>)}
                                </select>
                            </div>
                            <label className="flex items-center gap-3 rounded-lg border border-[var(--border-line)] bg-[var(--bg-subtle)] px-4 py-3 text-sm font-medium">
                                <input type="checkbox" checked={data.is_active} onChange={(event) => setData("is_active", event.target.checked)} />
                                Departemen aktif
                            </label>
                            <div className="md:col-span-2">
                                <label className="mb-2 block text-sm font-semibold">Deskripsi</label>
                                <textarea rows="3" value={data.description} onChange={(event) => setData("description", event.target.value)} className="ui-input" />
                            </div>
                            <div className="md:col-span-2 flex gap-3 pt-2">
                                <button type="submit" disabled={processing} className="ui-button-primary">
                                    {processing ? "Menyimpan..." : editingDepartment ? "Update Departemen" : "Simpan Departemen"}
                                </button>
                                <button type="button" onClick={closeModal} className="ui-button-secondary">Batal</button>
                            </div>
                        </form>
                    </div>
                </div>
            )}
        </AuthenticatedLayout>
    );
}
