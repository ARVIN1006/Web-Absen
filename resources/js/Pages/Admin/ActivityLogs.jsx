import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout";
import { Head } from "@inertiajs/react";

export default function ActivityLogs({ logs }) {
    return (
        <AuthenticatedLayout>
            <Head title="Audit Trail" />

            <div className="space-y-6">
                <section className="ui-card overflow-hidden">
                    <div className="p-6 lg:p-8">
                        <div className="ui-section-title">Audit Trail</div>
                        <h1 className="font-heading mt-3 text-3xl font-bold text-black">
                            Riwayat aktivitas operasional HRIS
                        </h1>
                        <p className="mt-3 max-w-2xl text-sm leading-6 text-[var(--text-muted)]">
                            Log ini membantu HR dan admin menelusuri approval, perubahan status,
                            serta aktivitas penting yang terjadi di modul inti.
                        </p>
                    </div>
                </section>

                <section className="ui-card overflow-hidden">
                    <div className="overflow-x-auto">
                        <table className="min-w-full divide-y divide-[var(--border-line)] text-sm">
                            <thead className="bg-[var(--bg-subtle)] text-left text-[11px] font-semibold uppercase tracking-[0.14em] text-[var(--text-soft)]">
                                <tr>
                                    <th className="px-6 py-4">Waktu</th>
                                    <th className="px-6 py-4">Aktor</th>
                                    <th className="px-6 py-4">Event</th>
                                    <th className="px-6 py-4">Deskripsi</th>
                                    <th className="px-6 py-4">Objek</th>
                                </tr>
                            </thead>
                            <tbody className="divide-y divide-[var(--border-line)]">
                                {logs.map((log) => (
                                    <tr key={log.id}>
                                        <td className="px-6 py-4 text-[var(--text-muted)]">
                                            {new Date(log.created_at).toLocaleString("id-ID")}
                                        </td>
                                        <td className="px-6 py-4">
                                            <div className="font-semibold text-[var(--text-main)]">
                                                {log.actor?.name || "System"}
                                            </div>
                                            <div className="text-xs text-[var(--text-muted)]">
                                                {log.actor?.email || "-"}
                                            </div>
                                        </td>
                                        <td className="px-6 py-4">
                                            <span className="ui-badge bg-indigo-50 text-[var(--primary-color)]">
                                                {log.event}
                                            </span>
                                        </td>
                                        <td className="px-6 py-4 text-[var(--text-main)]">
                                            {log.description}
                                        </td>
                                        <td className="px-6 py-4 text-[var(--text-muted)]">
                                            {log.subject_type.split("\\").pop()} #{log.subject_id}
                                        </td>
                                    </tr>
                                ))}
                            </tbody>
                        </table>
                    </div>
                </section>
            </div>
        </AuthenticatedLayout>
    );
}
