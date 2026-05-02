import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout";
import { Head, useForm } from "@inertiajs/react";
import { useState } from "react";

function numberValue(value) {
    return Number(value || 0).toLocaleString("id-ID", { maximumFractionDigits: 2 });
}

export default function LeaveBalances({ leaveBalances = [], employees = [], leaveTypes = [], currentYear, flash }) {
    const [showModal, setShowModal] = useState(false);
    const [editingBalance, setEditingBalance] = useState(null);
    const { data, setData, post, put, delete: destroy, processing, reset, errors } = useForm({
        user_id: "",
        leave_type_id: "",
        year: currentYear || new Date().getFullYear(),
        allocated_days: 0,
        used_days: 0,
        reserved_days: 0,
        remaining_days: 0,
    });

    const openCreate = () => {
        setEditingBalance(null);
        reset();
        setData({
            user_id: "",
            leave_type_id: "",
            year: currentYear || new Date().getFullYear(),
            allocated_days: 0,
            used_days: 0,
            reserved_days: 0,
            remaining_days: 0,
        });
        setShowModal(true);
    };

    const openEdit = (balance) => {
        setEditingBalance(balance);
        setData({
            user_id: balance.user_id,
            leave_type_id: balance.leave_type_id,
            year: balance.year,
            allocated_days: balance.allocated_days,
            used_days: balance.used_days,
            reserved_days: balance.reserved_days,
            remaining_days: balance.remaining_days,
        });
        setShowModal(true);
    };

    const closeModal = () => {
        setShowModal(false);
        setEditingBalance(null);
        reset();
    };

    const submit = (event) => {
        event.preventDefault();
        if (editingBalance) {
            put(route("admin.leave-balances.update", editingBalance.id), { onSuccess: closeModal });
            return;
        }

        post(route("admin.leave-balances.store"), { onSuccess: closeModal });
    };

    const recalculateRemaining = (field, value) => {
        const nextData = { ...data, [field]: value };
        const remaining = Number(nextData.allocated_days || 0) - Number(nextData.used_days || 0) - Number(nextData.reserved_days || 0);
        setData({
            ...nextData,
            remaining_days: Math.max(0, remaining),
        });
    };

    return (
        <AuthenticatedLayout>
            <Head title="Saldo Cuti" />

            <section className="ui-card overflow-hidden">
                <div className="flex flex-col gap-5 p-6 lg:flex-row lg:items-end lg:justify-between lg:p-8">
                    <div>
                        <div className="ui-section-title">Leave Balance</div>
                        <h1 className="font-heading mt-3 text-3xl font-bold text-black">Saldo cuti karyawan</h1>
                        <p className="mt-2 max-w-3xl text-sm leading-6 text-[var(--text-muted)]">
                            Kelola alokasi, pemakaian, dan sisa cuti yang dipakai saat karyawan mengajukan cuti.
                        </p>
                    </div>
                    <button type="button" onClick={openCreate} className="ui-button-primary">
                        Tambah Saldo
                    </button>
                </div>
            </section>

            {flash?.success && <div className="mt-6 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-700">{flash.success}</div>}
            {flash?.error && <div className="mt-6 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm font-medium text-red-700">{flash.error}</div>}

            <section className="ui-card mt-6 overflow-hidden">
                <div className="overflow-x-auto">
                    <table className="min-w-full divide-y divide-[var(--border-line)] text-sm">
                        <thead className="bg-[var(--bg-subtle)] text-left text-[11px] font-semibold uppercase tracking-[0.14em] text-[var(--text-soft)]">
                            <tr>
                                <th className="px-6 py-4">Karyawan</th>
                                <th className="px-6 py-4">Tipe Cuti</th>
                                <th className="px-6 py-4">Tahun</th>
                                <th className="px-6 py-4">Saldo</th>
                                <th className="px-6 py-4 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody className="divide-y divide-[var(--border-line)]">
                            {leaveBalances.length === 0 ? (
                                <tr>
                                    <td className="px-6 py-12 text-center text-[var(--text-muted)]" colSpan="5">
                                        Belum ada saldo cuti.
                                    </td>
                                </tr>
                            ) : (
                                leaveBalances.map((balance) => (
                                    <tr key={balance.id}>
                                        <td className="px-6 py-4">
                                            <div className="font-semibold text-[var(--text-main)]">{balance.user?.name || "-"}</div>
                                            <div className="mt-1 text-xs text-[var(--text-muted)]">{balance.user?.email || "-"}</div>
                                        </td>
                                        <td className="px-6 py-4">
                                            <div className="font-semibold text-[var(--text-main)]">{balance.leave_type?.name || "-"}</div>
                                            <div className="mt-1 text-xs text-[var(--text-muted)]">{balance.leave_type?.code || "-"}</div>
                                        </td>
                                        <td className="px-6 py-4 text-[var(--text-muted)]">{balance.year}</td>
                                        <td className="px-6 py-4 text-[var(--text-muted)]">
                                            <div>Alokasi: {numberValue(balance.allocated_days)}</div>
                                            <div className="mt-1">Terpakai: {numberValue(balance.used_days)}</div>
                                            <div className="mt-1">Reserved: {numberValue(balance.reserved_days)}</div>
                                            <div className="mt-1 font-semibold text-[var(--text-main)]">Sisa: {numberValue(balance.remaining_days)}</div>
                                        </td>
                                        <td className="px-6 py-4">
                                            <div className="flex justify-end gap-2">
                                                <button type="button" onClick={() => openEdit(balance)} className="ui-button-secondary px-3 py-2">
                                                    Edit
                                                </button>
                                                <button type="button" onClick={() => destroy(route("admin.leave-balances.destroy", balance.id))} className="rounded-md border border-red-200 px-3 py-2 text-sm font-semibold text-red-600 transition hover:bg-red-50">
                                                    Hapus
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                ))
                            )}
                        </tbody>
                    </table>
                </div>
            </section>

            {showModal && (
                <div className="fixed inset-0 z-[70] flex items-center justify-center bg-slate-950/50 p-4 backdrop-blur-sm">
                    <div className="w-full max-w-2xl rounded-xl bg-white shadow-[var(--shadow-pop)]">
                        <div className="flex items-center justify-between border-b border-[var(--border-line)] px-6 py-4">
                            <h2 className="font-heading text-xl font-semibold">{editingBalance ? "Edit Saldo Cuti" : "Tambah Saldo Cuti"}</h2>
                            <button type="button" onClick={closeModal} className="text-sm text-[var(--text-muted)]">Tutup</button>
                        </div>
                        <form onSubmit={submit} className="grid gap-5 p-6 md:grid-cols-2">
                            <Field error={errors.user_id}>
                                <select value={data.user_id} onChange={(event) => setData("user_id", event.target.value)} className="ui-input" required>
                                    <option value="">Pilih karyawan</option>
                                    {employees.map((employee) => (
                                        <option key={employee.id} value={employee.id}>{employee.name}</option>
                                    ))}
                                </select>
                            </Field>
                            <Field error={errors.leave_type_id}>
                                <select value={data.leave_type_id} onChange={(event) => setData("leave_type_id", event.target.value)} className="ui-input" required>
                                    <option value="">Pilih tipe cuti</option>
                                    {leaveTypes.map((leaveType) => (
                                        <option key={leaveType.id} value={leaveType.id}>{leaveType.name}</option>
                                    ))}
                                </select>
                            </Field>
                            <Field error={errors.year}>
                                <input type="number" value={data.year} onChange={(event) => setData("year", event.target.value)} className="ui-input" placeholder="Tahun" required />
                            </Field>
                            <Field error={errors.allocated_days}>
                                <input type="number" min="0" step="0.5" value={data.allocated_days} onChange={(event) => recalculateRemaining("allocated_days", event.target.value)} className="ui-input" placeholder="Alokasi" required />
                            </Field>
                            <Field error={errors.used_days}>
                                <input type="number" min="0" step="0.5" value={data.used_days} onChange={(event) => recalculateRemaining("used_days", event.target.value)} className="ui-input" placeholder="Terpakai" required />
                            </Field>
                            <Field error={errors.reserved_days}>
                                <input type="number" min="0" step="0.5" value={data.reserved_days} onChange={(event) => recalculateRemaining("reserved_days", event.target.value)} className="ui-input" placeholder="Reserved" required />
                            </Field>
                            <Field error={errors.remaining_days}>
                                <input type="number" min="0" step="0.5" value={data.remaining_days} onChange={(event) => setData("remaining_days", event.target.value)} className="ui-input" placeholder="Sisa" required />
                            </Field>
                            <div className="flex gap-3 md:col-span-2">
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

function Field({ error, children }) {
    return (
        <div>
            {children}
            {error && <p className="mt-1 text-sm text-red-600">{error}</p>}
        </div>
    );
}
