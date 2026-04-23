import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout";
import { Head } from "@inertiajs/react";
import { Line } from "react-chartjs-2";
import {
    Chart as ChartJS,
    CategoryScale,
    LinearScale,
    PointElement,
    LineElement,
    Tooltip,
    Legend,
    Filler,
} from "chart.js";

ChartJS.register(CategoryScale, LinearScale, PointElement, LineElement, Tooltip, Legend, Filler);

function StatCard({ title, value, subtitle, tone = "default" }) {
    const toneClass = {
        primary: "bg-[rgba(70,72,212,0.08)] text-[var(--primary-color)]",
        success: "bg-emerald-50 text-[var(--success-color)]",
        warning: "bg-amber-50 text-[var(--warning-color)]",
        danger: "bg-red-50 text-[var(--danger-color)]",
        default: "bg-slate-50 text-[var(--text-main)]",
    }[tone];

    return (
        <div className="ui-card p-4 sm:p-5">
            <div className="flex items-start justify-between gap-3">
                <div className="min-w-0">
                    <div className="text-[10px] font-semibold uppercase tracking-[0.16em] text-[var(--text-soft)] sm:text-xs">
                        {title}
                    </div>
                    <div className="mt-2 text-xs leading-5 text-[var(--text-muted)] sm:text-sm">{subtitle}</div>
                </div>
                <div className={`shrink-0 rounded-2xl px-3 py-2 font-heading text-2xl font-extrabold sm:text-3xl ${toneClass}`}>
                    {value}
                </div>
            </div>
        </div>
    );
}

function MiniInsight({ label, value, helper, tone = "default" }) {
    const toneClass = {
        success: "border-emerald-200 bg-emerald-50 text-emerald-700",
        warning: "border-amber-200 bg-amber-50 text-amber-800",
        danger: "border-red-200 bg-red-50 text-red-700",
        default: "border-slate-200 bg-slate-50 text-slate-700",
    }[tone];

    return (
        <div className={`rounded-2xl border px-4 py-3 ${toneClass}`}>
            <div className="text-[11px] font-bold uppercase tracking-[0.16em] opacity-70">{label}</div>
            <div className="mt-1 font-heading text-xl font-extrabold">{value}</div>
            <div className="mt-1 text-xs leading-5 opacity-80">{helper}</div>
        </div>
    );
}

