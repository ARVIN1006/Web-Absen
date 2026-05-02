import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout";
import { Head, Link } from "@inertiajs/react";

export default function PayrollShow({ payroll }) {
    const formatCurrency = (amount) =>
        new Intl.NumberFormat("id-ID", { style: "currency", currency: "IDR", minimumFractionDigits: 0 }).format(Number(amount || 0));

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
        "Juli", "Agustus", "September", "Oktober", "November", "Desember",
    ];
    const earnings = (payroll.component_items || []).filter((item) => item.payroll_component?.type === "earning");
    const deductions = (payroll.component_items || []).filter((item) => item.payroll_component?.type === "deduction");
    const statusText = {
        draft: "DRAFT",
        reviewed: "DIREVIEW",
        paid: "LUNAS",
    };

    return (
        <AuthenticatedLayout>
            <Head title={`Slip Gaji ${monthNames[payroll.month - 1]} ${payroll.year}`} />

            <div className="mx-auto max-w-3xl p-6">
                <div className="mb-6">
                    <Link href={route("payrolls.user_index")} className="flex items-center gap-2 text-[var(--text-muted)] transition-colors hover:text-[var(--text-main)]">
                        <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M15 19l-7-7 7-7" />
                        </svg>
                        Kembali ke Daftar Payroll
                    </Link>
                </div>

                <div className="glass rounded-3xl border border-[var(--border-glass)] p-8">
                    <div className="mb-8 border-b border-[var(--border-glass)] pb-6 text-center">
                        <h1 className="mb-1 text-2xl font-bold text-[var(--text-main)]">SLIP GAJI</h1>
                        <p className="text-[var(--text-muted)]">
                            Periode: {monthNames[payroll.month - 1]} {payroll.year}
                        </p>
                    </div>

                    <div className="mb-8 grid grid-cols-2 gap-4 rounded-2xl bg-black/5 p-4">
                        <div>
                            <div className="text-xs uppercase text-[var(--text-muted)]">Nama</div>
                            <div className="font-semibold text-[var(--text-main)]">{payroll.user?.name}</div>
                        </div>
                        <div>
                            <div className="text-xs uppercase text-[var(--text-muted)]">Jabatan</div>
                            <div className="font-semibold text-[var(--text-main)]">{payroll.user?.position?.name || "-"}</div>
                        </div>
                    </div>

                    <div className="mb-6">
                        <h3 className="mb-4 text-sm font-bold uppercase text-[var(--text-muted)]">Penghasilan</h3>
                        <div className="space-y-3">
                            {earnings.map((item) => (
                                <div key={item.id} className="flex justify-between py-2">
                                    <span className="text-[var(--text-main)]">{item.payroll_component?.name}</span>
                                    <span className="font-semibold text-[var(--text-main)]">+{formatCurrency(item.amount)}</span>
                                </div>
                            ))}
                            <div className="flex justify-between border-t border-[var(--border-glass)] py-2">
                                <span className="text-[var(--text-main)]">Total Penghasilan</span>
                                <span className="font-semibold">{formatCurrency(payroll.total_earnings || payroll.net_salary)}</span>
                            </div>
                            {deductions.map((item) => (
                                <div key={item.id} className="flex justify-between py-2">
                                    <span className="text-[var(--text-main)]">{item.payroll_component?.name}</span>
                                    <span className="font-semibold text-red-500">-{formatCurrency(item.amount)}</span>
                                </div>
                            ))}
                            <div className="flex justify-between py-2">
                                <span className="text-[var(--text-main)]">Total Potongan</span>
                                <span className="font-semibold text-red-500">-{formatCurrency(payroll.total_deductions || payroll.deductions || 0)}</span>
                            </div>
                        </div>
                    </div>

                    <div className="flex items-center justify-between border-t border-[var(--border-glass)] py-4">
                        <span className="text-lg font-bold text-[var(--text-main)]">TOTAL GAJI BERSIH</span>
                        <span className="text-2xl font-black text-blue-500">{formatCurrency(payroll.net_salary)}</span>
                    </div>

                    <div className="mt-6 rounded-2xl bg-black/5 p-4">
                        <div className="flex items-center justify-between">
                            <div>
                                <div className="text-xs uppercase text-[var(--text-muted)]">Status Pembayaran</div>
                                <div className={`font-bold ${payroll.status === "paid" ? "text-green-500" : payroll.status === "reviewed" ? "text-blue-500" : "text-yellow-500"}`}>
                                    {statusText[payroll.status] || payroll.status}
                                </div>
                            </div>
                            {payroll.paid_at && (
                                <div className="text-right">
                                    <div className="text-xs uppercase text-[var(--text-muted)]">Dibayar Pada</div>
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
