import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout";
import { Head, Link } from "@inertiajs/react";

export default function PayrollShow({ payroll }) {
    const formatCurrency = (amount) => {
        return new Intl.NumberFormat("id-ID", { style: "currency", currency: "IDR", minimumFractionDigits: 0 }).format(amount);
    };

    const formatDate = (date) => {
        if (!date) return "-";
        return new Date(date).toLocaleDateString("id-ID", {
            day: "numeric",
            month: "long",
            year: "numeric",
        });
    };

    const monthNames = [
        "Januari", "Februari", "Maret", "April", "Mei", "Juni",
        "Juli", "Agustus", "September", "Oktober", "November", "Desember"
    ];

    return (
        <AuthenticatedLayout>
            <Head title={`Slip Gaji ${monthNames[payroll.month - 1]} ${payroll.year}`} />

            <div className="max-w-3xl mx-auto p-6">
                {/* Back Button */}
                <div className="mb-6">
                    <Link
                        href={route("payrolls.user_index")}
                        className="text-[var(--text-muted)] hover:text-[var(--text-main)] flex items-center gap-2 transition-colors"
                    >
                        <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M15 19l-7-7 7-7" />
                        </svg>
                        Kembali ke Daftar Payroll
                    </Link>
                </div>

                {/* Slip Gaji */}
                <div className="glass rounded-3xl p-8 border border-[var(--border-glass)]">
                    {/* Header */}
                    <div className="text-center mb-8 pb-6 border-b border-[var(--border-glass)]">
                        <h1 className="text-2xl font-bold text-[var(--text-main)] mb-1">SLIP GAJI</h1>
                        <p className="text-[var(--text-muted)]">
                            Periode: {monthNames[payroll.month - 1]} {payroll.year}
                        </p>
                    </div>

                    {/* Employee Info */}
                    <div className="grid grid-cols-2 gap-4 mb-8 p-4 bg-black/5 rounded-2xl">
                        <div>
                            <div className="text-xs text-[var(--text-muted)] uppercase">Nama</div>
                            <div className="font-semibold text-[var(--text-main)]">{payroll.user?.name}</div>
                        </div>
                        <div>
                            <div className="text-xs text-[var(--text-muted)] uppercase">Jabatan</div>
                            <div className="font-semibold text-[var(--text-main)]">{payroll.user?.position?.name || "-"}</div>
                        </div>
                    </div>

                    {/* Earnings */}
                    <div className="mb-6">
                        <h3 className="text-sm font-bold text-[var(--text-muted)] uppercase mb-4">PENGHASILAN</h3>
                        <div className="space-y-3">
                            <div className="flex justify-between py-2">
                                <span className="text-[var(--text-main)]">Gaji Pokok</span>
                                <span className="font-semibold">{formatCurrency(payroll.basic_salary)}</span>
                            </div>
                            <div className="flex justify-between py-2">
                                <span className="text-[var(--text-main)]">Tunjangan Lembur</span>
                                <span className="font-semibold text-blue-500">+{formatCurrency(payroll.overtime_pay)}</span>
                            </div>
                            {payroll.component_items?.map((item) => (
                                <div key={item.id} className="flex justify-between py-2">
                                    <span className="text-[var(--text-main)]">{item.payroll_component?.name}</span>
                                    <span className={`font-semibold ${item.payroll_component?.type === "deduction" ? "text-red-500" : "text-[var(--text-main)]"}`}>
                                        {item.payroll_component?.type === "deduction" ? "-" : "+"}{formatCurrency(item.amount)}
                                    </span>
                                </div>
                            ))}
                            <div className="flex justify-between py-2 border-t border-[var(--border-glass)]">
                                <span className="text-[var(--text-main)]">Total Penghasilan</span>
                                <span className="font-semibold">{formatCurrency(payroll.total_earnings || payroll.net_salary)}</span>
                            </div>
                            <div className="flex justify-between py-2">
                                <span className="text-[var(--text-main)]">Total Potongan</span>
                                <span className="font-semibold text-red-500">-{formatCurrency(payroll.total_deductions || payroll.deductions || 0)}</span>
                            </div>
                        </div>
                    </div>

                    {/* Total */}
                    <div className="flex justify-between items-center py-4 border-t border-[var(--border-glass)]">
                        <span className="text-lg font-bold text-[var(--text-main)]">TOTAL GAJI BERSIH</span>
                        <span className="text-2xl font-black text-blue-500">{formatCurrency(payroll.net_salary)}</span>
                    </div>

                    {/* Status */}
                    <div className="mt-6 p-4 bg-black/5 rounded-2xl">
                        <div className="flex justify-between items-center">
                            <div>
                                <div className="text-xs text-[var(--text-muted)] uppercase">Status Pembayaran</div>
                                <div
                                    className={`font-bold ${
                                        payroll.status === "paid" ? "text-green-500" : "text-yellow-500"
                                    }`}
                                >
                                    {payroll.status === "paid" ? "✅ LUNAS" : "⏳ BELUM DIBAYAR"}
                                </div>
                            </div>
                            {payroll.paid_at && (
                                <div className="text-right">
                                    <div className="text-xs text-[var(--text-muted)] uppercase">Dibayar Pada</div>
                                    <div className="font-semibold text-[var(--text-main)]">{formatDate(payroll.paid_at)}</div>
                                </div>
                            )}
                        </div>
                    </div>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}
