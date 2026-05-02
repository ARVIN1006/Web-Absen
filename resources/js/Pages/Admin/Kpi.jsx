import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout";
import { Head, router, useForm } from "@inertiajs/react";
import { useState } from "react";

const emptyForm = {
    user_id: "",
    period: "",
    attendance_score: "",
    performance_score: "",
    attitude_score: "",
    feedback: "",
};

function scoreColor(score) {
    if (score >= 80) return "bg-emerald-50 text-emerald-600";
    if (score >= 60) return "bg-amber-50 text-amber-600";
    return "bg-red-50 text-red-600";
}

export default function Kpi({ kpis, employees, filters = {}, flash }) {
    const [showModal, setShowModal] = useState(false);
    const [editingKpi, setEditingKpi] = useState(null);
    const filterForm = useForm({
        period: filters.period || "",
        user_id: filters.user_id || "",
    });
    const form = useForm(emptyForm);

    const average = (kpi) =>
        Math.round((Number(kpi.attendance_score) + Number(kpi.performance_score) + Number(kpi.attitude_score)) / 3);

    const openCreate = () => {
        setEditingKpi(null);
        form.setData(emptyForm);
        form.clearErrors();
        setShowModal(true);
    };

    const openEdit = (kpi) => {
        setEditingKpi(kpi);
        form.setData({
            user_id: kpi.user_id || "",
            period: kpi.period || "",
            attendance_score: kpi.attendance_score || "",
            performance_score: kpi.performance_score || "",
            attitude_score: kpi.attitude_score || "",
            feedback: kpi.feedback || "",
        });
        form.clearErrors();
        setShowModal(true);
    };

    const closeModal = () => {
        setShowModal(false);
        setEditingKpi(null);
        form.reset();
    };

    const submit = (event) => {
        event.preventDefault();
        if (editingKpi) {
            form.put(route("admin.kpi.update", editingKpi.id), { onSuccess: closeModal, preserveScroll: true });
            return;
        }
        form.post(route("admin.kpi.store"), { onSuccess: closeModal, preserveScroll: true });
    };

    const submitFilters = (event) => {
        event.preventDefault();
        router.get(route("admin.kpi.index"), filterForm.data, {
            preserveState: true,
            preserveScroll: true,
        });
    };

    const resetFilters = () => {
        filterForm.setData({ period: "", user_id: "" });
        router.get(route("admin.kpi.index"), {}, { preserveScroll: true });
    };

    const destroy = (kpi) => {
        if (confirm(`Hapus KPI ${kpi.user?.name || "karyawan"} periode ${kpi.period}?`)) {
            router.delete(route("admin.kpi.destroy", kpi.id), { preserveScroll: true });
        }
    };

    return (
        <AuthenticatedLayout>
            <Head title="KPI Karyawan" />

            <section className="ui-card overflow-hidden">
                <div className="flex flex-col gap-5 p-6 lg:flex-row lg:items-end lg:justify-between lg:p-8">
                    <div>
                        <div className="ui-section-title">Performance Review</div>
                        <h1 className="font-heading mt-3 text-3xl font-bold text-black">KPI karyawan</h1>
                    </div>
                    <button type="button" onClick={openCreate} className="ui-button-primary">Tambah Penilaian</button>
                </div>
            </section>

            {flash?.success && <div className="mt-6 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-700">{flash.success}</div>}

            <section className="ui-card mt-6 p-6">
                <form onSubmit={submitFilters} className="grid gap-4 md:grid-cols-[1fr_1fr_auto_auto] md:items-end">
                    <label className="block">
                        <span className="mb-2 block text-sm font-semibold text-[var(--text-main)]">Periode</span>
                        <input value={filterForm.data.period} onChange={(event) => filterForm.setData("period", event.target.value)} className="ui-input" placeholder="2026-Q1 / Mei 2026" />
                    </label>
                    <label className="block">
                        <span className="mb-2 block text-sm font-semibold text-[var(--text-main)]">Karyawan</span>
                        <select value={filterForm.data.user_id} onChange={(event) => filterForm.setData("user_id", event.target.value)} className="ui-input">
                            <option value="">Semua karyawan</option>
                            {employees.map((employee) => (
                                <option key={employee.id} value={employee.id}>{employee.name}</option>
                            ))}
                        </select>
                    </label>
                    <button type="submit" className="ui-button-primary">Filter</button>
                    <button type="button" onClick={resetFilters} className="ui-button-secondary">Reset</button>
                </form>
            </section>

            <section className="ui-card mt-6 overflow-hidden">
                <div className="overflow-x-auto">
                    <table className="min-w-full divide-y divide-[var(--border-line)] text-sm">
                        <thead className="bg-[var(--bg-subtle)] text-left text-[11px] font-semibold uppercase tracking-[0.14em] text-[var(--text-soft)]">
                            <tr>
                                <th className="px-6 py-4">Karyawan</th>
                                <th className="px-6 py-4">Periode</th>
                                <th className="px-6 py-4 text-center">Kehadiran</th>
                                <th className="px-6 py-4 text-center">Performa</th>
                                <th className="px-6 py-4 text-center">Sikap</th>
                                <th className="px-6 py-4 text-center">Rata-rata</th>
                                <th className="px-6 py-4 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody className="divide-y divide-[var(--border-line)]">
                            {kpis.length === 0 ? (
                                <tr>
                                    <td colSpan="7" className="px-6 py-12 text-center text-sm text-[var(--text-muted)]">Belum ada data KPI.</td>
                                </tr>
                            ) : kpis.map((kpi) => {
                                const avg = average(kpi);
                                return (
                                    <tr key={kpi.id}>
                                        <td className="px-6 py-4">
                                            <div className="font-semibold text-[var(--text-main)]">{kpi.user?.name}</div>
                                            <div className="mt-1 text-xs text-[var(--text-muted)]">{kpi.user?.position?.name || "-"}</div>
                                        </td>
                                        <td className="px-6 py-4 text-[var(--text-muted)]">{kpi.period}</td>
                                        <td className="px-6 py-4 text-center"><span className={`ui-badge ${scoreColor(kpi.attendance_score)}`}>{kpi.attendance_score}</span></td>
                                        <td className="px-6 py-4 text-center"><span className={`ui-badge ${scoreColor(kpi.performance_score)}`}>{kpi.performance_score}</span></td>
                                        <td className="px-6 py-4 text-center"><span className={`ui-badge ${scoreColor(kpi.attitude_score)}`}>{kpi.attitude_score}</span></td>
                                        <td className="px-6 py-4 text-center"><span className={`ui-badge ${scoreColor(avg)}`}>{avg}</span></td>
                                        <td className="px-6 py-4">
                                            <div className="flex justify-end gap-2">
                                                <button type="button" onClick={() => openEdit(kpi)} className="ui-button-secondary px-3 py-2">Edit</button>
                                                <button type="button" onClick={() => destroy(kpi)} className="rounded-md border border-red-200 px-3 py-2 text-sm font-semibold text-red-600 transition hover:bg-red-50">Hapus</button>
                                            </div>
                                        </td>
                                    </tr>
                                );
                            })}
                        </tbody>
                    </table>
                </div>
            </section>

            {showModal && (
                <div className="fixed inset-0 z-[70] flex items-center justify-center bg-slate-950/50 p-4 backdrop-blur-sm">
                    <div className="w-full max-w-2xl rounded-xl bg-white shadow-[var(--shadow-pop)]">
                        <div className="flex items-center justify-between border-b border-[var(--border-line)] px-6 py-4">
                            <h2 className="font-heading text-xl font-semibold">{editingKpi ? "Edit KPI" : "Tambah KPI"}</h2>
                            <button type="button" onClick={closeModal} className="text-sm text-[var(--text-muted)]">Tutup</button>
                        </div>
                        <form onSubmit={submit} className="grid gap-5 p-6 md:grid-cols-2">
                            <label className="block md:col-span-2">
                                <span className="mb-2 block text-sm font-semibold text-[var(--text-main)]">Karyawan</span>
                                <select value={form.data.user_id} onChange={(event) => form.setData("user_id", event.target.value)} className="ui-input">
                                    <option value="">Pilih karyawan</option>
                                    {employees.map((employee) => (
                                        <option key={employee.id} value={employee.id}>{employee.name}</option>
                                    ))}
                                </select>
                                {form.errors.user_id && <span className="mt-1 block text-sm text-red-500">{form.errors.user_id}</span>}
                            </label>
                            <label className="block md:col-span-2">
                                <span className="mb-2 block text-sm font-semibold text-[var(--text-main)]">Periode</span>
                                <input value={form.data.period} onChange={(event) => form.setData("period", event.target.value)} className="ui-input" placeholder="2026-Q1 / Mei 2026" />
                                {form.errors.period && <span className="mt-1 block text-sm text-red-500">{form.errors.period}</span>}
                            </label>
                            <input type="number" min="0" max="100" value={form.data.attendance_score} onChange={(event) => form.setData("attendance_score", event.target.value)} className="ui-input" placeholder="Skor kehadiran" />
                            <input type="number" min="0" max="100" value={form.data.performance_score} onChange={(event) => form.setData("performance_score", event.target.value)} className="ui-input" placeholder="Skor performa" />
                            <input type="number" min="0" max="100" value={form.data.attitude_score} onChange={(event) => form.setData("attitude_score", event.target.value)} className="ui-input" placeholder="Skor sikap" />
                            <textarea value={form.data.feedback} onChange={(event) => form.setData("feedback", event.target.value)} className="ui-input md:col-span-2" rows="3" placeholder="Feedback" />
                            <div className="flex gap-3 md:col-span-2">
                                <button type="submit" disabled={form.processing} className="ui-button-primary">{form.processing ? "Menyimpan..." : "Simpan"}</button>
                                <button type="button" onClick={closeModal} className="ui-button-secondary">Batal</button>
                            </div>
                        </form>
                    </div>
                </div>
            )}
        </AuthenticatedLayout>
    );
}
