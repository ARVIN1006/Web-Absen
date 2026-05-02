import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout";
import { Head, Link, useForm } from "@inertiajs/react";
import { useState } from "react";

function Section({ title, children }) {
    return (
        <section className="ui-card p-6">
            <div className="border-b border-[var(--border-line)] pb-4">
                <h2 className="font-heading text-xl font-semibold">{title}</h2>
            </div>
            <div className="mt-5">{children}</div>
        </section>
    );
}

function Item({ label, value }) {
    return (
        <div className="flex items-start justify-between gap-4 border-b border-dashed border-[var(--border-line)] py-2 last:border-b-0">
            <span className="text-sm text-[var(--text-muted)]">{label}</span>
            <span className="text-sm font-medium text-[var(--text-main)] text-right">{value || "-"}</span>
        </div>
    );
}

function Field({ label, error, children }) {
    return (
        <label className="block">
            <span className="mb-2 block text-sm font-semibold text-[var(--text-main)]">{label}</span>
            {children}
            {error && <span className="mt-1 block text-sm text-red-500">{error}</span>}
        </label>
    );
}

const dateValue = (value) => (value ? String(value).slice(0, 10) : "");
const dateTimeLocalValue = (value) => {
    if (!value) return "";
    const date = new Date(value);
    if (Number.isNaN(date.getTime())) return "";
    const offset = date.getTimezoneOffset() * 60000;
    return new Date(date.getTime() - offset).toISOString().slice(0, 16);
};