export default function Dashboard({
    totalEmployees,
    todayAttendancesCount,
    presentToday,
    absentToday,
    totalLocations,
    pendingLeaves,
    pendingCorrections,
    expiringDocumentsCount,
    expiredDocumentsCount,
    recentAttendances,
    chartLabels,
    chartData,
    chartDataOut,
}) {
    const attendanceRate = totalEmployees > 0 ? Math.round((presentToday / totalEmployees) * 100) : 0;
    const attentionTotal = pendingLeaves + pendingCorrections + expiringDocumentsCount + expiredDocumentsCount;

    const data = {
        labels: chartLabels,
        datasets: [
            {
                label: "Masuk",
                data: chartData,
                borderColor: "#4648d4",
                backgroundColor: "rgba(70, 72, 212, 0.12)",
                fill: true,
                tension: 0.35,
                borderWidth: 3,
                pointBackgroundColor: "#4648d4",
                pointRadius: 3,
            },
            {
                label: "Pulang",
                data: chartDataOut,
                borderColor: "#0f9f6e",
                backgroundColor: "rgba(15, 159, 110, 0.08)",
                fill: true,
                tension: 0.35,
                borderWidth: 3,
                pointBackgroundColor: "#0f9f6e",
                pointRadius: 3,
            },
        ],
    };

    const options = {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                position: "top",
                align: "end",
                labels: {
                    color: "#5f6b7a",
                    boxWidth: 12,
                    usePointStyle: true,
                    pointStyle: "circle",
                },
            },
        },
        scales: {
            y: {
                beginAtZero: true,
                grid: { color: "#eef2f7" },
                ticks: { color: "#76859b", precision: 0 },
            },
            x: {
                grid: { display: false },
                ticks: { color: "#76859b", maxRotation: 0 },
            },
        },
    };

    return (
        <AuthenticatedLayout>
            <Head title="Admin Dashboard" />

            <section className="ui-card overflow-hidden">
                <div className="grid gap-4 p-4 sm:p-6 lg:grid-cols-[1.45fr_0.8fr] lg:p-8">
                    <div>
                        <div className="ui-section-title">Admin Dashboard</div>
                        <h1 className="font-heading mt-2 text-2xl font-bold text-black sm:text-3xl lg:text-4xl">
                            Kontrol operasional kehadiran dan SDM
                        </h1>
                        <p className="mt-3 max-w-2xl text-sm leading-6 text-[var(--text-muted)] sm:text-base">
                            Pantau presensi, antrian approval, dokumen kritis, dan aktivitas terbaru dari satu layar yang nyaman di mobile.
                        </p>

                        <div className="mt-4 grid grid-cols-2 gap-3 sm:grid-cols-4">
                            <MiniInsight
                                label="Hadir"
                                value={`${attendanceRate}%`}
                                helper="coverage hari ini"
                                tone={attendanceRate >= 80 ? "success" : "warning"}
                            />
                            <MiniInsight label="Transaksi" value={todayAttendancesCount} helper="masuk + pulang" />
                            <MiniInsight
                                label="Perlu review"
                                value={attentionTotal}
                                helper="approval & dokumen"
                                tone={attentionTotal > 0 ? "warning" : "success"}
                            />
                            <MiniInsight label="Lokasi" value={totalLocations} helper="aktif geofence" />
                        </div>
                    </div>

                    <div className="ui-card-muted p-4 sm:p-5">
                        <div className="ui-section-title">Kehadiran Hari Ini</div>
                        <div className="mt-3 font-heading text-4xl font-bold text-[var(--primary-color)] sm:text-5xl">
                            {presentToday} / {totalEmployees}
                        </div>
                        <div className="mt-2 text-sm leading-6 text-[var(--text-muted)]">
                            {todayAttendancesCount} total transaksi presensi tercatat pada hari ini.
                        </div>
                        <div className="mt-4 h-3 overflow-hidden rounded-full bg-white">
                            <div className="h-full rounded-full bg-[var(--primary-color)] transition-all" style={{ width: `${attendanceRate}%` }} />
                        </div>
                    </div>
                </div>
            </section>

            <section className="mt-5 grid grid-cols-1 gap-3 sm:grid-cols-2 xl:grid-cols-4">
                <StatCard title="Total Karyawan" value={totalEmployees} subtitle="Seluruh karyawan aktif" tone="primary" />
                <StatCard title="Hadir Hari Ini" value={presentToday} subtitle="Sudah melakukan presensi" tone="success" />
                <StatCard title="Tidak Hadir" value={absentToday} subtitle="Belum tercatat masuk" tone={absentToday > 0 ? "warning" : "success"} />
                <StatCard title="Cuti Pending" value={pendingLeaves} subtitle="Menunggu review admin" tone={pendingLeaves > 0 ? "warning" : "default"} />
                <StatCard title="Lokasi Aktif" value={totalLocations} subtitle="Titik geofence siap pakai" tone="primary" />
                <StatCard title="Koreksi Pending" value={pendingCorrections} subtitle="Butuh review HR" tone={pendingCorrections > 0 ? "warning" : "default"} />
                <StatCard title="Dokumen Expiring" value={expiringDocumentsCount} subtitle="Kedaluwarsa < 30 hari" tone={expiringDocumentsCount > 0 ? "warning" : "default"} />
                <StatCard title="Dokumen Expired" value={expiredDocumentsCount} subtitle="Perlu tindak lanjut" tone={expiredDocumentsCount > 0 ? "danger" : "default"} />
            </section>

            <section className="mt-5 grid gap-5 xl:grid-cols-[1.45fr_0.95fr]">
                <div className="ui-card p-4 sm:p-6">
                    <div className="flex flex-wrap items-center justify-between gap-2 border-b border-[var(--border-line)] pb-4">
                        <h2 className="font-heading text-lg font-semibold sm:text-xl">Tren Kehadiran 7 Hari Terakhir</h2>
                        <span className="text-xs font-semibold uppercase tracking-[0.18em] text-[var(--text-soft)]">Analytics</span>
                    </div>
                    <div className="mt-4 h-[240px] sm:h-[320px] lg:h-[360px]">
                        <Line data={data} options={options} />
                    </div>
                </div>

                <div className="ui-card p-4 sm:p-6">
                    <div className="flex items-center justify-between gap-3 border-b border-[var(--border-line)] pb-4">
                        <div>
                            <h2 className="font-heading text-lg font-semibold sm:text-xl">Absensi Terbaru</h2>
                            <p className="mt-1 text-xs text-[var(--text-muted)]">Menampilkan hingga 12 aktivitas terbaru.</p>
                        </div>
                        <span className="ui-badge bg-[var(--bg-subtle)] text-[var(--text-muted)]">Live Feed</span>
                    </div>

                    {recentAttendances?.length ? (
                        <div className="mt-3 max-h-[460px] divide-y divide-[var(--border-line)] overflow-y-auto pr-1">
                            {recentAttendances.slice(0, 12).map((attendance) => (
                                <div key={attendance.id} className="flex items-center justify-between gap-3 py-3">
                                    <div className="min-w-0">
                                        <div className="truncate font-semibold text-[var(--text-main)]">{attendance.user?.name || "Karyawan"}</div>
                                        <div className="mt-1 text-sm text-[var(--text-muted)]">
                                            {attendance.type === "in" ? "Check-in" : "Check-out"}{" "}
                                            {new Date(attendance.created_at).toLocaleString("id-ID", {
                                                day: "2-digit",
                                                month: "short",
                                                hour: "2-digit",
                                                minute: "2-digit",
                                            })}
                                        </div>
                                    </div>
                                    <span
                                        className={`ui-badge shrink-0 ${
                                            attendance.type === "in"
                                                ? "bg-emerald-50 text-[var(--success-color)]"
                                                : "bg-amber-50 text-[var(--warning-color)]"
                                        }`}
                                    >
                                        {attendance.type === "in" ? "Masuk" : "Pulang"}
                                    </span>
                                </div>
                            ))}
                        </div>
                    ) : (
                        <div className="mt-5 rounded-lg border border-dashed border-[var(--border-strong)] bg-[var(--bg-subtle)] p-8 text-center text-sm text-[var(--text-muted)]">
                            Belum ada data absensi terbaru untuk ditampilkan.
                        </div>
                    )}
                </div>
            </section>
        </AuthenticatedLayout>
    );
}
