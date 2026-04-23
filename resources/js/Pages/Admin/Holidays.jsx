import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout";
import { Head, useForm } from "@inertiajs/react";
import { useState } from "react";

export default function Holidays({ holidays, branches, flash }) {
    const [showModal, setShowModal] = useState(false);
    const [editingHoliday, setEditingHoliday] = useState(null);
    const { data, setData, post, put, delete: destroy, processing, reset } = useForm({
        name: "",
        holiday_date: "",
        type: "national",
        branch_id: "",
        is_recurring: false,
        notes: "",
    });

    const openCreate = () => {
        setEditingHoliday(null);
        reset();
        setData("type", "national");
        setData("is_recurring", false);
        setShowModal(true);
    };

    const openEdit = (holiday) => {
        setEditingHoliday(holiday);
        setData({
            name: holiday.name || "",
            holiday_date: holiday.holiday_date || "",
            type: holiday.type || "national",
            branch_id: holiday.branch_id || "",
            is_recurring: holiday.is_recurring ?? false,
            notes: holiday.notes || "",
        });
        setShowModal(true);
    };

    const closeModal = () => {
        setShowModal(false);
        setEditingHoliday(null);
        reset();
    };

    const submit = (event) => {
        event.preventDefault();

        if (editingHoliday) {
            put(route("admin.holidays.update", editingHoliday.id), { onSuccess: closeModal });
            return;
        }

        post(route("admin.holidays.store"), { onSuccess: closeModal });
    };

    const handleDelete = (id) => {
        if (confirm("Hapus hari libur ini?")) {
            destroy(route("admin.holidays.destroy", id));
        }
    };

    return (
        <AuthenticatedLayout>
            <Head title="Hari Libur" />

            <div className="space-y-6">
                <section className="ui-card overflow-hidden">
                    <div className="flex flex-col gap-5 p-6 lg:flex-row lg:items-end lg:justify-between lg:p-8">
                        <div>
                            <div className="ui-section-title">Holiday Calendar</div>
                            <h1 className="font-heading mt-3 text-3xl font-bold text-black">
                                Master hari libur dan kalender kerja
                            </h1>
                            <p className="mt-3 max-w-2xl text-sm leading-6 text-[var(--text-muted)]">
                                Kalender ini dipakai untuk pengajuan cuti, analitik kehadiran, dan
                                pengaturan hari kerja lintas cabang.
                            </p>
                        </div>
                        <button type="button" onClick={openCreate} className="ui-button-primary">
                            Tambah Hari Libur
                        </button>
                    </div>
                </section>

                {flash?.success && (
                    <div className="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-700">
                        {flash.success}
                    </div>
                )}

                <section className="ui-card overflow-hidden">
                    <div className="overflow-x-auto">
                        <table className="min-w-full divide-y divide-[var(--border-line)] text-sm">
                            <thead className="bg-[var(--bg-subtle)] text-left text-[11px] font-semibold uppercase tracking-[0.14em] text-[var(--text-soft)]">
                                <tr>
                                    <th className="px-6 py-4">Hari Libur</th>
                                    <th className="px-6 py-4">Tanggal</th>
                                    <th className="px-6 py-4">Tipe</th>
                                    <th className="px-6 py-4">Cabang</th>
                                    <th className="px-6 py-4">Pola</th>
                                    <th className="px-6 py-4 text-right">Aksi</th>
                                </tr>
                            </thead>
                            <tbody className="divide-y divide-[var(--border-line)]">
                                {holidays.map((holiday) => (
                                    <tr key={holiday.id}>
                                        <td className="px-6 py-4">
                                            <div className="font-semibold text-[var(--text-main)]">{holiday.name}</div>
                                            <div className="mt-1 text-xs text-[var(--text-muted)]">
                                                {holiday.notes || "Tanpa catatan tambahan"}
                                            </div>
                                        </td>
                                        <td className="px-6 py-4 font-data text-[var(--text-main)]">
                                            {holiday.holiday_date}
                                        </td>
                                        <td className="px-6 py-4">
                                            <span className="ui-badge bg-indigo-50 text-[var(--primary-color)]">
                                                {holiday.type.replaceAll("_", " ")}
                                            </span>
                                        </td>
                                        <td className="px-6 py-4 text-[var(--text-muted)]">
                                            {holiday.branch?.name || "Semua cabang"}
                                        </td>
                                        <td className="px-6 py-4 text-[var(--text-muted)]">
                                            {holiday.is_recurring ? "Berulang tahunan" : "Sekali"}
                                        </td>
                                        <td className="px-6 py-4">
                                            <div className="flex justify-end gap-2">
                                                <button type="button" onClick={() => openEdit(holiday)} className="ui-button-secondary px-3 py-2">
                                                    Edit
                                                </button>
                                                <button
                                                    type="button"
                                                    onClick={() => handleDelete(holiday.id)}
                                                    className="rounded-md border border-red-200 px-3 py-2 text-sm font-semibold text-red-600 transition hover:bg-red-50"
                                                >
                                                    Hapus
                                                </button>
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
                                <h2 className="font-heading text-xl font-semibold">
                                    {editingHoliday ? "Edit Hari Libur" : "Tambah Hari Libur"}
                                </h2>
                                <button type="button" onClick={closeModal} className="text-sm text-[var(--text-muted)]">
                                    Tutup
                                </button>
                            </div>
                            <form onSubmit={submit} className="grid gap-5 p-6 md:grid-cols-2">
                                <div className="md:col-span-2">
                                    <label className="mb-2 block text-sm font-semibold">Nama hari libur</label>
                                    <input value={data.name} onChange={(event) => setData("name", event.target.value)} className="ui-input" />
                                </div>
                                <div>
                                    <label className="mb-2 block text-sm font-semibold">Tanggal</label>
                                    <input type="date" value={data.holiday_date} onChange={(event) => setData("holiday_date", event.target.value)} className="ui-input" />
                                </div>
                                <div>
                                    <label className="mb-2 block text-sm font-semibold">Tipe</label>
                                    <select value={data.type} onChange={(event) => setData("type", event.target.value)} className="ui-input">
                                        <option value="national">National</option>
                                        <option value="company">Company</option>
                                        <option value="branch">Branch</option>
                                        <option value="collective_leave">Collective Leave</option>
                                    </select>
                                </div>
                                <div>
                                    <label className="mb-2 block text-sm font-semibold">Cabang</label>
                                    <select value={data.branch_id} onChange={(event) => setData("branch_id", event.target.value)} className="ui-input">
                                        <option value="">Semua cabang</option>
                                        {branches.map((branch) => (
                                            <option key={branch.id} value={branch.id}>
                                                {branch.name}
                                            </option>
                                        ))}
                                    </select>
                                </div>
                                <label className="flex items-center gap-3 rounded-lg border border-[var(--border-line)] bg-[var(--bg-subtle)] px-4 py-3 text-sm font-medium">
                                    <input type="checkbox" checked={data.is_recurring} onChange={(event) => setData("is_recurring", event.target.checked)} />
                                    Ulang setiap tahun
                                </label>
                                <div className="md:col-span-2">
                                    <label className="mb-2 block text-sm font-semibold">Catatan</label>
                                    <textarea rows="3" value={data.notes} onChange={(event) => setData("notes", event.target.value)} className="ui-input" />
                                </div>
                                <div className="md:col-span-2 flex gap-3 pt-2">
                                    <button type="submit" disabled={processing} className="ui-button-primary">
                                        {processing ? "Menyimpan..." : editingHoliday ? "Update Hari Libur" : "Simpan Hari Libur"}
                                    </button>
                                    <button type="button" onClick={closeModal} className="ui-button-secondary">
                                        Batal
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                )}
            </div>
        </AuthenticatedLayout>
    );
}
