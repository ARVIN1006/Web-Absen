import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout";
import { Head, useForm } from "@inertiajs/react";
import { useState } from "react";

export default function LeaveRequests({ leaveRequests, flash }) {
    const [showApproveModal, setShowApproveModal] = useState(false);
    const [showRejectModal, setShowRejectModal] = useState(false);
    const [processingId, setProcessingId] = useState(null);

    const { data, setData, post, processing, reset } = useForm({
        admin_note: "",
    });

    const openApproveModal = (id) => {
        setProcessingId(id);
        reset();
        setShowApproveModal(true);
    };

    const openRejectModal = (id) => {
        setProcessingId(id);
        reset();
        setShowRejectModal(true);
    };

    const closeModals = () => {
        setShowApproveModal(false);
        setShowRejectModal(false);
        setProcessingId(null);
        reset();
    };

    const handleApprove = (e) => {
        e.preventDefault();
        post(route("admin.leave-requests.approve", processingId), {
            onSuccess: closeModals,
        });
    };

    const handleReject = (e) => {
        e.preventDefault();
        post(route("admin.leave-requests.reject", processingId), {
            onSuccess: closeModals,
        });
    };

    const getStatusBadge = (status) => {
        const styles = {
            pending: {
                bg: "rgba(245, 158, 11, 0.1)",
                color: "#f59e0b",
                border: "rgba(245, 158, 11, 0.2)",
                icon: "⏳",
            },
            approved: {
                bg: "rgba(16, 185, 129, 0.1)",
                color: "#10b981",
                border: "rgba(16, 185, 129, 0.2)",
                icon: "✅",
            },
            rejected: {
                bg: "rgba(239, 68, 68, 0.1)",
                color: "#ef4444",
                border: "rgba(239, 68, 68, 0.2)",
                icon: "❌",
            },
        };
        const style = styles[status] || styles.pending;

        return (
            <span
                className="absolute top-6 right-6 px-4 py-2 rounded-xl text-xs font-extrabold uppercase tracking-wide flex items-center gap-1.5 border"
                style={{
                    backgroundColor: style.bg,
                    color: style.color,
                    borderColor: style.border,
                }}
            >
                {style.icon} {status.toUpperCase()}
            </span>
        );
    };

    return (
        <AuthenticatedLayout>
            <Head title="Pengajuan Cuti" />

            <div className="leave-container max-w-[1200px] mx-auto p-6">
                {/* Header */}
                <div className="glass rounded-3xl p-6 mb-8 border border-[var(--border-glass)] backdrop-blur-xl">
                    <h1 className="text-3xl font-extrabold bg-gradient-to-r from-[var(--text-main)] to-[var(--text-muted)] bg-clip-text text-transparent">
                        Pengajuan Cuti
                    </h1>
                    <p className="text-[var(--text-muted)] mt-2">Kelola izin dan ketidakhadiran karyawan.</p>
                </div>

                {/* Flash Message */}
                {flash?.success && (
                    <div className="bg-green-500/10 border-l-4 border-green-500 text-green-500 px-5 py-4 rounded-xl mb-6 flex items-center gap-3">
                        <svg width="20" height="20" fill="currentColor" viewBox="0 0 20 20">
                            <path fillRule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clipRule="evenodd" />
                        </svg>
                        <span className="font-semibold">{flash.success}</span>
                    </div>
                )}

                {/* Grid */}
                <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    {leaveRequests.map((req) => (
                        <div
                            key={req.id}
                            className="glass rounded-3xl p-7 relative flex flex-col overflow-hidden hover:-translate-y-1 hover:border-blue-500/50 hover:shadow-xl transition-all"
                        >
                            {getStatusBadge(req.status)}

                            <div className="flex items-center gap-3 mb-5 pr-28">
                                <div className="w-11 h-11 rounded-xl bg-gradient-to-br from-blue-500 to-green-500 text-white flex items-center justify-center font-extrabold text-lg">
                                    {req.user?.name?.charAt(0).toUpperCase()}
                                </div>
                                <div>
                                    <div className="text-lg font-extrabold text-[var(--text-main)]">
                                        {req.user?.name}
                                    </div>
                                    <div className="text-sm text-[var(--text-muted)]">
                                        {req.user?.position?.name || "Karyawan"}
                                    </div>
                                </div>
                            </div>

                            <div className="bg-black/5 rounded-2xl p-5 mb-5 border border-[var(--border-glass)]">
                                <div className="flex justify-between items-center mb-2">
                                    <span className="text-xs text-[var(--text-muted)] font-semibold">Tipe Cuti</span>
                                    <span className="text-sm font-bold text-blue-500">{req.leave_type?.name}</span>
                                </div>
                                <div className="flex justify-between items-center mb-2">
                                    <span className="text-xs text-[var(--text-muted)] font-semibold">Periode</span>
                                    <span className="text-sm font-bold text-[var(--text-main)] text-right">
                                        {new Date(req.start_date).toLocaleDateString("id-ID", { day: "2-digit", month: "short" })} -{" "}
                                        {new Date(req.end_date).toLocaleDateString("id-ID", { day: "2-digit", month: "short", year: "2-digit" })}
                                        <span className="ml-2 px-2 py-0.5 rounded-md bg-blue-500 text-white text-xs font-extrabold">
                                            {req.total_days} Hari
                                        </span>
                                    </span>
                                </div>
                                <div className="flex justify-between items-center">
                                    <span className="text-xs text-[var(--text-muted)] font-semibold">Diajukan</span>
                                    <span className="text-sm font-bold text-[var(--text-main)]">
                                        {new Date(req.created_at).toLocaleDateString("id-ID", { day: "numeric", month: "short", year: "numeric" })}
                                    </span>
                                </div>
                            </div>

                            <div className="text-sm text-[var(--text-main)] opacity-80 leading-relaxed mb-5 line-clamp-2">
                                &quot;{req.reason}&quot;
                                {req.attachment_path && (
                                    <a
                                        href={`/storage/${req.attachment_path}`}
                                        target="_blank"
                                        rel="noopener noreferrer"
                                        className="block mt-2 text-xs text-blue-500 font-bold hover:underline"
                                    >
                                        📎 Lihat Lampiran
                                    </a>
                                )}
                            </div>

                            {req.status === "pending" ? (
                                <div className="flex gap-3 mt-auto">
                                    <button
                                        onClick={() => openApproveModal(req.id)}
                                        className="flex-1 bg-green-500 text-white py-3 rounded-2xl font-bold flex items-center justify-center gap-2 hover:-translate-y-0.5 hover:shadow-lg transition-all"
                                    >
                                        Setujui
                                    </button>
                                    <button
                                        onClick={() => openRejectModal(req.id)}
                                        className="flex-1 bg-red-500/10 text-red-500 border border-red-500/20 py-3 rounded-2xl font-bold flex items-center justify-center gap-2 hover:bg-red-500 hover:text-white transition-all"
                                    >
                                        Tolak
                                    </button>
                                </div>
                            ) : (
                                <div className="text-xs text-[var(--text-muted)] pt-4 border-t border-[var(--border-glass)]">
                                    <strong>Keputusan:</strong> {req.approver?.name || "-"} pada{" "}
                                    {new Date(req.updated_at).toLocaleDateString("id-ID", { day: "2-digit", month: "short", year: "2-digit" })}
                                    {req.admin_note && (
                                        <div className="mt-1 text-[var(--text-main)] italic">&quot;{req.admin_note}&quot;</div>
                                    )}
                                </div>
                            )}
                        </div>
                    ))}
                </div>

                {leaveRequests.length === 0 && (
                    <div className="text-center py-20 glass rounded-3xl border border-dashed border-[var(--border-glass)]">
                        <div className="text-5xl mb-6 opacity-40">🏖️</div>
                        <h3 className="text-2xl font-bold text-[var(--text-main)] mb-3">Tidak Ada Pengajuan</h3>
                        <p className="text-[var(--text-muted)]">Daftar pengajuan cuti karyawan akan muncul di sini.</p>
                    </div>
                )}
            </div>

            {/* Approve Modal */}
            {showApproveModal && (
                <Modal title="Setujui Pengajuan" onClose={closeModals}>
                    <form onSubmit={handleApprove} className="space-y-4">
                        <div>
                            <label className="block text-xs font-bold text-[var(--text-muted-dark)] uppercase tracking-wide mb-2">
                                Catatan (Opsional)
                            </label>
                            <textarea
                                value={data.admin_note}
                                onChange={(e) => setData("admin_note", e.target.value)}
                                rows="3"
                                className="w-full px-4 py-3 rounded-xl bg-black/5 border border-[var(--border-line)] focus:border-green-500 outline-none transition-all resize-none"
                                placeholder="Berikan pesan singkat untuk karyawan..."
                            />
                        </div>
                        <button
                            type="submit"
                            disabled={processing}
                            className="w-full bg-green-500 text-white py-4 rounded-xl font-bold hover:shadow-lg transition-all disabled:opacity-50"
                        >
                            {processing ? "Memproses..." : "Konfirmasi Persetujuan"}
                        </button>
                    </form>
                </Modal>
            )}

            {/* Reject Modal */}
            {showRejectModal && (
                <Modal title="Tolak Pengajuan" onClose={closeModals}>
                    <form onSubmit={handleReject} className="space-y-4">
                        <div>
                            <label className="block text-xs font-bold text-[var(--text-muted-dark)] uppercase tracking-wide mb-2">
                                Alasan Penolakan <span className="text-red-500">*</span>
                            </label>
                            <textarea
                                value={data.admin_note}
                                onChange={(e) => setData("admin_note", e.target.value)}
                                rows="3"
                                className="w-full px-4 py-3 rounded-xl bg-black/5 border border-[var(--border-line)] focus:border-red-500 outline-none transition-all resize-none"
                                placeholder="Wajib diisi agar karyawan tahu alasannya..."
                                required
                            />
                        </div>
                        <button
                            type="submit"
                            disabled={processing}
                            className="w-full bg-red-500 text-white py-4 rounded-xl font-bold hover:shadow-lg transition-all disabled:opacity-50"
                        >
                            {processing ? "Memproses..." : "Konfirmasi Penolakan"}
                        </button>
                    </form>
                </Modal>
            )}
        </AuthenticatedLayout>
    );
}

function Modal({ title, children, onClose }) {
    return (
        <div className="fixed inset-0 bg-black/60 backdrop-blur-sm z-50 flex items-center justify-center p-4">
            <div className="bg-[var(--bg-body)] border border-[var(--border-glass)] rounded-3xl w-full max-w-md shadow-2xl overflow-hidden">
                <div className="px-6 py-4 border-b border-[var(--border-line)] bg-black/5 flex justify-between items-center">
                    <h3 className="font-extrabold text-lg">{title}</h3>
                    <button onClick={onClose} className="text-[var(--text-muted)] hover:text-[var(--text-main)] text-2xl">
                        &times;
                    </button>
                </div>
                <div className="p-6">{children}</div>
            </div>
        </div>
    );
}
