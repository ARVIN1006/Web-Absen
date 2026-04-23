import { Head } from "@inertiajs/react";
import { Link } from "@inertiajs/react";

export default function Guide() {
    return (
        <>
            <Head title="Panduan Demo" />
            
            <div className="min-h-screen bg-[#0f172a] text-[#f8fafc] p-5">
                <div className="max-w-4xl mx-auto py-12">
                    {/* Header */}
                    <div className="text-center mb-12">
                        <h1 className="text-4xl md:text-5xl font-extrabold mb-4">
                            Panduan Penggunaan
                        </h1>
                        <p className="text-[#94a3b8] text-lg">
                            Pelajari cara menggunakan sistem absensi dengan fitur pengenalan wajah
                        </p>
                        <Link
                            href={route("login")}
                            className="inline-block mt-6 bg-blue-500 hover:bg-blue-600 text-white px-8 py-3 rounded-xl font-bold transition-all"
                        >
                            ← Kembali ke Login
                        </Link>
                    </div>

                    {/* Content */}
                    <div className="space-y-6">
                        {/* Step 1 */}
                        <div className="bg-[rgba(30,41,59,0.7)] border border-[rgba(255,255,255,0.1)] rounded-3xl p-8">
                            <div className="flex items-start gap-4">
                                <div className="w-12 h-12 rounded-xl bg-blue-500/20 text-blue-500 flex items-center justify-center text-xl font-bold flex-shrink-0">
                                    1
                                </div>
                                <div>
                                    <h3 className="text-xl font-bold mb-2">Login ke Sistem</h3>
                                    <p className="text-[#94a3b8]">
                                        Masukkan email dan password Anda. Sistem akan mengarahkan Anda ke dashboard sesuai role (Admin atau Karyawan).
                                    </p>
                                </div>
                            </div>
                        </div>

                        {/* Step 2 */}
                        <div className="bg-[rgba(30,41,59,0.7)] border border-[rgba(255,255,255,0.1)] rounded-3xl p-8">
                            <div className="flex items-start gap-4">
                                <div className="w-12 h-12 rounded-xl bg-green-500/20 text-green-500 flex items-center justify-center text-xl font-bold flex-shrink-0">
                                    2
                                </div>
                                <div>
                                    <h3 className="text-xl font-bold mb-2">Lakukan Absensi</h3>
                                    <p className="text-[#94a3b8] mb-3">
                                        Klik menu <strong>Absensi</strong> dan ikuti langkah:
                                    </p>
                                    <ul className="list-disc list-inside text-[#94a3b8] space-y-1 ml-4">
                                        <li>Izinkan akses lokasi dan kamera</li>
                                        <li>Pastikan berada dalam radius kantor</li>
                                        <li>Ambil foto wajah Anda</li>
                                        <li>Konfirmasi absensi</li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        {/* Step 3 */}
                        <div className="bg-[rgba(30,41,59,0.7)] border border-[rgba(255,255,255,0.1)] rounded-3xl p-8">
                            <div className="flex items-start gap-4">
                                <div className="w-12 h-12 rounded-xl bg-purple-500/20 text-purple-500 flex items-center justify-center text-xl font-bold flex-shrink-0">
                                    3
                                </div>
                                <div>
                                    <h3 className="text-xl font-bold mb-2">Ajukan Cuti & Reimbursement</h3>
                                    <p className="text-[#94a3b8]">
                                        Gunakan menu <strong>Pengajuan Cuti</strong> untuk mengajukan izin tidak masuk, dan <strong>Reimbursement</strong> untuk klaim pengeluaran bisnis.
                                    </p>
                                </div>
                            </div>
                        </div>

                        {/* Step 4 */}
                        <div className="bg-[rgba(30,41,59,0.7)] border border-[rgba(255,255,255,0.1)] rounded-3xl p-8">
                            <div className="flex items-start gap-4">
                                <div className="w-12 h-12 rounded-xl bg-orange-500/20 text-orange-500 flex items-center justify-center text-xl font-bold flex-shrink-0">
                                    4
                                </div>
                                <div>
                                    <h3 className="text-xl font-bold mb-2">Lihat Slip Gaji</h3>
                                    <p className="text-[#94a3b8]">
                                        Akses menu <strong>Payroll Saya</strong> untuk melihat riwayat gaji dan mengunduh slip gaji.
                                    </p>
                                </div>
                            </div>
                        </div>

                        {/* Tips */}
                        <div className="bg-blue-500/10 border border-blue-500/30 rounded-3xl p-8 mt-8">
                            <h3 className="text-xl font-bold mb-4 text-blue-500">💡 Tips Penting</h3>
                            <ul className="space-y-2 text-[#94a3b8]">
                                <li>• Pastikan wajah terlihat jelas saat absensi</li>
                                <li>• Aktifkan GPS untuk validasi lokasi</li>
                                <li>• Simpan lampiran dengan format yang benar</li>
                                <li>• Periksa notifikasi untuk status pengajuan</li>
                            </ul>
                        </div>
                    </div>

                    {/* Footer */}
                    <div className="text-center mt-12 pt-8 border-t border-[rgba(255,255,255,0.1)]">
                        <p className="text-[#64748b] text-sm">
                            Butuh bantuan? Hubungi tim HR atau admin sistem.
                        </p>
                    </div>
                </div>
            </div>
        </>
    );
}
