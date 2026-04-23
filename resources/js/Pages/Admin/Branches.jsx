import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout";
import { Head, useForm } from "@inertiajs/react";
import { useState } from "react";

export default function Branches({ branches, flash }) {
    const [showModal, setShowModal] = useState(false);
    const [editingBranch, setEditingBranch] = useState(null);
    const { data, setData, post, put, delete: destroy, processing, errors, reset } = useForm({
        name: "",
        code: "",
        phone_number: "",
        email: "",
        address: "",
        city: "",
        province: "",
        postal_code: "",
        is_head_office: false,
        is_active: true,
    });

    const openCreate = () => {
        setEditingBranch(null);
        reset();
        setData("is_active", true);
        setShowModal(true);
    };

    const openEdit = (branch) => {
        setEditingBranch(branch);
        setData({
            name: branch.name || "",
            code: branch.code || "",
            phone_number: branch.phone_number || "",
            email: branch.email || "",
            address: branch.address || "",
            city: branch.city || "",
            province: branch.province || "",
            postal_code: branch.postal_code || "",
            is_head_office: branch.is_head_office,
            is_active: branch.is_active,
        });
        setShowModal(true);
    };

    const closeModal = () => {
        setShowModal(false);
        setEditingBranch(null);
        reset();
    };

    const submit = (event) => {
        event.preventDefault();
        if (editingBranch) {
            put(route("admin.branches.update", editingBranch.id), { onSuccess: closeModal });
            return;
        }
        post(route("admin.branches.store"), { onSuccess: closeModal });
    };

    const handleDelete = (id) => {
        if (confirm("Hapus cabang ini?")) {
            destroy(route("admin.branches.destroy", id));
        }
    };

    return (
        <AuthenticatedLayout>
            <Head title="Master Cabang" />

            <section className="ui-card overflow-hidden">
                <div className="flex flex-col gap-5 p-6 lg:flex-row lg:items-end lg:justify-between lg:p-8">
                    <div>
                        <div className="ui-section-title">Branch Master</div>
                        <h1 className="font-heading mt-3 text-3xl font-bold text-black">Cabang dan kantor operasional</h1>
                        <p className="mt-3 max-w-2xl text-sm leading-6 text-[var(--text-muted)]">Master cabang dipakai oleh departemen, lokasi absensi, shift kerja, dan employee assignment.</p>
                    </div>
                    <button type="button" onClick={openCreate} className="ui-button-primary">Tambah Cabang</button>
                </div>
            </section>

            {flash?.success && <div className="mt-6 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-700">{flash.success}</div>}
            {flash?.error && <div className="mt-6 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm font-medium text-red-700">{flash.error}</div>}

            <section className="ui-card mt-6 overflow-hidden">
                <div className="overflow-x-auto">
                    <table className="min-w-full divide-y divide-[var(--border-line)] text-sm">
                        <thead className="bg-[var(--bg-subtle)] text-left text-[11px] font-semibold uppercase tracking-[0.14em] text-[var(--text-soft)]">
                            <tr>
                                <th className="px-6 py-4">Cabang</th>
                                <th className="px-6 py-4">Kontak</th>
                                <th className="px-6 py-4">Pemakaian</th>
                                <th className="px-6 py-4">Status</th>
                                <th className="px-6 py-4 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody className="divide-y divide-[var(--border-line)]">
                            {branches.map((branch) => (
                                <tr key={branch.id}>
                                    <td className="px-6 py-4">
                                        <div className="font-semibold text-[var(--text-main)]">{branch.name}</div>
                                        <div className="mt-1 text-xs text-[var(--text-muted)]">{branch.code} • {branch.city || "-"}, {branch.province || "-"}</div>
                                    </td>
                                    <td className="px-6 py-4 text-[var(--text-muted)]">
                                        <div>{branch.phone_number || "-"}</div>
                                        <div className="mt-1">{branch.email || "-"}</div>
                                    </td>
                                    <td className="px-6 py-4 text-[var(--text-muted)]">
                                        <div>User: {branch.users_count}</div>
                                        <div className="mt-1">Departemen: {branch.departments_count}</div>
                                        <div className="mt-1">Lokasi: {branch.locations_count}</div>
                                    </td>
                                    <td className="px-6 py-4">
                                        <div className="flex flex-wrap gap-2">
                                            <span className={`ui-badge ${branch.is_active ? "bg-emerald-50 text-[var(--success-color)]" : "bg-slate-100 text-slate-500"}`}>{branch.is_active ? "Aktif" : "Nonaktif"}</span>
                                            {branch.is_head_office && <span className="ui-badge bg-indigo-50 text-[var(--primary-color)]">Head Office</span>}
                                        </div>
                                    </td>
                                    <td className="px-6 py-4">
                                        <div className="flex justify-end gap-2">
                                            <button type="button" onClick={() => openEdit(branch)} className="ui-button-secondary px-3 py-2">Edit</button>
                                            <button type="button" onClick={() => handleDelete(branch.id)} className="rounded-md border border-red-200 px-3 py-2 text-sm font-semibold text-red-600 transition hover:bg-red-50">Hapus</button>
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
                            <h2 className="font-heading text-xl font-semibold">{editingBranch ? "Edit Cabang" : "Tambah Cabang"}</h2>
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
                            </div>
                            <div>
                                <label className="mb-2 block text-sm font-semibold">Telepon</label>
                                <input value={data.phone_number} onChange={(event) => setData("phone_number", event.target.value)} className="ui-input" />
                            </div>
                            <div>
                                <label className="mb-2 block text-sm font-semibold">Email</label>
                                <input value={data.email} onChange={(event) => setData("email", event.target.value)} className="ui-input" />
                            </div>
                            <div>
                                <label className="mb-2 block text-sm font-semibold">Kota</label>
                                <input value={data.city} onChange={(event) => setData("city", event.target.value)} className="ui-input" />
                            </div>
                            <div>
                                <label className="mb-2 block text-sm font-semibold">Provinsi</label>
                                <input value={data.province} onChange={(event) => setData("province", event.target.value)} className="ui-input" />
                            </div>
                            <div>
                                <label className="mb-2 block text-sm font-semibold">Kode pos</label>
                                <input value={data.postal_code} onChange={(event) => setData("postal_code", event.target.value)} className="ui-input" />
                            </div>
                            <label className="flex items-center gap-3 rounded-lg border border-[var(--border-line)] bg-[var(--bg-subtle)] px-4 py-3 text-sm font-medium">
                                <input type="checkbox" checked={data.is_head_office} onChange={(event) => setData("is_head_office", event.target.checked)} />
                                Jadikan head office
                            </label>
                            <div className="md:col-span-2">
                                <label className="mb-2 block text-sm font-semibold">Alamat</label>
                                <textarea rows="3" value={data.address} onChange={(event) => setData("address", event.target.value)} className="ui-input" />
                            </div>
                            <label className="md:col-span-2 flex items-center gap-3 rounded-lg border border-[var(--border-line)] bg-[var(--bg-subtle)] px-4 py-3 text-sm font-medium">
                                <input type="checkbox" checked={data.is_active} onChange={(event) => setData("is_active", event.target.checked)} />
                                Cabang aktif
                            </label>
                            <div className="md:col-span-2 flex gap-3 pt-2">
                                <button type="submit" disabled={processing} className="ui-button-primary">{processing ? "Menyimpan..." : editingBranch ? "Update Cabang" : "Simpan Cabang"}</button>
                                <button type="button" onClick={closeModal} className="ui-button-secondary">Batal</button>
                            </div>
                        </form>
                    </div>
                </div>
            )}
        </AuthenticatedLayout>
    );
}
