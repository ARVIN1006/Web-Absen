import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout";
import { Head, Link, useForm } from "@inertiajs/react";
import { useMemo, useState } from "react";

function StatCard({ label, value, subtitle }) {
    return (
        <div className="ui-card p-5">
            <div className="ui-section-title">{label}</div>
            <div className="mt-3 font-heading text-3xl font-bold text-black">{value}</div>
            <div className="mt-2 text-sm text-[var(--text-muted)]">{subtitle}</div>
        </div>
    );
}

export default function Employees({ employees, flash }) {
    const [search, setSearch] = useState("");
    const { delete: destroy } = useForm();

    const filteredEmployees = useMemo(() => {
        const keyword = search.toLowerCase();

        return employees.filter((employee) =>
            [
                employee.name,
                employee.email,
                employee.profile?.employee_code,
                employee.department?.name,
                employee.position?.name,
                employee.branch?.name,
            ]
                .filter(Boolean)
                .some((value) => value.toLowerCase().includes(keyword))
        );
    }, [employees, search]);

    const activeCount = employees.filter((employee) => employee.is_active).length;
    const verifiedCount = employees.filter((employee) => employee.profile?.face_reference_path).length;
    const branchCount = new Set(employees.map((employee) => employee.branch?.name).filter(Boolean)).size;

    const handleDelete = (id) => {
        if (confirm("Hapus data karyawan ini?")) {
            destroy(route("admin.employees.destroy", id));
        }
    };

    return (
        <AuthenticatedLayout>
            <Head title="Master Karyawan" />

            <section className="ui-card overflow-hidden">
                <div className="flex flex-col gap-5 p-6 lg:flex-row lg:items-end lg:justify-between lg:p-8">
                    <div>
                        <div className="ui-section-title">Employee Master</div>
                        <h1 className="font-heading mt-3 text-3xl font-bold text-black">Data induk karyawan HRIS</h1>
                        <p className="mt-3 max-w-2xl text-sm leading-6 text-[var(--text-muted)]">
                            Halaman ini sekarang menampilkan employee code, penempatan organisasi, employment type, status aktif, dan data administratif inti dari skema HRIS baru.
                        </p>
                    </div>

                    <Link href={route("admin.employees.create")} className="ui-button-primary">
                        Tambah Karyawan
                    </Link>
                </div>
            </section>

            <section className="mt-6 grid gap-5 md:grid-cols-3">
                <StatCard label="Total Karyawan" value={employees.length} subtitle="Seluruh user dengan role employee" />
                <StatCard label="Karyawan Aktif" value={activeCount} subtitle="Status aktif pada sistem" />
                <StatCard label="Cabang Aktif" value={branchCount} subtitle={`Wajah terverifikasi: ${verifiedCount}`} />
            </section>

            {flash?.success && (
                <div className="mt-6 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-700">
                    {flash.success}
                </div>
            )}

            <section className="ui-card mt-6 overflow-hidden">
                <div className="flex flex-col gap-4 border-b border-[var(--border-line)] p-6 lg:flex-row lg:items-center lg:justify-between">
                    <div>
                        <h2 className="font-heading text-xl font-semibold">Daftar Karyawan</h2>
                        <p className="mt-1 text-sm text-[var(--text-muted)]">Data master yang siap dipakai untuk attendance, cuti, payroll, dan reporting.</p>
                    </div>
                    <input
                        type="text"
                        value={search}
                        onChange={(event) => setSearch(event.target.value)}
                        placeholder="Cari nama, email, kode, departemen..."
                        className="ui-input max-w-md"
                    />
                </div>

                <div className="overflow-x-auto">
                    <table className="min-w-full divide-y divide-[var(--border-line)] text-sm">
                        <thead className="bg-[var(--bg-subtle)] text-left text-[11px] font-semibold uppercase tracking-[0.14em] text-[var(--text-soft)]">
                            <tr>
                                <th className="px-6 py-4">Karyawan</th>
                                <th className="px-6 py-4">Organisasi</th>
                                <th className="px-6 py-4">Status</th>
                                <th className="px-6 py-4">Kontak</th>
                                <th className="px-6 py-4 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody className="divide-y divide-[var(--border-line)]">
                            {filteredEmployees.length === 0 ? (
                                <tr>
                                    <td colSpan="5" className="px-6 py-16 text-center text-[var(--text-muted)]">
                                        Tidak ada data karyawan yang cocok dengan pencarian.
                                    </td>
                                </tr>
                            ) : (
                                filteredEmployees.map((employee) => (
                                    <tr key={employee.id} className="align-top hover:bg-slate-50/80">
                                        <td className="px-6 py-4">
                                            <div className="font-semibold text-[var(--text-main)]">{employee.name}</div>
                                            <div className="mt-1 text-xs text-[var(--text-muted)]">{employee.profile?.employee_code || "Tanpa kode"} • {employee.email}</div>
                                            <div className="mt-2 flex flex-wrap gap-2">
                                                <span className={`ui-badge ${employee.is_active ? "bg-emerald-50 text-[var(--success-color)]" : "bg-slate-100 text-slate-500"}`}>
                                                    {employee.is_active ? "Aktif" : "Nonaktif"}
                                                </span>
                                                <span className={`ui-badge ${employee.profile?.face_reference_path ? "bg-indigo-50 text-[var(--primary-color)]" : "bg-amber-50 text-[var(--warning-color)]"}`}>
                                                    {employee.profile?.face_reference_path ? "Wajah terdaftar" : "Wajah belum ada"}
                                                </span>
                                            </div>
                                        </td>
                                        <td className="px-6 py-4 text-[var(--text-muted)]">
                                            <div><span className="font-medium text-[var(--text-main)]">Jabatan:</span> {employee.position?.name || "-"}</div>
                                            <div className="mt-1"><span className="font-medium text-[var(--text-main)]">Departemen:</span> {employee.department?.name || "-"}</div>
                                            <div className="mt-1"><span className="font-medium text-[var(--text-main)]">Cabang:</span> {employee.branch?.name || "-"}</div>
                                            <div className="mt-1"><span className="font-medium text-[var(--text-main)]">Employment:</span> {employee.employment_type?.name || "-"}</div>
                                            <div className="mt-1"><span className="font-medium text-[var(--text-main)]">Atasan:</span> {employee.manager?.name || "-"}</div>
                                        </td>
                                        <td className="px-6 py-4 text-[var(--text-muted)]">
                                            <div><span className="font-medium text-[var(--text-main)]">Status kerja:</span> {employee.profile?.employment_status || "-"}</div>
                                            <div className="mt-1"><span className="font-medium text-[var(--text-main)]">Masuk:</span> {employee.joined_at || "-"}</div>
                                            <div className="mt-1"><span className="font-medium text-[var(--text-main)]">Kontrak selesai:</span> {employee.profile?.contract_end_at || "-"}</div>
                                            <div className="mt-1"><span className="font-medium text-[var(--text-main)]">NIK:</span> {employee.profile?.identity_number || "-"}</div>
                                        </td>
                                        <td className="px-6 py-4 text-[var(--text-muted)]">
                                            <div>{employee.phone_number || "-"}</div>
                                            <div className="mt-1">{employee.profile?.personal_email || employee.email}</div>
                                        </td>
                                        <td className="px-6 py-4">
                                            <div className="flex justify-end gap-2">
                                                <Link href={route("admin.employees.show", employee.id)} className="ui-button-secondary px-3 py-2">
                                                    Detail
                                                </Link>
                                                <Link href={route("admin.employees.edit", employee.id)} className="ui-button-secondary px-3 py-2">
                                                    Edit
                                                </Link>
                                                <button type="button" onClick={() => handleDelete(employee.id)} className="rounded-md border border-red-200 px-3 py-2 text-sm font-semibold text-red-600 transition hover:bg-red-50">
                                                    Hapus
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                ))
                            )}
                        </tbody>
                    </table>
                </div>
            </section>
        </AuthenticatedLayout>
    );
}
