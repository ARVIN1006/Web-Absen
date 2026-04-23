import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout";
import { Head, useForm } from "@inertiajs/react";
import { useState } from "react";

export default function EmploymentTypes({ employmentTypes, flash }) {
    const [showModal, setShowModal] = useState(false);
    const [editingType, setEditingType] = useState(null);
    const { data, setData, post, put, delete: destroy, processing, errors, reset } = useForm({
        name: "",
        code: "",
        category: "permanent",
        description: "",
        is_active: true,
    });

    const openCreate = () => {
        setEditingType(null);
        reset();
        setData("category", "permanent");
        setData("is_active", true);
        setShowModal(true);
    };

    const openEdit = (type) => {
        setEditingType(type);
        setData({
            name: type.name || "",
            code: type.code || "",
            category: type.category || "permanent",
            description: type.description || "",
            is_active: type.is_active,
        });
        setShowModal(true);
    };

    const closeModal = () => {
        setShowModal(false);
        setEditingType(null);
        reset();
    };

    const submit = (event) => {
        event.preventDefault();
        if (editingType) {
            put(route("admin.employment-types.update", editingType.id), { onSuccess: closeModal });
            return;
        }
        post(route("admin.employment-types.store"), { onSuccess: closeModal });
    };

    const handleDelete = (id) => {
        if (confirm("Hapus employment type ini?")) {
            destroy(route("admin.employment-types.destroy", id));
        }
    };

    return (
        <AuthenticatedLayout>
            <Head title="Employment Types" />

            <section className="ui-card overflow-hidden">
                <div className="flex flex-col gap-5 p-6 lg:flex-row lg:items-end lg:justify-between lg:p-8">
                    <div>
                        <div className="ui-section-title">Employment Type Master</div>
                        <h1 className="font-heading mt-3 text-3xl font-bold text-black">Kategori hubungan kerja</h1>
                        <p className="mt-3 max-w-2xl text-sm leading-6 text-[var(--text-muted)]">Dipakai untuk membedakan karyawan tetap, kontrak, magang, dan model hubungan kerja lain.</p>
                    </div>
                    <button type="button" onClick={openCreate} className="ui-button-primary">Tambah Employment Type</button>
                </div>
            </section>

            {flash?.success && <div className="mt-6 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-700">{flash.success}</div>}
            {flash?.error && <div className="mt-6 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm font-medium text-red-700">{flash.error}</div>}

            <section className="ui-card mt-6 overflow-hidden">
                <div className="overflow-x-auto">
                    <table className="min-w-full divide-y divide-[var(--border-line)] text-sm">
                        <thead className="bg-[var(--bg-subtle)] text-left text-[11px] font-semibold uppercase tracking-[0.14em] text-[var(--text-soft)]">
                            <tr>
                                <th className="px-6 py-4">Type</th>
                                <th className="px-6 py-4">Kategori</th>
                                <th className="px-6 py-4">Pemakaian</th>
                                <th className="px-6 py-4">Status</th>
                                <th className="px-6 py-4 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody className="divide-y divide-[var(--border-line)]">
                            {employmentTypes.map((type) => (
                                <tr key={type.id}>
                                    <td className="px-6 py-4">
                                        <div className="font-semibold text-[var(--text-main)]">{type.name}</div>
                                        <div className="mt-1 text-xs text-[var(--text-muted)]">{type.code}</div>
                                    </td>
                                    <td className="px-6 py-4 text-[var(--text-muted)]">
                                        <div>{type.category}</div>
                                        <div className="mt-1 text-xs">{type.description || "Tanpa deskripsi"}</div>
                                    </td>
                                    <td className="px-6 py-4">
                                        <span className="ui-badge bg-indigo-50 text-[var(--primary-color)]">{type.users_count} employee</span>
                                    </td>
                                    <td className="px-6 py-4">
                                        <span className={`ui-badge ${type.is_active ? "bg-emerald-50 text-[var(--success-color)]" : "bg-slate-100 text-slate-500"}`}>{type.is_active ? "Aktif" : "Nonaktif"}</span>
                                    </td>
                                    <td className="px-6 py-4">
                                        <div className="flex justify-end gap-2">
                                            <button type="button" onClick={() => openEdit(type)} className="ui-button-secondary px-3 py-2">Edit</button>
                                            <button type="button" onClick={() => handleDelete(type.id)} className="rounded-md border border-red-200 px-3 py-2 text-sm font-semibold text-red-600 transition hover:bg-red-50">Hapus</button>
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
                            <h2 className="font-heading text-xl font-semibold">{editingType ? "Edit Employment Type" : "Tambah Employment Type"}</h2>
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
                                <label className="mb-2 block text-sm font-semibold">Kategori</label>
                                <select value={data.category} onChange={(event) => setData("category", event.target.value)} className="ui-input">
                                    <option value="permanent">Permanent</option>
                                    <option value="contract">Contract</option>
                                    <option value="intern">Intern</option>
                                    <option value="freelance">Freelance</option>
                                </select>
                            </div>
                            <label className="flex items-center gap-3 rounded-lg border border-[var(--border-line)] bg-[var(--bg-subtle)] px-4 py-3 text-sm font-medium">
                                <input type="checkbox" checked={data.is_active} onChange={(event) => setData("is_active", event.target.checked)} />
                                Aktif
                            </label>
                            <div className="md:col-span-2">
                                <label className="mb-2 block text-sm font-semibold">Deskripsi</label>
                                <textarea rows="3" value={data.description} onChange={(event) => setData("description", event.target.value)} className="ui-input" />
                            </div>
                            <div className="md:col-span-2 flex gap-3 pt-2">
                                <button type="submit" disabled={processing} className="ui-button-primary">{processing ? "Menyimpan..." : editingType ? "Update Type" : "Simpan Type"}</button>
                                <button type="button" onClick={closeModal} className="ui-button-secondary">Batal</button>
                            </div>
                        </form>
                    </div>
                </div>
            )}
        </AuthenticatedLayout>
    );
}
