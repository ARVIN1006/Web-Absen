import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout";
import { Head, useForm } from "@inertiajs/react";
import { useState } from "react";

export default function Kpi({ kpis, employees, flash }) {
    const [showModal, setShowModal] = useState(false);
    const { data, setData, post, processing, errors, reset } = useForm({
        user_id: "",
        period: "",
        attendance_score: "",
        performance_score: "",
        attitude_score: "",
    });

    const openModal = () => {
        reset();
        setShowModal(true);
    };

    const closeModal = () => {
        setShowModal(false);
        reset();
    };

    const handleSubmit = (e) => {
        e.preventDefault();
        post(route("admin.kpi.store"), {
            onSuccess: closeModal,
        });
    };

    const calculateAverage = (kpi) => {
        return Math.round((kpi.attendance_score + kpi.performance_score + kpi.attitude_score) / 3);
    };

    const getScoreColor = (score) => {
        if (score >= 80) return "#10b981";
        if (score >= 60) return "#f59e0b";
        return "#ef4444";
    };

    return (
        <AuthenticatedLayout>
            <Head title="KPI Karyawan" />

            <div className="max-w-[1200px] mx-auto p-6">
                {/* Header */}
                <div className="glass rounded-3xl p-6 mb-6 flex justify-between items-center border border-[var(--border-glass)] backdrop-blur-xl">
                    <h1 className="text-2xl font-bold text-[var(--text-main)]">KPI Karyawan</h1>
                    <button
                        onClick={openModal}
                        className="bg-blue-500 text-white px-5 py-2.5 rounded-xl font-semibold flex items-center gap-2 hover:opacity-80 transition-all"
                    >
                        + Tambah Penilaian
                    </button>
                </div>

                {/* Flash Messages */}
                {flash?.success && (
                    <div className="bg-green-500/15 border border-green-500/30 text-green-500 px-5 py-3 rounded-xl mb-5 font-medium">
                        {flash.success}
                    </div>
                )}

                {/* Table */}
                <div className="glass rounded-2xl overflow-hidden">
                    <div className="overflow-x-auto">
                        <table className="w-full">
                            <thead>
                                <tr className="bg-black/5 border-b border-[var(--border-glass)]">
                                    <th className="px-6 py-4 text-left text-xs font-bold text-[var(--text-muted-dark)] uppercase">
                                        Karyawan
                                    </th>
                                    <th className="px-6 py-4 text-left text-xs font-bold text-[var(--text-muted-dark)] uppercase">
                                        Periode
                                    </th>
                                    <th className="px-6 py-4 text-center text-xs font-bold text-[var(--text-muted-dark)] uppercase">
                                        Kehadiran
                                    </th>
                                    <th className="px-6 py-4 text-center text-xs font-bold text-[var(--text-muted-dark)] uppercase">
                                        Performa
                                    </th>
                                    <th className="px-6 py-4 text-center text-xs font-bold text-[var(--text-muted-dark)] uppercase">
                                        Sikap
                                    </th>
                                    <th className="px-6 py-4 text-center text-xs font-bold text-[var(--text-muted-dark)] uppercase">
                                        Rata-rata
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                {kpis.length === 0 ? (
                                    <tr>
                                        <td colSpan="6" className="text-center py-12 text-[var(--text-muted)]">
                                            Belum ada data KPI.
                                        </td>
                                    </tr>
                                ) : (
                                    kpis.map((kpi) => {
                                        const avg = calculateAverage(kpi);
                                        return (
                                            <tr
                                                key={kpi.id}
                                                className="border-b border-[var(--border-glass)] hover:bg-[var(--hover-bg)] transition-colors"
                                            >
                                                <td className="px-6 py-4">
                                                    <div className="flex items-center gap-3">
                                                        <div className="w-10 h-10 rounded-xl bg-gradient-to-br from-blue-500 to-green-500 text-white flex items-center justify-center font-bold">
                                                            {kpi.user?.name?.charAt(0).toUpperCase()}
                                                        </div>
                                                        <div>
                                                            <div className="font-semibold text-[var(--text-main)]">
                                                                {kpi.user?.name}
                                                            </div>
                                                            <div className="text-xs text-[var(--text-muted)]">
                                                                {kpi.user?.position?.name}
                                                            </div>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td className="px-6 py-4 text-sm text-[var(--text-muted)]">
                                                    {kpi.period}
                                                </td>
                                                <td className="px-6 py-4 text-center">
                                                    <span
                                                        className="px-3 py-1 rounded-lg text-sm font-bold"
                                                        style={{
                                                            backgroundColor: `${getScoreColor(kpi.attendance_score)}20`,
                                                            color: getScoreColor(kpi.attendance_score),
                                                        }}
                                                    >
                                                        {kpi.attendance_score}
                                                    </span>
                                                </td>
                                                <td className="px-6 py-4 text-center">
                                                    <span
                                                        className="px-3 py-1 rounded-lg text-sm font-bold"
                                                        style={{
                                                            backgroundColor: `${getScoreColor(kpi.performance_score)}20`,
                                                            color: getScoreColor(kpi.performance_score),
                                                        }}
                                                    >
                                                        {kpi.performance_score}
                                                    </span>
                                                </td>
                                                <td className="px-6 py-4 text-center">
                                                    <span
                                                        className="px-3 py-1 rounded-lg text-sm font-bold"
                                                        style={{
                                                            backgroundColor: `${getScoreColor(kpi.attitude_score)}20`,
                                                            color: getScoreColor(kpi.attitude_score),
                                                        }}
                                                    >
                                                        {kpi.attitude_score}
                                                    </span>
                                                </td>
                                                <td className="px-6 py-4 text-center">
                                                    <span
                                                        className="px-4 py-2 rounded-xl text-lg font-extrabold"
                                                        style={{
                                                            backgroundColor: `${getScoreColor(avg)}20`,
                                                            color: getScoreColor(avg),
                                                        }}
                                                    >
                                                        {avg}
                                                    </span>
                                                </td>
                                            </tr>
                                        );
                                    })
                                )}
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            {/* Modal */}
            {showModal && (
                <div className="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex items-center justify-center p-4">
                    <div className="bg-[var(--bg-body)] border border-[var(--border-glass)] rounded-2xl w-full max-w-md overflow-hidden">
                        <div className="px-6 py-4 border-b border-[var(--border-glass)] flex justify-between items-center">
                            <h3 className="text-lg font-bold text-[var(--text-main)]">Tambah Penilaian KPI</h3>
                            <button
                                onClick={closeModal}
                                className="text-[var(--text-muted)] hover:text-[var(--text-main)] text-2xl"
                            >
                                &times;
                            </button>
                        </div>
                        <form onSubmit={handleSubmit} className="p-6 space-y-4">
                            <div>
                                <label className="block text-sm font-semibold text-[var(--text-muted)] mb-2">
                                    Karyawan <span className="text-red-500">*</span>
                                </label>
                                <select
                                    value={data.user_id}
                                    onChange={(e) => setData("user_id", e.target.value)}
                                    className="w-full px-4 py-3 rounded-xl bg-black/5 border border-[var(--border-glass)] focus:border-blue-500 outline-none transition-all"
                                    required
                                >
                                    <option value="">Pilih Karyawan</option>
                                    {employees.map((emp) => (
                                        <option key={emp.id} value={emp.id}>
                                            {emp.name}
                                        </option>
                                    ))}
                                </select>
                                {errors.user_id && <p className="text-red-500 text-sm mt-1">{errors.user_id}</p>}
                            </div>
                            <div>
                                <label className="block text-sm font-semibold text-[var(--text-muted)] mb-2">
                                    Periode <span className="text-red-500">*</span>
                                </label>
                                <input
                                    type="text"
                                    value={data.period}
                                    onChange={(e) => setData("period", e.target.value)}
                                    className="w-full px-4 py-3 rounded-xl bg-black/5 border border-[var(--border-glass)] focus:border-blue-500 outline-none transition-all"
                                    placeholder="e.g., 2026-Q1, Januari 2026"
                                    required
                                />
                                {errors.period && <p className="text-red-500 text-sm mt-1">{errors.period}</p>}
                            </div>
                            <div className="grid grid-cols-3 gap-3">
                                <div>
                                    <label className="block text-sm font-semibold text-[var(--text-muted)] mb-2">
                                        Kehadiran <span className="text-red-500">*</span>
                                    </label>
                                    <input
                                        type="number"
                                        min="0"
                                        max="100"
                                        value={data.attendance_score}
                                        onChange={(e) => setData("attendance_score", e.target.value)}
                                        className="w-full px-4 py-3 rounded-xl bg-black/5 border border-[var(--border-glass)] focus:border-blue-500 outline-none transition-all"
                                        required
                                    />
                                </div>
                                <div>
                                    <label className="block text-sm font-semibold text-[var(--text-muted)] mb-2">
                                        Performa <span className="text-red-500">*</span>
                                    </label>
                                    <input
                                        type="number"
                                        min="0"
                                        max="100"
                                        value={data.performance_score}
                                        onChange={(e) => setData("performance_score", e.target.value)}
                                        className="w-full px-4 py-3 rounded-xl bg-black/5 border border-[var(--border-glass)] focus:border-blue-500 outline-none transition-all"
                                        required
                                    />
                                </div>
                                <div>
                                    <label className="block text-sm font-semibold text-[var(--text-muted)] mb-2">
                                        Sikap <span className="text-red-500">*</span>
                                    </label>
                                    <input
                                        type="number"
                                        min="0"
                                        max="100"
                                        value={data.attitude_score}
                                        onChange={(e) => setData("attitude_score", e.target.value)}
                                        className="w-full px-4 py-3 rounded-xl bg-black/5 border border-[var(--border-glass)] focus:border-blue-500 outline-none transition-all"
                                        required
                                    />
                                </div>
                            </div>
                            <button
                                type="submit"
                                disabled={processing}
                                className="w-full bg-blue-500 text-white py-3 rounded-xl font-bold hover:opacity-90 transition-all disabled:opacity-50"
                            >
                                {processing ? "Menyimpan..." : "Simpan Penilaian"}
                            </button>
                        </form>
                    </div>
                </div>
            )}
        </AuthenticatedLayout>
    );
}
