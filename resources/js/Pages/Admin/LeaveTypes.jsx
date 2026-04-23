import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout";
import { Head, useForm } from "@inertiajs/react";
import { useState } from "react";

export default function LeaveTypes({ leaveTypes, flash }) {
    const [showModal, setShowModal] = useState(false);
    const [editingLeaveType, setEditingLeaveType] = useState(null);
    const { data, setData, post, put, delete: destroy, processing, errors, reset } = useForm({
        name: "",
        code: "",
        max_days_per_year: "",
        is_paid: true,
        requires_attachment: false,
        requires_balance: true,
        description: "",
        is_active: true,
    });

    const openAddModal = () => {
        setEditingLeaveType(null);
        reset();
        setShowModal(true);
    };

    const openEditModal = (leaveType) => {
        setEditingLeaveType(leaveType);
        setData({
            name: leaveType.name,
            code: leaveType.code,
            max_days_per_year: leaveType.max_days_per_year,
            is_paid: leaveType.is_paid,
            requires_attachment: leaveType.requires_attachment,
            requires_balance: leaveType.requires_balance,
            description: leaveType.description || "",
            is_active: leaveType.is_active,
        });
        setShowModal(true);
    };

    const closeModal = () => {
        setShowModal(false);
        setEditingLeaveType(null);
        reset();
    };

    const handleSubmit = (event) => {
        event.preventDefault();

        if (editingLeaveType) {
            put(route("admin.leave-types.update", editingLeaveType.id), {
                onSuccess: closeModal,
            });

            return;
        }

        post(route("admin.leave-types.store"), {
            onSuccess: closeModal,
        });
    };

    const handleDelete = (id) => {
        if (confirm("Yakin ingin menghapus tipe cuti ini?")) {
            destroy(route("admin.leave-types.destroy", id));
        }
    };

    return (
        <AuthenticatedLayout>
            <Head title="Kelola Tipe Cuti" />

            <div className="space-y-6">
                <div className="flex flex-col gap-4 rounded-[28px] border border-slate-200 bg-white p-6 shadow-sm lg:flex-row lg:items-center lg:justify-between">
                    <div>
                        <p className="text-sm font-semibold uppercase tracking-[0.2em] text-[var(--text-dark-muted)]">
                            HR Policy
                        </p>
                        <h1 className="mt-2 font-heading text-3xl font-bold text-[var(--text-main)]">
                            Kelola Tipe Cuti
                        </h1>
                        <p className="mt-2 max-w-2xl text-sm text-slate-500">
                            Atur kebijakan cuti tahunan, cuti berbayar, kebutuhan saldo, dan
                            persyaratan lampiran untuk setiap jenis pengajuan.
                        </p>
                    </div>
                    <button
                        onClick={openAddModal}
                        className="inline-flex items-center justify-center rounded-2xl bg-[var(--primary-color)] px-5 py-3 text-sm font-semibold text-white transition hover:brightness-110"
                    >
                        + Tambah Tipe Cuti
                    </button>
                </div>

                {flash?.success && (
                    <div className="rounded-2xl border border-emerald-200 bg-emerald-50 px-5 py-3 text-sm font-medium text-emerald-700">
                        {flash.success}
                    </div>
                )}

                <div className="grid gap-6 md:grid-cols-2 xl:grid-cols-3">
                    {leaveTypes.length === 0 ? (
                        <div className="col-span-full rounded-[28px] border border-dashed border-slate-300 bg-white px-6 py-16 text-center text-slate-500">
                            Belum ada data tipe cuti.
                        </div>
                    ) : (
                        leaveTypes.map((leaveType) => (
                            <div
                                key={leaveType.id}
                                className="flex flex-col rounded-[28px] border border-slate-200 bg-white p-6 shadow-sm transition hover:-translate-y-1 hover:shadow-lg"
                            >
                                <div className="mb-4 flex items-start justify-between gap-4">
                                    <div>
                                        <div className="text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">
                                            {leaveType.code}
                                        </div>
                                        <h3 className="mt-2 text-lg font-bold text-[var(--text-main)]">
                                            {leaveType.name}
                                        </h3>
                                    </div>
                                    <span
                                        className={`rounded-full px-3 py-1 text-xs font-bold ${
                                            leaveType.is_active
                                                ? "bg-emerald-50 text-emerald-700"
                                                : "bg-slate-100 text-slate-500"
                                        }`}
                                    >
                                        {leaveType.is_active ? "Aktif" : "Nonaktif"}
                                    </span>
                                </div>

                                <div className="mb-4 grid gap-3 text-sm">
                                    <div className="flex justify-between gap-3">
                                        <span className="text-slate-500">Maks Hari/Tahun</span>
                                        <span className="font-semibold text-[var(--text-main)]">
                                            {leaveType.max_days_per_year} hari
                                        </span>
                                    </div>
                                    <div className="flex justify-between gap-3">
                                        <span className="text-slate-500">Tipe Bayaran</span>
                                        <span className="font-semibold text-[var(--text-main)]">
                                            {leaveType.is_paid ? "Paid Leave" : "Unpaid Leave"}
                                        </span>
                                    </div>
                                    <div className="flex justify-between gap-3">
                                        <span className="text-slate-500">Butuh Saldo</span>
                                        <span className="font-semibold text-[var(--text-main)]">
                                            {leaveType.requires_balance ? "Ya" : "Tidak"}
                                        </span>
                                    </div>
                                    <div className="flex justify-between gap-3">
                                        <span className="text-slate-500">Wajib Lampiran</span>
                                        <span className="font-semibold text-[var(--text-main)]">
                                            {leaveType.requires_attachment ? "Ya" : "Tidak"}
                                        </span>
                                    </div>
                                </div>

                                {leaveType.description && (
                                    <p className="mb-4 rounded-2xl bg-slate-50 px-4 py-3 text-sm text-slate-500">
                                        {leaveType.description}
                                    </p>
                                )}

                                <div className="mt-auto flex gap-2 border-t border-slate-100 pt-4">
                                    <button
                                        onClick={() => openEditModal(leaveType)}
                                        className="flex-1 rounded-xl bg-blue-50 px-3 py-2 text-sm font-semibold text-blue-700 transition hover:bg-blue-100"
                                    >
                                        Edit
                                    </button>
                                    <button
                                        onClick={() => handleDelete(leaveType.id)}
                                        className="flex-1 rounded-xl bg-red-50 px-3 py-2 text-sm font-semibold text-red-700 transition hover:bg-red-100"
                                    >
                                        Hapus
                                    </button>
                                </div>
                            </div>
                        ))
                    )}
                </div>
            </div>

            {showModal && (
                <div className="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/45 p-4 backdrop-blur-sm">
                    <div className="w-full max-w-2xl overflow-hidden rounded-[28px] border border-slate-200 bg-white shadow-2xl">
                        <div className="flex items-center justify-between border-b border-slate-100 px-6 py-5">
                            <h3 className="text-lg font-bold text-[var(--text-main)]">
                                {editingLeaveType ? "Edit Tipe Cuti" : "Tambah Tipe Cuti Baru"}
                            </h3>
                            <button
                                onClick={closeModal}
                                className="text-2xl text-slate-400 transition hover:text-slate-700"
                            >
                                &times;
                            </button>
                        </div>

                        <form onSubmit={handleSubmit} className="space-y-5 p-6">
                            <div className="grid gap-5 md:grid-cols-2">
                                <div>
                                    <label className="mb-2 block text-sm font-semibold text-slate-500">
                                        Nama Tipe Cuti <span className="text-red-500">*</span>
                                    </label>
                                    <input
                                        type="text"
                                        value={data.name}
                                        onChange={(event) => setData("name", event.target.value)}
                                        className="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 outline-none transition focus:border-[var(--primary-color)] focus:bg-white"
                                        required
                                    />
                                    {errors.name && <p className="mt-1 text-sm text-red-500">{errors.name}</p>}
                                </div>

                                <div>
                                    <label className="mb-2 block text-sm font-semibold text-slate-500">
                                        Kode Tipe Cuti <span className="text-red-500">*</span>
                                    </label>
                                    <input
                                        type="text"
                                        value={data.code}
                                        onChange={(event) => setData("code", event.target.value.toUpperCase())}
                                        className="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 uppercase outline-none transition focus:border-[var(--primary-color)] focus:bg-white"
                                        required
                                    />
                                    {errors.code && <p className="mt-1 text-sm text-red-500">{errors.code}</p>}
                                </div>

                                <div>
                                    <label className="mb-2 block text-sm font-semibold text-slate-500">
                                        Maks Hari per Tahun <span className="text-red-500">*</span>
                                    </label>
                                    <input
                                        type="number"
                                        min="0"
                                        value={data.max_days_per_year}
                                        onChange={(event) => setData("max_days_per_year", event.target.value)}
                                        className="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 outline-none transition focus:border-[var(--primary-color)] focus:bg-white"
                                        required
                                    />
                                    {errors.max_days_per_year && (
                                        <p className="mt-1 text-sm text-red-500">{errors.max_days_per_year}</p>
                                    )}
                                </div>

                                <div>
                                    <label className="mb-2 block text-sm font-semibold text-slate-500">
                                        Status
                                    </label>
                                    <select
                                        value={data.is_active ? "active" : "inactive"}
                                        onChange={(event) => setData("is_active", event.target.value === "active")}
                                        className="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 outline-none transition focus:border-[var(--primary-color)] focus:bg-white"
                                    >
                                        <option value="active">Aktif</option>
                                        <option value="inactive">Nonaktif</option>
                                    </select>
                                </div>
                            </div>

                            <div>
                                <label className="mb-2 block text-sm font-semibold text-slate-500">
                                    Deskripsi
                                </label>
                                <textarea
                                    value={data.description}
                                    onChange={(event) => setData("description", event.target.value)}
                                    rows="3"
                                    className="w-full resize-none rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 outline-none transition focus:border-[var(--primary-color)] focus:bg-white"
                                />
                            </div>

                            <div className="grid gap-3 md:grid-cols-3">
                                <label className="flex items-center gap-3 rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3">
                                    <input
                                        type="checkbox"
                                        checked={data.is_paid}
                                        onChange={(event) => setData("is_paid", event.target.checked)}
                                        className="h-4 w-4 rounded border-slate-300"
                                    />
                                    <span className="text-sm font-semibold text-slate-700">Cuti Berbayar</span>
                                </label>

                                <label className="flex items-center gap-3 rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3">
                                    <input
                                        type="checkbox"
                                        checked={data.requires_balance}
                                        onChange={(event) => setData("requires_balance", event.target.checked)}
                                        className="h-4 w-4 rounded border-slate-300"
                                    />
                                    <span className="text-sm font-semibold text-slate-700">Kurangi Saldo</span>
                                </label>

                                <label className="flex items-center gap-3 rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3">
                                    <input
                                        type="checkbox"
                                        checked={data.requires_attachment}
                                        onChange={(event) => setData("requires_attachment", event.target.checked)}
                                        className="h-4 w-4 rounded border-slate-300"
                                    />
                                    <span className="text-sm font-semibold text-slate-700">Wajib Lampiran</span>
                                </label>
                            </div>

                            <button
                                type="submit"
                                disabled={processing}
                                className="w-full rounded-2xl bg-[var(--primary-color)] py-3 text-sm font-bold text-white transition hover:brightness-110 disabled:opacity-50"
                            >
                                {processing
                                    ? "Menyimpan..."
                                    : editingLeaveType
                                      ? "Update Tipe Cuti"
                                      : "Simpan Tipe Cuti"}
                            </button>
                        </form>
                    </div>
                </div>
            )}
        </AuthenticatedLayout>
    );
}