export default function Profile({ employee, workShift, todayAttendance, leaveBalances, attendanceCorrections, recentLeaveRequests, recentPayrolls }) {
    const [tab, setTab] = useState("profile");
    const profileForm = useForm({
        name: employee.name || "",
        phone_number: employee.profile?.phone_number || employee.phone_number || "",
        address: employee.profile?.current_address || employee.address || "",
        gender: employee.profile?.gender || employee.gender || "",
        birth_date: dateValue(employee.profile?.birth_date || employee.birth_date),
        place_of_birth: employee.profile?.place_of_birth || "",
        personal_email: employee.profile?.personal_email || "",
        alternate_phone_number: employee.profile?.alternate_phone_number || "",
        current_address: employee.profile?.current_address || employee.address || "",
        domicile_address: employee.profile?.domicile_address || "",
        marital_status: employee.profile?.marital_status || "",
        religion: employee.profile?.religion || "",
        nationality: employee.profile?.nationality || "",
        bank_name: employee.profile?.bank_name || "",
        bank_account_number: employee.profile?.bank_account_number || "",
        bank_account_name: employee.profile?.bank_account_name || "",
        password: "",
        password_confirmation: "",
    });
    const correctionForm = useForm({
        attendance_id: todayAttendance?.id || "",
        attendance_date: dateValue(todayAttendance?.attendance_date || new Date().toISOString()),
        requested_check_in_at: dateTimeLocalValue(todayAttendance?.check_in_at),
        requested_check_out_at: dateTimeLocalValue(todayAttendance?.check_out_at),
        reason: "",
        attachment: null,
    });

    return (
        <AuthenticatedLayout>
            <Head title="Profil Saya" />

            <section className="ui-card overflow-hidden">
                <div className="grid gap-6 p-6 lg:grid-cols-[1.3fr_0.8fr] lg:p-8">
                    <div>
                        <div className="ui-section-title">Employee Self Service</div>
                        <h1 className="font-heading mt-3 text-3xl font-bold text-black">{employee.name}</h1>
                        <p className="mt-2 text-sm text-[var(--text-muted)]">{employee.profile?.employee_code || "-"} • {employee.position?.name || "-"} • {employee.department?.name || "-"}</p>
                        <div className="mt-4 flex flex-wrap gap-2">
                            <span className="ui-badge bg-indigo-50 text-[var(--primary-color)]">{employee.branch?.name || "Tanpa cabang"}</span>
                            <span className="ui-badge bg-slate-100 text-slate-600">{employee.employment_type?.name || "Tanpa employment type"}</span>
                            <span className="ui-badge bg-emerald-50 text-[var(--success-color)]">{employee.profile?.employment_status || "active"}</span>
                        </div>
                    </div>
                    <div className="ui-card-muted p-5">
                        <div className="ui-section-title">Presensi Hari Ini</div>
                        <div className="mt-4 text-sm text-[var(--text-muted)]">
                            <div>Check-in: <span className="font-medium text-[var(--text-main)]">{todayAttendance?.check_in_at || "-"}</span></div>
                            <div className="mt-1">Check-out: <span className="font-medium text-[var(--text-main)]">{todayAttendance?.check_out_at || "-"}</span></div>
                            <div className="mt-1">Shift: <span className="font-medium text-[var(--text-main)]">{workShift?.name || "-"}</span></div>
                        </div>
                    </div>
                </div>
            </section>

            <div className="mt-6 flex gap-2 overflow-x-auto">
                {["profile", "leave", "attendance", "payroll"].map((value) => (
                    <button key={value} type="button" onClick={() => setTab(value)} className={`rounded-md px-4 py-2 text-sm font-semibold ${tab === value ? "bg-[var(--primary-color)] text-white" : "border border-[var(--border-line)] bg-white text-[var(--text-main)]"}`}>
                        {value}
                    </button>
                ))}
            </div>

            {tab === "profile" && (
                <div className="mt-6 space-y-6">
                    <Section title="Update Profil Pribadi">
                        <form
                            onSubmit={(e) => {
                                e.preventDefault();
                                profileForm.put(route("profile.update"), {
                                    preserveScroll: true,
                                    onSuccess: () => profileForm.setData({ ...profileForm.data, password: "", password_confirmation: "" }),
                                });
                            }}
                            className="space-y-6"
                        >
                            <div className="grid gap-4 md:grid-cols-2">
                                <Field label="Nama Lengkap" error={profileForm.errors.name}>
                                    <input value={profileForm.data.name} onChange={(e) => profileForm.setData("name", e.target.value)} className="ui-input" />
                                </Field>
                                <Field label="Email Kerja">
                                    <input value={employee.email} disabled className="ui-input cursor-not-allowed bg-slate-100 text-slate-500" />
                                </Field>
                                <Field label="Email Personal" error={profileForm.errors.personal_email}>
                                    <input type="email" value={profileForm.data.personal_email} onChange={(e) => profileForm.setData("personal_email", e.target.value)} className="ui-input" placeholder="nama@email.com" />
                                </Field>
                                <Field label="No. HP" error={profileForm.errors.phone_number}>
                                    <input value={profileForm.data.phone_number} onChange={(e) => profileForm.setData("phone_number", e.target.value)} className="ui-input" placeholder="08xxxxxxxxxx" />
                                </Field>
                                <Field label="No. HP Alternatif" error={profileForm.errors.alternate_phone_number}>
                                    <input value={profileForm.data.alternate_phone_number} onChange={(e) => profileForm.setData("alternate_phone_number", e.target.value)} className="ui-input" placeholder="Opsional" />
                                </Field>
                                <Field label="Jenis Kelamin" error={profileForm.errors.gender}>
                                    <select value={profileForm.data.gender} onChange={(e) => profileForm.setData("gender", e.target.value)} className="ui-input">
                                        <option value="">Pilih jenis kelamin</option>
                                        <option value="male">Laki-laki</option>
                                        <option value="female">Perempuan</option>
                                    </select>
                                </Field>
                                <Field label="Tempat Lahir" error={profileForm.errors.place_of_birth}>
                                    <input value={profileForm.data.place_of_birth} onChange={(e) => profileForm.setData("place_of_birth", e.target.value)} className="ui-input" />
                                </Field>
                                <Field label="Tanggal Lahir" error={profileForm.errors.birth_date}>
                                    <input type="date" value={profileForm.data.birth_date} onChange={(e) => profileForm.setData("birth_date", e.target.value)} className="ui-input" />
                                </Field>
                                <Field label="Status Pernikahan" error={profileForm.errors.marital_status}>
                                    <input value={profileForm.data.marital_status} onChange={(e) => profileForm.setData("marital_status", e.target.value)} className="ui-input" placeholder="single / married / lainnya" />
                                </Field>
                                <Field label="Agama" error={profileForm.errors.religion}>
                                    <input value={profileForm.data.religion} onChange={(e) => profileForm.setData("religion", e.target.value)} className="ui-input" />
                                </Field>
                                <Field label="Kewarganegaraan" error={profileForm.errors.nationality}>
                                    <input value={profileForm.data.nationality} onChange={(e) => profileForm.setData("nationality", e.target.value)} className="ui-input" placeholder="Indonesia" />
                                </Field>
                            </div>

                            <div className="grid gap-4 md:grid-cols-2">
                                <Field label="Alamat Saat Ini" error={profileForm.errors.current_address || profileForm.errors.address}>
                                    <textarea value={profileForm.data.current_address} onChange={(e) => { profileForm.setData("current_address", e.target.value); profileForm.setData("address", e.target.value); }} className="ui-input" rows="3" />
                                </Field>
                                <Field label="Alamat Domisili" error={profileForm.errors.domicile_address}>
                                    <textarea value={profileForm.data.domicile_address} onChange={(e) => profileForm.setData("domicile_address", e.target.value)} className="ui-input" rows="3" placeholder="Kosongkan jika sama dengan alamat saat ini" />
                                </Field>
                            </div>

                            <div className="rounded-2xl border border-[var(--border-line)] bg-slate-50 p-4">
                                <h3 className="font-heading text-base font-semibold text-[var(--text-main)]">Informasi Rekening</h3>
                                <p className="mt-1 text-sm text-[var(--text-muted)]">Opsional, isi jika diperlukan untuk data payroll.</p>
                                <div className="mt-4 grid gap-4 md:grid-cols-3">
                                    <Field label="Nama Bank" error={profileForm.errors.bank_name}>
                                        <input value={profileForm.data.bank_name} onChange={(e) => profileForm.setData("bank_name", e.target.value)} className="ui-input" />
                                    </Field>
                                    <Field label="Nomor Rekening" error={profileForm.errors.bank_account_number}>
                                        <input value={profileForm.data.bank_account_number} onChange={(e) => profileForm.setData("bank_account_number", e.target.value)} className="ui-input" />
                                    </Field>
                                    <Field label="Nama Pemilik Rekening" error={profileForm.errors.bank_account_name}>
                                        <input value={profileForm.data.bank_account_name} onChange={(e) => profileForm.setData("bank_account_name", e.target.value)} className="ui-input" />
                                    </Field>
                                </div>
                            </div>

                            <div className="rounded-2xl border border-[var(--border-line)] bg-white p-4">
                                <h3 className="font-heading text-base font-semibold text-[var(--text-main)]">Ubah Password</h3>
                                <p className="mt-1 text-sm text-[var(--text-muted)]">Kosongkan jika tidak ingin mengganti password.</p>
                                <div className="mt-4 grid gap-4 md:grid-cols-2">
                                    <Field label="Password Baru" error={profileForm.errors.password}>
                                        <input type="password" value={profileForm.data.password} onChange={(e) => profileForm.setData("password", e.target.value)} className="ui-input" />
                                    </Field>
                                    <Field label="Konfirmasi Password Baru">
                                        <input type="password" value={profileForm.data.password_confirmation} onChange={(e) => profileForm.setData("password_confirmation", e.target.value)} className="ui-input" />
                                    </Field>
                                </div>
                            </div>

                            <div className="flex flex-col gap-3 sm:flex-row sm:items-center">
                                <button type="submit" disabled={profileForm.processing} className="ui-button-primary">
                                    {profileForm.processing ? "Menyimpan..." : "Simpan Perubahan"}
                                </button>
                                <span className="text-sm text-[var(--text-muted)]">Data pekerjaan seperti departemen, jabatan, dan shift tetap dikelola admin.</span>
                            </div>
                        </form>
                    </Section>

                    <div className="grid gap-6 lg:grid-cols-2">
                        <Section title="Ringkasan Profil">
                            <Item label="Email kerja" value={employee.email} />
                            <Item label="Email personal" value={employee.profile?.personal_email} />
                            <Item label="No. HP" value={employee.profile?.phone_number || employee.phone_number} />
                            <Item label="Alamat" value={employee.profile?.current_address || employee.address} />
                            <Item label="Tempat / Tanggal Lahir" value={`${employee.profile?.place_of_birth || "-"} / ${employee.profile?.birth_date || "-"}`} />
                            <Item label="NIK" value={employee.profile?.identity_number || employee.nik} />
                        </Section>

                        <Section title="Informasi Kerja">
                            <Item label="Cabang" value={employee.branch?.name} />
                            <Item label="Departemen" value={employee.department?.name} />
                            <Item label="Jabatan" value={employee.position?.name} />
                            <Item label="Shift" value={workShift?.name} />
                            <Item label="Tanggal Bergabung" value={employee.profile?.joined_at || employee.joined_at} />
                            <Item label="Akhir Kontrak" value={employee.profile?.contract_end_at || employee.contract_end_at} />
                        </Section>
                    </div>
                </div>
            )}

            {tab === "leave" && (
                <div className="mt-6 grid gap-6 lg:grid-cols-2">
                    <Section title="Saldo Cuti">
                        <div className="space-y-3">
                            {(leaveBalances || []).map((balance) => (
                                <div key={balance.id} className="rounded-lg border border-[var(--border-line)] p-4">
                                    <div className="font-semibold">{balance.leave_type?.name}</div>
                                    <div className="mt-2 text-sm text-[var(--text-muted)]">Allocated: {balance.allocated_days} • Used: {balance.used_days} • Remaining: {balance.remaining_days}</div>
                                </div>
                            ))}
                        </div>
                    </Section>

                    <Section title="Pengajuan Cuti Terbaru">
                        <div className="space-y-3">
                            {(recentLeaveRequests || []).map((request) => (
                                <div key={request.id} className="rounded-lg border border-[var(--border-line)] p-4">
                                    <div className="font-semibold">{request.leave_type?.name}</div>
                                    <div className="mt-2 text-sm text-[var(--text-muted)]">{request.start_date} - {request.end_date} • {request.status}</div>
                                </div>
                            ))}
                        </div>
                        <Link href={route("employee.leave-requests.index")} className="ui-button-secondary mt-4">Buka Menu Cuti</Link>
                    </Section>
                </div>
            )}

            {tab === "attendance" && (
                <div className="mt-6 grid gap-6 lg:grid-cols-2">
                    <Section title="Koreksi Absensi">
                        <form
                            onSubmit={(e) => {
                                e.preventDefault();
                                correctionForm.post(route("attendance-corrections.store"), {
                                    forceFormData: true,
                                    preserveScroll: true,
                                    onSuccess: () => correctionForm.setData({
                                        ...correctionForm.data,
                                        reason: "",
                                        attachment: null,
                                    }),
                                });
                            }}
                            className="space-y-4"
                        >
                            <Field label="Tanggal absensi" error={correctionForm.errors.attendance_date}>
                                <input type="date" value={correctionForm.data.attendance_date} onChange={(e) => correctionForm.setData("attendance_date", e.target.value)} className="ui-input" />
                            </Field>
                            <Field label="Check-in yang diajukan" error={correctionForm.errors.requested_check_in_at}>
                                <input type="datetime-local" value={correctionForm.data.requested_check_in_at} onChange={(e) => correctionForm.setData("requested_check_in_at", e.target.value)} className="ui-input" />
                            </Field>
                            <Field label="Check-out yang diajukan" error={correctionForm.errors.requested_check_out_at}>
                                <input type="datetime-local" value={correctionForm.data.requested_check_out_at} onChange={(e) => correctionForm.setData("requested_check_out_at", e.target.value)} className="ui-input" />
                            </Field>
                            <Field label="Lampiran" error={correctionForm.errors.attachment}>
                                <input type="file" accept="image/*,.pdf" onChange={(e) => correctionForm.setData("attachment", e.target.files?.[0] || null)} className="ui-input" />
                            </Field>
                            <Field label="Alasan koreksi" error={correctionForm.errors.reason}>
                                <textarea value={correctionForm.data.reason} onChange={(e) => correctionForm.setData("reason", e.target.value)} className="ui-input" rows="3" />
                            </Field>
                            <button type="submit" disabled={correctionForm.processing} className="ui-button-primary">
                                {correctionForm.processing ? "Mengirim..." : "Ajukan Koreksi"}
                            </button>
                        </form>
                    </Section>

                    <Section title="Riwayat Koreksi">
                        <div className="space-y-3">
                            {(attendanceCorrections || []).map((item) => (
                                <div key={item.id} className="rounded-lg border border-[var(--border-line)] p-4">
                                    <div className="font-semibold">{item.attendance_date}</div>
                                    <div className="mt-2 text-sm text-[var(--text-muted)]">{item.reason}</div>
                                    <div className="mt-2">
                                        <span className={`ui-badge ${item.status === "pending" ? "bg-amber-50 text-[var(--warning-color)]" : item.status === "approved" ? "bg-emerald-50 text-[var(--success-color)]" : "bg-red-50 text-[var(--danger-color)]"}`}>
                                            {item.status}
                                        </span>
                                    </div>
                                </div>
                            ))}
                        </div>
                        <Link href={route("attendance.index")} className="ui-button-secondary mt-4">Buka Presensi</Link>
                    </Section>
                </div>
            )}

            {tab === "payroll" && (
                <div className="mt-6">
                    <Section title="Payroll Terbaru">
                        <div className="space-y-3">
                            {(recentPayrolls || []).map((payroll) => (
                                <div key={payroll.id} className="rounded-lg border border-[var(--border-line)] p-4">
                                    <div className="font-semibold">{payroll.month}/{payroll.year}</div>
                                    <div className="mt-2 text-sm text-[var(--text-muted)]">Net salary: Rp {Number(payroll.net_salary || 0).toLocaleString("id-ID")} • {payroll.status}</div>
                                </div>
                            ))}
                        </div>
                    </Section>
                </div>
            )}
        </AuthenticatedLayout>
    );
}
