import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout";
import { Head } from "@inertiajs/react";
import { useState } from "react";

export default function LaborCost({ monthlyCosts, year }) {
    const [filterYear, setFilterYear] = useState(year);

    const formatCurrency = (amount) => {
        return new Intl.NumberFormat("id-ID", { style: "currency", currency: "IDR", minimumFractionDigits: 0 }).format(amount);
    };

    const monthNames = [
        "Januari", "Februari", "Maret", "April", "Mei", "Juni",
        "Juli", "Agustus", "September", "Oktober", "November", "Desember"
    ];

    const totalBasic = monthlyCosts.reduce((sum, item) => sum + parseFloat(item.total_basic || 0), 0);
    const totalOvertime = monthlyCosts.reduce((sum, item) => sum + parseFloat(item.total_overtime || 0), 0);
    const totalNet = monthlyCosts.reduce((sum, item) => sum + parseFloat(item.total_net || 0), 0);

    return (
        <AuthenticatedLayout>
            <Head title="Laporan Labor Cost" />

            <div className="max-w-[1200px] mx-auto p-6">
                {/* Header */}
                <div className="glass rounded-3xl p-6 mb-6 flex flex-col md:flex-row justify-between items-start md:items-center gap-4 border border-[var(--border-glass)] backdrop-blur-xl">
                    <div>
                        <h1 className="text-2xl font-bold text-[var(--text-main)]">Laporan Labor Cost</h1>
                        <p className="text-[var(--text-muted)] mt-1">Analisis biaya tenaga kerja per bulan</p>
                    </div>
                    <form className="flex gap-3">
                        <input
                            type="number"
                            value={filterYear}
                            onChange={(e) => setFilterYear(e.target.value)}
                            className="px-4 py-2 rounded-xl bg-black/5 border border-[var(--border-glass)] focus:border-blue-500 outline-none w-28"
                            min="2020"
                        />
                        <button
                            type="submit"
                            className="bg-blue-500 text-white px-5 py-2 rounded-xl font-semibold hover:opacity-80 transition-all"
                        >
                            Filter
                        </button>
                    </form>
                </div>

                {/* Summary Cards */}
                <div className="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                    <div className="glass rounded-2xl p-6">
                        <div className="text-sm text-[var(--text-muted)] mb-1">Total Gaji Pokok</div>
                        <div className="text-2xl font-bold text-[var(--text-main)]">{formatCurrency(totalBasic)}</div>
                    </div>
                    <div className="glass rounded-2xl p-6">
                        <div className="text-sm text-[var(--text-muted)] mb-1">Total Lembur</div>
                        <div className="text-2xl font-bold text-blue-500">+{formatCurrency(totalOvertime)}</div>
                    </div>
                    <div className="glass rounded-2xl p-6">
                        <div className="text-sm text-[var(--text-muted)] mb-1">Total Net</div>
                        <div className="text-2xl font-bold text-green-500">{formatCurrency(totalNet)}</div>
                    </div>
                </div>

                {/* Table */}
                <div className="glass rounded-2xl overflow-hidden">
                    <div className="overflow-x-auto">
                        <table className="w-full">
                            <thead>
                                <tr className="bg-black/5 border-b border-[var(--border-glass)]">
                                    <th className="px-6 py-4 text-left text-xs font-bold text-[var(--text-muted-dark)] uppercase">
                                        Bulan
                                    </th>
                                    <th className="px-6 py-4 text-right text-xs font-bold text-[var(--text-muted-dark)] uppercase">
                                        Gaji Pokok
                                    </th>
                                    <th className="px-6 py-4 text-right text-xs font-bold text-[var(--text-muted-dark)] uppercase">
                                        Lembur
                                    </th>
                                    <th className="px-6 py-4 text-right text-xs font-bold text-[var(--text-muted-dark)] uppercase">
                                        Total Net
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                {monthlyCosts.length === 0 ? (
                                    <tr>
                                        <td colSpan="4" className="text-center py-12 text-[var(--text-muted)]">
                                            Belum ada data payroll untuk tahun ini.
                                        </td>
                                    </tr>
                                ) : (
                                    monthlyCosts.map((item) => (
                                        <tr
                                            key={item.month}
                                            className="border-b border-[var(--border-glass)] hover:bg-[var(--hover-bg)] transition-colors"
                                        >
                                            <td className="px-6 py-4 font-semibold text-[var(--text-main)]">
                                                {monthNames[item.month - 1]}
                                            </td>
                                            <td className="px-6 py-4 text-right">
                                                {formatCurrency(item.total_basic)}
                                            </td>
                                            <td className="px-6 py-4 text-right text-blue-500">
                                                +{formatCurrency(item.total_overtime)}
                                            </td>
                                            <td className="px-6 py-4 text-right font-bold text-[var(--text-main)]">
                                                {formatCurrency(item.total_net)}
                                            </td>
                                        </tr>
                                    ))
                                )}
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}
