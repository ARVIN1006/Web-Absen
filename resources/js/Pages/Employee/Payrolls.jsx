import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout";
import { Head, Link } from "@inertiajs/react";

export default function Payrolls({ payrolls }) {
    const formatCurrency = (amount) => {
        return new Intl.NumberFormat("id-ID", { style: "currency", currency: "IDR", minimumFractionDigits: 0 }).format(amount);
    };

    const monthNames = [
        "Januari", "Februari", "Maret", "April", "Mei", "Juni",
        "Juli", "Agustus", "September", "Oktober", "November", "Desember"
    ];

    return (
        <AuthenticatedLayout>
            <Head title="Payroll Saya" />

            <div className="max-w-[1200px] mx-auto p-6">
                {/* Header */}
                <div className="glass rounded-3xl p-6 mb-6 border border-[var(--border-glass)] backdrop-blur-xl">
                    <h1 className="text-2xl font-bold text-[var(--text-main)]">Payroll Saya</h1>
                    <p className="text-[var(--text-muted)] mt-1">Riwayat gaji dan slip gaji Anda.</p>
                </div>

                {/* Payroll List */}
                <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    {payrolls.length === 0 ? (
                        <div className="col-span-full text-center py-12 text-[var(--text-muted)]">
                            Belum ada data payroll.
                        </div>
                    ) : (
                        payrolls.map((payroll) => (
                            <div key={payroll.id} className="glass rounded-2xl p-6 hover:-translate-y-1 hover:border-blue-500/50 transition-all">
                                <div className="flex justify-between items-start mb-4">
                                    <div>
                                        <div className="text-lg font-bold text-[var(--text-main)]">
                                            {monthNames[payroll.month - 1]} {payroll.year}
                                        </div>
                                        <div className="text-sm text-[var(--text-muted)]">
                                            {payroll.status === "paid" ? "Lunas" : "Draft"}
                                        </div>
                                    </div>
                                    <span
                                        className={`px-3 py-1 rounded-lg text-xs font-bold ${
                                            payroll.status === "paid"
                                                ? "bg-green-500/10 text-green-500"
                                                : "bg-yellow-500/10 text-yellow-500"
                                        }`}
                                    >
                                        {payroll.status === "paid" ? "LUNAS" : "DRAFT"}
                                    </span>
                                </div>

                                <div className="space-y-2 text-sm mb-4">
                                    <div className="flex justify-between">
                                        <span className="text-[var(--text-muted)]">Gaji Pokok</span>
                                        <span className="font-semibold">{formatCurrency(payroll.basic_salary)}</span>
                                    </div>
                                    <div className="flex justify-between">
                                        <span className="text-[var(--text-muted)]">Lembur</span>
                                        <span className="font-semibold text-blue-500">
                                            +{formatCurrency(payroll.overtime_pay)}
                                        </span>
                                    </div>
                                    <div className="flex justify-between pt-2 border-t border-[var(--border-glass)]">
                                        <span className="text-[var(--text-muted)]">Earnings</span>
                                        <span className="font-semibold">{formatCurrency(payroll.total_earnings || payroll.net_salary)}</span>
                                    </div>
                                    <div className="flex justify-between">
                                        <span className="text-[var(--text-muted)]">Potongan</span>
                                        <span className="font-semibold text-red-500">-{formatCurrency(payroll.total_deductions || payroll.deductions || 0)}</span>
                                    </div>
                                    <div className="flex justify-between pt-2 border-t border-[var(--border-glass)]">
                                        <span className="font-bold text-[var(--text-main)]">Total</span>
                                        <span className="font-bold text-[var(--text-main)]">
                                            {formatCurrency(payroll.net_salary)}
                                        </span>
                                    </div>
                                </div>

                                <Link
                                    href={route("payrolls.show", payroll.id)}
                                    className="block w-full text-center bg-blue-500/10 text-blue-500 py-2 rounded-xl font-semibold hover:bg-blue-500 hover:text-white transition-all"
                                >
                                    Lihat Detail
                                </Link>
                            </div>
                        ))
                    )}
                </div>
            </div>
        </AuthenticatedLayout>
    );
}
