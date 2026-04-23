import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout";
import { Head, Link } from "@inertiajs/react";

export default function LeaveRequests({ leaveRequests, flash }) {
    const getStatusBadge = (status) => {
        const variants = {
            pending: "bg-yellow-500/10 text-yellow-500",
            approved: "bg-green-500/10 text-green-500",
            rejected: "bg-red-500/10 text-red-500",
        };
        const icons = { pending: "⏳", approved: "✅", rejected: "❌" };
        const texts = { pending: "MENUNGGU", approved: "DISETUJUI", rejected: "DITOLAK" };
        
        return (
            <span className={`px-3 py-1 rounded-lg text-xs font-bold flex items-center gap-1 ${variants[status] || variants.pending}`}>
                {icons[status]} {texts[status]}
            </span>
        );
    };

    return (
        <AuthenticatedLayout>
            <Head title="Pengajuan Cuti Saya" />

            <div className="max-w-[1200px] mx-auto p-6">
                {/* Header */}
                <div className="glass rounded-3xl p-6 mb-6 flex justify-between items-center border border-[var(--border-glass)] backdrop-blur-xl">
                    <div>
                        <h1 className="text-2xl font-bold text-[var(--text-main)]">Pengajuan Cuti Saya</h1>
                        <p className="text-[var(--text-muted)] mt-1">Riwayat dan status pengajuan cuti Anda.</p>
                    </div>
                    <Link
                        href={route("employee.leave-requests.create")}
                        className="bg-blue-500 text-white px-5 py-2.5 rounded-xl font-semibold flex items-center gap-2 hover:opacity-80 transition-all"
                    >
                        + Ajukan Cuti
                    </Link>
                </div>

                {/* Flash Message */}
                {flash?.success && (
                    <div className="bg-green-500/15 border border-green-500/30 text-green-500 px-5 py-3 rounded-xl mb-5 font-medium">
                        {flash.success}
                    </div>
                )}

                {/* List */}
                <div className="space-y-4">
                    {leaveRequests.length === 0 ? (
                        <div className="text-center py-12 text-[var(--text-muted)]">
                            Belum ada pengajuan cuti.
                        </div>
                    ) : (
                        leaveRequests.map((req) => (
                            <div
                                key={req.id}
                                className="glass rounded-2xl p-5 flex flex-col md:flex-row justify-between gap-4 hover:-translate-y-0.5 hover:border-blue-500/30 transition-all"
                            >
                                <div className="flex-1">
                                    <div className="flex items-center gap-3 mb-2">
                                        <h3 className="font-bold text-[var(--text-main)]">{req.leave_type?.name}</h3>
                                        {getStatusBadge(req.status)}
                                    </div>
                                    <div className="flex gap-4 text-sm text-[var(--text-muted)] mb-2">
                                        <span>
                                            📅 {new Date(req.start_date).toLocaleDateString("id-ID")} -{" "}
                                            {new Date(req.end_date).toLocaleDateString("id-ID")}
                                        </span>
                                        <span className="px-2 py-0.5 rounded bg-blue-500/10 text-blue-500 font-bold text-xs">
                                            {req.total_days} hari
                                        </span>
                                    </div>
                                    <p className="text-sm text-[var(--text-muted)]">{req.reason}</p>
                                    {req.status !== "pending" && req.approver && (
                                        <div className="mt-3 pt-3 border-t border-[var(--border-glass)] text-xs">
                                            <span className="text-[var(--text-muted)]">
                                                Diproses oleh: <strong>{req.approver.name}</strong> pada{" "}
                                                {new Date(req.updated_at).toLocaleDateString("id-ID")}
                                            </span>
                                            {req.admin_note && (
                                                <p className="text-[var(--text-main)] italic mt-1">
                                                    &quot;{req.admin_note}&quot;
                                                </p>
                                            )}
                                        </div>
                                    )}
                                </div>
                                <div className="text-right">
                                    <div className="text-xs text-[var(--text-muted)]">
                                        Diajukan: {new Date(req.created_at).toLocaleDateString("id-ID")}
                                    </div>
                                    {req.attachment_path && (
                                        <a
                                            href={`/storage/${req.attachment_path}`}
                                            target="_blank"
                                            rel="noopener noreferrer"
                                            className="inline-block mt-2 text-xs text-blue-500 hover:underline"
                                        >
                                            📎 Lihat Lampiran
                                        </a>
                                    )}
                                </div>
                            </div>
                        ))
                    )}
                </div>
            </div>
        </AuthenticatedLayout>
    );
}
