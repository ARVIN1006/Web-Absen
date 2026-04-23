import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout";
import { Head } from "@inertiajs/react";

export default function PerformanceHeatmap({ heatData }) {
    const getStatusStyles = (avg) => {
        if (avg >= 80) return {
            color: "text-green-500",
            bg: "bg-green-500",
            bgLight: "bg-green-500/10",
            border: "border-green-500",
            label: "Baik"
        };
        if (avg >= 60) return {
            color: "text-yellow-500",
            bg: "bg-yellow-500",
            bgLight: "bg-yellow-500/10",
            border: "border-yellow-500",
            label: "Cukup"
        };
        return {
            color: "text-red-500",
            bg: "bg-red-500",
            bgLight: "bg-red-500/10",
            border: "border-red-500",
            label: "Perlu Perbaikan"
        };
    };

    return (
        <AuthenticatedLayout>
            <Head title="Performance Heatmap" />

            <div className="max-w-[1200px] mx-auto p-6">
                {/* Header */}
                <div className="glass rounded-3xl p-6 mb-6 border border-[var(--border-glass)] backdrop-blur-xl">
                    <h1 className="text-2xl font-bold text-[var(--text-main)]">Performance Heatmap</h1>
                    <p className="text-[var(--text-muted)] mt-1">Visualisasi performa karyawan per departemen</p>
                </div>

                {/* Legend */}
                <div className="flex gap-6 mb-6 overflow-x-auto pb-2">
                    <div className="flex items-center gap-2 whitespace-nowrap">
                        <div className="w-4 h-4 rounded bg-green-500" />
                        <span className="text-sm text-[var(--text-muted)]">Baik (≥80)</span>
                    </div>
                    <div className="flex items-center gap-2 whitespace-nowrap">
                        <div className="w-4 h-4 rounded bg-yellow-500" />
                        <span className="text-sm text-[var(--text-muted)]">Cukup (60-79)</span>
                    </div>
                    <div className="flex items-center gap-2 whitespace-nowrap">
                        <div className="w-4 h-4 rounded bg-red-500" />
                        <span className="text-sm text-[var(--text-muted)]">Perlu Perbaikan (&lt;60)</span>
                    </div>
                </div>

                {/* Heatmap Grid */}
                <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    {heatData.length === 0 ? (
                        <div className="col-span-full text-center py-12 text-[var(--text-muted)]">
                            Belum ada data KPI untuk ditampilkan.
                        </div>
                    ) : (
                        heatData.map((item) => {
                            const styles = getStatusStyles(item.avg);
                            return (
                                <div
                                    key={item.dept}
                                    className={`glass rounded-2xl p-6 relative overflow-hidden border-l-4 ${styles.border}`}
                                >
                                    <div className="flex justify-between items-start mb-4">
                                        <h3 className="text-lg font-bold text-[var(--text-main)]">{item.dept}</h3>
                                        <span className={`px-3 py-1 rounded-lg text-xs font-bold ${styles.bgLight} ${styles.color}`}>
                                            {styles.label}
                                        </span>
                                    </div>

                                    <div className="flex items-end gap-2 mb-2">
                                        <span className={`text-4xl font-black ${styles.color}`}>
                                            {Math.round(item.avg)}
                                        </span>
                                        <span className="text-sm text-[var(--text-muted)] mb-1">/ 100</span>
                                    </div>

                                    <div className="text-sm text-[var(--text-muted)]">
                                        Berdasarkan {item.count} karyawan
                                    </div>

                                    {/* Progress bar */}
                                    <div className="mt-4 h-2 bg-black/10 rounded-full overflow-hidden">
                                        <div
                                            className={`h-full rounded-full transition-all ${styles.bg}`}
                                            style={{ width: `${Math.min(item.avg, 100)}%` }}
                                        />
                                    </div>
                                </div>
                            );
                        })
                    )}
                </div>
            </div>
        </AuthenticatedLayout>
    );
}

