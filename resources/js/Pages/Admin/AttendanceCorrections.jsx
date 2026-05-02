import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout";
import { Head, useForm } from "@inertiajs/react";
import { useState } from "react";

function formatDateTime(value) {
    if (!value) return "-";

    return new Date(value).toLocaleString("id-ID", {
        day: "2-digit",
        month: "short",
        year: "numeric",
        hour: "2-digit",
        minute: "2-digit",
    });
}

function statusBadge(status) {
    const styles = {
        pending: "bg-amber-50 text-[var(--warning-color)]",
        approved: "bg-emerald-50 text-[var(--success-color)]",
        rejected: "bg-red-50 text-[var(--danger-color)]",
    };

    return <span className={`ui-badge ${styles[status] || styles.pending}`}>{status}</span>;
}

export default function AttendanceCorrections({ corrections = [], flash }) {
    const [activeCorrection, setActiveCorrection] = useState(null);
    const [action, setAction] = useState(null);
    const { data, setData, post, processing, reset, errors } = useForm({
        admin_note: "",
    });

    const openModal = (correction, nextAction) => {
        setActiveCorrection(correction);
        setAction(nextAction);
        reset();
    };

    const closeModal = () => {
        setActiveCorrection(null);
        setAction(null);
        reset();
    };

    const submit = (event) => {
        event.preventDefault();
        post(route(`admin.attendance-corrections.${action}`, activeCorrection.id), {
            onSuccess: closeModal,
        });
    };

    return (
        <AuthenticatedLayout>
            <Head title="Koreksi Absensi" />

            <section className="ui-card overflow-hidden">
                <div className="flex flex-col gap-3 p-6 lg:p-8">
                    <div className="ui-section-title">Attendance Corrections</div>
                    <h1 className="font-heading text-3xl font-bold text-black">Koreksi absensi karyawan</h1>
                    <p className="max-w-3xl text-sm leading-6 text-[var(--text-muted)]">
                        Tinjau permintaan koreksi jam masuk atau pulang. Saat disetujui, data absensi terkait akan ikut diperbarui.
                    </p>
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
                                <th className="px-6 py-4">Tanggal</th>
                                <th className="px-6 py-4">Jam Diajukan</th>
                                <th className="px-6 py-4">Alasan</th>
                                <th className="px-6 py-4">Status</th>
                                <th className="px-6 py-4 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody className="divide-y divide-[var(--border-line)]">
                            {corrections.length === 0 ? (
                                <tr>
                                    <td className="px-6 py-12 text-center text-[var(--text-muted)]" colSpan="6">
                                        Belum ada permintaan koreksi absensi.
                                    </td>
                                </tr>
                            ) : (
                                corrections.map((correction) => (
                                    <tr key={correction.id}>
                                        <td className="px-6 py-4">
                                            <div className="font-semibold text-[var(--text-main)]">{correction.user?.name || "-"}</div>
                                            <div className="mt-1 text-xs text-[var(--text-muted)]">{correction.user?.email || "-"}</div>
                                        </td>
                                        <td className="px-6 py-4 text-[var(--text-muted)]">
                                            {correction.attendance_date}
                                        </td>
                                        <td className="px-6 py-4 text-[var(--text-muted)]">
                                            <div>Masuk: {formatDateTime(correction.requested_check_in_at)}</div>
                                            <div className="mt-1">Pulang: {formatDateTime(correction.requested_check_out_at)}</div>
                                        </td>
                                        <td className="max-w-xs px-6 py-4 text-[var(--text-muted)]">
                                            <div className="line-clamp-2">{correction.reason}</div>
                                            {correction.attachment_path && (
                                                <a href={`/storage/${correction.attachment_path}`} target="_blank" rel="noopener noreferrer" className="mt-2 inline-flex text-xs font-semibold text-[var(--primary-color)] hover:underline">
                                                    Lihat lampiran
                                                </a>
                                            )}
                                            {correction.admin_note && <div className="mt-2 text-xs text-[var(--text-main)]">Catatan admin: {correction.admin_note}</div>}
                                        </td>
                                        <td className="px-6 py-4">{statusBadge(correction.status)}</td>
                                        <td className="px-6 py-4">
                                            <div className="flex justify-end gap-2">
                                                {correction.status === "pending" ? (
                                                    <>
                                                        <button type="button" onClick={() => openModal(correction, "approve")} className="ui-button-primary px-3 py-2">
                                                            Setujui
                                                        </button>
                                                        <button type="button" onClick={() => openModal(correction, "reject")} className="rounded-md border border-red-200 px-3 py-2 text-sm font-semibold text-red-600 transition hover:bg-red-50">
                                                            Tolak
                                                        </button>
                                                    </>
                                                ) : (
                                                    <span className="text-xs text-[var(--text-muted)]">Diproses oleh {correction.approver?.name || "-"}</span>
                                                )}
                                            </div>
                                        </td>
                                    </tr>
                                ))
                            )}
                        </tbody>
                    </table>
                </div>
            </section>

            {activeCorrection && (
                <div className="fixed inset-0 z-[70] flex items-center justify-center bg-slate-950/50 p-4 backdrop-blur-sm">
                    <div className="w-full max-w-md rounded-xl bg-white shadow-[var(--shadow-pop)]">
                        <div className="border-b border-[var(--border-line)] px-6 py-4">
                            <h2 className="font-heading text-xl font-semibold">
                                {action === "approve" ? "Setujui Koreksi" : "Tolak Koreksi"}
                            </h2>
                        </div>
                        <form onSubmit={submit} className="space-y-4 p-6">
                            <textarea
                                value={data.admin_note}
                                onChange={(event) => setData("admin_note", event.target.value)}
                                rows="4"
                                className="ui-input"
                                placeholder={action === "approve" ? "Catatan opsional" : "Alasan penolakan"}
                                required={action === "reject"}
                            />
                            {errors.admin_note && <p className="text-sm text-red-600">{errors.admin_note}</p>}
                            <div className="flex gap-3">
                                <button type="submit" disabled={processing} className={action === "approve" ? "ui-button-primary flex-1" : "flex-1 rounded-md bg-red-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-red-700 disabled:opacity-60"}>
                                    {processing ? "Memproses..." : "Konfirmasi"}
                                </button>
                                <button type="button" onClick={closeModal} className="ui-button-secondary flex-1">
                                    Batal
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            )}
        </AuthenticatedLayout>
    );
}
