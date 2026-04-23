import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout";
import { Head, Link, useForm } from "@inertiajs/react";
import { useState } from "react";

function SectionCard({ title, children, action }) {
    return (
        <section className="ui-card p-5 sm:p-6">
            <div className="flex flex-col gap-3 border-b border-[var(--border-line)] pb-4 sm:flex-row sm:items-center sm:justify-between">
                <h2 className="font-heading text-lg font-semibold sm:text-xl">{title}</h2>
                {action}
            </div>
            <div className="mt-5">{children}</div>
        </section>
    );
}

function KeyValue({ label, value }) {
    return (
        <div className="flex flex-col gap-1 border-b border-dashed border-[var(--border-line)] py-2 last:border-b-0 sm:flex-row sm:items-start sm:justify-between sm:gap-4">
            <span className="text-sm text-[var(--text-muted)]">{label}</span>
            <span className="text-sm font-medium text-[var(--text-main)] sm:text-right">{value || "-"}</span>
        </div>
    );
}

function SimpleModal({ title, onClose, children }) {
    return (
        <div className="fixed inset-0 z-[70] flex items-center justify-center bg-slate-950/50 p-4 backdrop-blur-sm">
            <div className="w-full max-w-3xl rounded-xl bg-white shadow-[var(--shadow-pop)]">
                <div className="flex items-center justify-between border-b border-[var(--border-line)] px-5 py-4 sm:px-6">
                    <h2 className="font-heading text-lg font-semibold sm:text-xl">{title}</h2>
                    <button type="button" onClick={onClose} className="text-sm text-[var(--text-muted)]">
                        Tutup
                    </button>
                </div>
                <div className="p-5 sm:p-6">{children}</div>
            </div>
        </div>
    );
}

export default function EmployeeShow({ employee, departments, positions, branches, activityLogs }) {
    const [modal, setModal] = useState(null);
    const [editingItem, setEditingItem] = useState(null);

    const emergencyForm = useForm({ name: "", relationship: "", phone_number: "", address: "", is_primary: false });
    const documentForm = useForm({ document_type: "", document_number: "", document_file: null, issued_at: "", expired_at: "", notes: "", is_verified: false });
    const educationForm = useForm({ education_level: "", institution_name: "", major: "", start_year: "", end_year: "", gpa: "", is_latest: false });
    const careerForm = useForm({ department_id: "", position_id: "", branch_id: "", start_date: "", end_date: "", employment_status: "", change_reason: "", notes: "" });

    const openModal = (type, item = null) => {
        setModal(type);
        setEditingItem(item);

        if (type === "emergency") {
            emergencyForm.setData({
                name: item?.name || "",
                relationship: item?.relationship || "",
                phone_number: item?.phone_number || "",
                address: item?.address || "",
                is_primary: item?.is_primary || false,
            });
        }

        if (type === "document") {
            documentForm.setData({
                document_type: item?.document_type || "",
                document_number: item?.document_number || "",
                document_file: null,
                issued_at: item?.issued_at || "",
                expired_at: item?.expired_at || "",
                notes: item?.notes || "",
                is_verified: item?.is_verified || false,
            });
        }

        if (type === "education") {
            educationForm.setData({
                education_level: item?.education_level || "",
                institution_name: item?.institution_name || "",
                major: item?.major || "",
                start_year: item?.start_year || "",
                end_year: item?.end_year || "",
                gpa: item?.gpa || "",
                is_latest: item?.is_latest || false,
            });
        }

        if (type === "career") {
            careerForm.setData({
                department_id: item?.department_id || "",
                position_id: item?.position_id || "",
                branch_id: item?.branch_id || "",
                start_date: item?.start_date || "",
                end_date: item?.end_date || "",
                employment_status: item?.employment_status || "",
                change_reason: item?.change_reason || "",
                notes: item?.notes || "",
            });
        }
    };

    const closeModal = () => {
        setModal(null);
        setEditingItem(null);
    };

    return (
        <AuthenticatedLayout>
            <Head title={`Detail ${employee.name}`} />

            <div className="mobile-page max-w-7xl">
                <Link href={route("admin.employees.index")} className="mb-4 inline-flex items-center text-sm font-medium text-[var(--text-muted)] hover:text-[var(--text-main)] sm:mb-6">
                    Kembali ke master karyawan
                </Link>

                <section className="ui-card overflow-hidden">
                    <div className="grid gap-6 p-5 sm:p-6 lg:grid-cols-[1.4fr_0.8fr] lg:p-8">
                        <div>
                            <div className="ui-section-title">Employee Detail Center</div>
                            <h1 className="mt-3 font-heading text-2xl font-bold text-black sm:text-3xl">{employee.name}</h1>
                            <p className="mt-3 break-words text-sm text-[var(--text-muted)]">
                                {employee.profile?.employee_code || "Tanpa kode"} | {employee.position?.name || "-"} | {employee.department?.name || "-"}
                            </p>
                            <div className="mt-4 flex flex-wrap gap-2">
                                <span className="ui-badge bg-indigo-50 text-[var(--primary-color)]">{employee.branch?.name || "Tanpa cabang"}</span>
                                <span className="ui-badge bg-slate-100 text-slate-600">{employee.employment_type?.name || "Tanpa employment type"}</span>
                                <span className={`ui-badge ${employee.is_active ? "bg-emerald-50 text-[var(--success-color)]" : "bg-slate-100 text-slate-500"}`}>
                                    {employee.is_active ? "Aktif" : "Nonaktif"}
                                </span>
                            </div>
                        </div>
                        <div className="ui-card-muted p-5">
                            <div className="ui-section-title">Ringkasan</div>
                            <div className="mt-4 space-y-2 text-sm text-[var(--text-muted)]">
                                <div>Kontak darurat: <span className="font-medium text-[var(--text-main)]">{employee.emergency_contacts?.length || 0}</span></div>
                                <div>Dokumen: <span className="font-medium text-[var(--text-main)]">{employee.documents?.length || 0}</span></div>
                                <div>Pendidikan: <span className="font-medium text-[var(--text-main)]">{employee.educations?.length || 0}</span></div>
                                <div>Riwayat karier: <span className="font-medium text-[var(--text-main)]">{employee.career_histories?.length || 0}</span></div>
                            </div>
                        </div>
                    </div>
                </section>

                <div className="mt-6 grid gap-6 lg:grid-cols-2">
                    <SectionCard title="Profil Inti">
                        <KeyValue label="Email kerja" value={employee.email} />
                        <KeyValue label="Email personal" value={employee.profile?.personal_email} />
                        <KeyValue label="NIK" value={employee.profile?.identity_number || employee.nik} />
                        <KeyValue label="NPWP" value={employee.profile?.tax_number || employee.npwp} />
                        <KeyValue label="Tanggal bergabung" value={employee.profile?.joined_at || employee.joined_at} />
                        <KeyValue label="Akhir kontrak" value={employee.profile?.contract_end_at || employee.contract_end_at} />
                        <KeyValue label="Status kerja" value={employee.profile?.employment_status} />
                        <KeyValue label="Bank" value={employee.profile?.bank_name} />
                        <KeyValue label="Biometrik wajah" value={employee.profile?.face_reference_path ? "Tersedia" : "Belum tersedia"} />
                    </SectionCard>

                    <SectionCard title="Organisasi">
                        <KeyValue label="Cabang" value={employee.branch?.name} />
                        <KeyValue label="Departemen" value={employee.department?.name} />
                        <KeyValue label="Jabatan" value={employee.position?.name} />
                        <KeyValue label="Manager" value={employee.manager?.name} />
                        <KeyValue label="Shift" value={employee.work_shift?.name} />
                        <KeyValue label="Employment type" value={employee.employment_type?.name} />
                    </SectionCard>
                </div>

                <div className="mt-6 grid gap-6 lg:grid-cols-2">
                    <SectionCard title="Kontak Darurat" action={<button type="button" onClick={() => openModal("emergency")} className="ui-button-secondary w-full sm:w-auto">Tambah</button>}>
                        <div className="space-y-3">
                            {(employee.emergency_contacts || []).map((item) => (
                                <div key={item.id} className="rounded-lg border border-[var(--border-line)] p-4">
                                    <div className="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                                        <div className="font-semibold">{item.name}</div>
                                        <div className="flex flex-wrap gap-2">
                                            <button type="button" onClick={() => openModal("emergency", item)} className="text-sm text-[var(--primary-color)]">Edit</button>
                                            <Link as="button" method="delete" href={route("admin.employees.emergency-contacts.destroy", [employee.id, item.id])} className="text-sm text-red-600">Hapus</Link>
                                        </div>
                                    </div>
                                    <div className="mt-2 text-sm text-[var(--text-muted)]">{item.relationship} | {item.phone_number}</div>
                                </div>
                            ))}
                        </div>
                    </SectionCard>

                    <SectionCard title="Dokumen" action={<button type="button" onClick={() => openModal("document")} className="ui-button-secondary w-full sm:w-auto">Tambah</button>}>
                        <div className="space-y-3">
                            {(employee.documents || []).map((item) => (
                                <div key={item.id} className="rounded-lg border border-[var(--border-line)] p-4">
                                    <div className="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                                        <div className="font-semibold">{item.document_type}</div>
                                        <div className="flex flex-wrap gap-2">
                                            <button type="button" onClick={() => openModal("document", item)} className="text-sm text-[var(--primary-color)]">Edit</button>
                                            <Link as="button" method="delete" href={route("admin.employees.documents.destroy", [employee.id, item.id])} className="text-sm text-red-600">Hapus</Link>
                                        </div>
                                    </div>
                                    <div className="mt-2 text-sm text-[var(--text-muted)]">
                                        {item.document_number || "-"} | Berlaku sampai {item.expired_at || "-"} | {item.is_verified ? "Terverifikasi" : "Belum diverifikasi"}
                                    </div>
                                    {item.file_path && (
                                        <a href={`/storage/${item.file_path}`} target="_blank" rel="noreferrer" className="mt-2 inline-block text-xs font-semibold text-[var(--primary-color)] hover:underline">
                                            Lihat dokumen
                                        </a>
                                    )}
                                </div>
                            ))}
                        </div>
                    </SectionCard>

                    <SectionCard title="Pendidikan" action={<button type="button" onClick={() => openModal("education")} className="ui-button-secondary w-full sm:w-auto">Tambah</button>}>
                        <div className="space-y-3">
                            {(employee.educations || []).map((item) => (
                                <div key={item.id} className="rounded-lg border border-[var(--border-line)] p-4">
                                    <div className="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                                        <div className="font-semibold">{item.education_level} | {item.institution_name}</div>
                                        <div className="flex flex-wrap gap-2">
                                            <button type="button" onClick={() => openModal("education", item)} className="text-sm text-[var(--primary-color)]">Edit</button>
                                            <Link as="button" method="delete" href={route("admin.employees.educations.destroy", [employee.id, item.id])} className="text-sm text-red-600">Hapus</Link>
                                        </div>
                                    </div>
                                    <div className="mt-2 text-sm text-[var(--text-muted)]">{item.major || "-"} | {item.start_year || "-"} - {item.end_year || "-"}</div>
                                </div>
                            ))}
                        </div>
                    </SectionCard>

                    <SectionCard title="Riwayat Karier" action={<button type="button" onClick={() => openModal("career")} className="ui-button-secondary w-full sm:w-auto">Tambah</button>}>
                        <div className="space-y-3">
                            {(employee.career_histories || []).map((item) => (
                                <div key={item.id} className="rounded-lg border border-[var(--border-line)] p-4">
                                    <div className="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                                        <div className="font-semibold">{item.position?.name || "-"} | {item.department?.name || "-"}</div>
                                        <div className="flex flex-wrap gap-2">
                                            <button type="button" onClick={() => openModal("career", item)} className="text-sm text-[var(--primary-color)]">Edit</button>
                                            <Link as="button" method="delete" href={route("admin.employees.career-histories.destroy", [employee.id, item.id])} className="text-sm text-red-600">Hapus</Link>
                                        </div>
                                    </div>
                                    <div className="mt-2 text-sm text-[var(--text-muted)]">{item.branch?.name || "-"} | {item.start_date} - {item.end_date || "Sekarang"}</div>
                                </div>
                            ))}
                        </div>
                    </SectionCard>
                </div>

                <div className="mt-6">
                    <SectionCard title="Audit Trail Terbaru">
                        <div className="space-y-3">
                            {(activityLogs || []).map((item) => (
                                <div key={item.id} className="rounded-lg border border-[var(--border-line)] p-4">
                                    <div className="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                                        <div className="font-semibold text-[var(--text-main)]">{item.description}</div>
                                        <span className="ui-badge bg-indigo-50 text-[var(--primary-color)]">{item.event}</span>
                                    </div>
                                    <div className="mt-2 text-sm text-[var(--text-muted)]">
                                        {item.actor?.name || "System"} | {new Date(item.created_at).toLocaleString("id-ID")}
                                    </div>
                                </div>
                            ))}
                        </div>
                    </SectionCard>
                </div>

                {modal === "emergency" && (
                    <SimpleModal title={editingItem ? "Edit Kontak Darurat" : "Tambah Kontak Darurat"} onClose={closeModal}>
                        <form onSubmit={(event) => { event.preventDefault(); editingItem ? emergencyForm.put(route("admin.employees.emergency-contacts.update", [employee.id, editingItem.id]), { onSuccess: closeModal }) : emergencyForm.post(route("admin.employees.emergency-contacts.store", employee.id), { onSuccess: closeModal }); }} className="grid gap-4 md:grid-cols-2">
                            <input value={emergencyForm.data.name} onChange={(event) => emergencyForm.setData("name", event.target.value)} className="ui-input" placeholder="Nama" />
                            <input value={emergencyForm.data.relationship} onChange={(event) => emergencyForm.setData("relationship", event.target.value)} className="ui-input" placeholder="Hubungan" />
                            <input value={emergencyForm.data.phone_number} onChange={(event) => emergencyForm.setData("phone_number", event.target.value)} className="ui-input" placeholder="Telepon" />
                            <label className="flex items-center gap-2 rounded-lg border border-[var(--border-line)] px-4 py-3"><input type="checkbox" checked={emergencyForm.data.is_primary} onChange={(event) => emergencyForm.setData("is_primary", event.target.checked)} /> Kontak utama</label>
                            <textarea value={emergencyForm.data.address} onChange={(event) => emergencyForm.setData("address", event.target.value)} className="ui-input md:col-span-2" rows="3" placeholder="Alamat" />
                            <div className="md:col-span-2 grid gap-3 sm:flex">
                                <button className="ui-button-primary w-full sm:w-auto" type="submit">Simpan</button>
                                <button type="button" onClick={closeModal} className="ui-button-secondary w-full sm:w-auto">Batal</button>
                            </div>
                        </form>
                    </SimpleModal>
                )}

                {modal === "document" && (
                    <SimpleModal title={editingItem ? "Edit Dokumen" : "Tambah Dokumen"} onClose={closeModal}>
                        <form onSubmit={(event) => {
                            event.preventDefault();
                            if (editingItem) {
                                documentForm
                                    .transform((formData) => ({ ...formData, _method: "put" }))
                                    .post(route("admin.employees.documents.update", [employee.id, editingItem.id]), {
                                        forceFormData: true,
                                        onSuccess: closeModal,
                                    });
                                return;
                            }

                            documentForm.post(route("admin.employees.documents.store", employee.id), {
                                forceFormData: true,
                                onSuccess: closeModal,
                            });
                        }} className="grid gap-4 md:grid-cols-2">
                            <input value={documentForm.data.document_type} onChange={(event) => documentForm.setData("document_type", event.target.value)} className="ui-input" placeholder="Jenis dokumen" />
                            <input value={documentForm.data.document_number} onChange={(event) => documentForm.setData("document_number", event.target.value)} className="ui-input" placeholder="Nomor dokumen" />
                            <input type="file" onChange={(event) => documentForm.setData("document_file", event.target.files[0])} className="ui-input" />
                            <input type="date" value={documentForm.data.issued_at} onChange={(event) => documentForm.setData("issued_at", event.target.value)} className="ui-input" />
                            <input type="date" value={documentForm.data.expired_at} onChange={(event) => documentForm.setData("expired_at", event.target.value)} className="ui-input" />
                            <textarea value={documentForm.data.notes} onChange={(event) => documentForm.setData("notes", event.target.value)} className="ui-input md:col-span-2" rows="3" placeholder="Catatan" />
                            <label className="md:col-span-2 flex items-center gap-2 rounded-lg border border-[var(--border-line)] px-4 py-3"><input type="checkbox" checked={documentForm.data.is_verified} onChange={(event) => documentForm.setData("is_verified", event.target.checked)} /> Dokumen sudah diverifikasi</label>
                            <div className="md:col-span-2 grid gap-3 sm:flex">
                                <button className="ui-button-primary w-full sm:w-auto" type="submit">Simpan</button>
                                <button type="button" onClick={closeModal} className="ui-button-secondary w-full sm:w-auto">Batal</button>
                            </div>
                        </form>
                    </SimpleModal>
                )}

                {modal === "education" && (
                    <SimpleModal title={editingItem ? "Edit Pendidikan" : "Tambah Pendidikan"} onClose={closeModal}>
                        <form onSubmit={(event) => { event.preventDefault(); editingItem ? educationForm.put(route("admin.employees.educations.update", [employee.id, editingItem.id]), { onSuccess: closeModal }) : educationForm.post(route("admin.employees.educations.store", employee.id), { onSuccess: closeModal }); }} className="grid gap-4 md:grid-cols-2">
                            <input value={educationForm.data.education_level} onChange={(event) => educationForm.setData("education_level", event.target.value)} className="ui-input" placeholder="Level pendidikan" />
                            <input value={educationForm.data.institution_name} onChange={(event) => educationForm.setData("institution_name", event.target.value)} className="ui-input" placeholder="Institusi" />
                            <input value={educationForm.data.major} onChange={(event) => educationForm.setData("major", event.target.value)} className="ui-input" placeholder="Jurusan" />
                            <input value={educationForm.data.start_year} onChange={(event) => educationForm.setData("start_year", event.target.value)} className="ui-input" placeholder="Tahun mulai" />
                            <input value={educationForm.data.end_year} onChange={(event) => educationForm.setData("end_year", event.target.value)} className="ui-input" placeholder="Tahun selesai" />
                            <input value={educationForm.data.gpa} onChange={(event) => educationForm.setData("gpa", event.target.value)} className="ui-input" placeholder="IPK" />
                            <label className="md:col-span-2 flex items-center gap-2 rounded-lg border border-[var(--border-line)] px-4 py-3"><input type="checkbox" checked={educationForm.data.is_latest} onChange={(event) => educationForm.setData("is_latest", event.target.checked)} /> Jadikan pendidikan terbaru</label>
                            <div className="md:col-span-2 grid gap-3 sm:flex">
                                <button className="ui-button-primary w-full sm:w-auto" type="submit">Simpan</button>
                                <button type="button" onClick={closeModal} className="ui-button-secondary w-full sm:w-auto">Batal</button>
                            </div>
                        </form>
                    </SimpleModal>
                )}

                {modal === "career" && (
                    <SimpleModal title={editingItem ? "Edit Riwayat Karier" : "Tambah Riwayat Karier"} onClose={closeModal}>
                        <form onSubmit={(event) => { event.preventDefault(); editingItem ? careerForm.put(route("admin.employees.career-histories.update", [employee.id, editingItem.id]), { onSuccess: closeModal }) : careerForm.post(route("admin.employees.career-histories.store", employee.id), { onSuccess: closeModal }); }} className="grid gap-4 md:grid-cols-2">
                            <select value={careerForm.data.branch_id} onChange={(event) => careerForm.setData("branch_id", event.target.value)} className="ui-input"><option value="">Pilih cabang</option>{branches.map((item) => <option key={item.id} value={item.id}>{item.name}</option>)}</select>
                            <select value={careerForm.data.department_id} onChange={(event) => careerForm.setData("department_id", event.target.value)} className="ui-input"><option value="">Pilih departemen</option>{departments.map((item) => <option key={item.id} value={item.id}>{item.name}</option>)}</select>
                            <select value={careerForm.data.position_id} onChange={(event) => careerForm.setData("position_id", event.target.value)} className="ui-input md:col-span-2"><option value="">Pilih jabatan</option>{positions.map((item) => <option key={item.id} value={item.id}>{item.name}</option>)}</select>
                            <input type="date" value={careerForm.data.start_date} onChange={(event) => careerForm.setData("start_date", event.target.value)} className="ui-input" />
                            <input type="date" value={careerForm.data.end_date} onChange={(event) => careerForm.setData("end_date", event.target.value)} className="ui-input" />
                            <input value={careerForm.data.employment_status} onChange={(event) => careerForm.setData("employment_status", event.target.value)} className="ui-input" placeholder="Status" />
                            <input value={careerForm.data.change_reason} onChange={(event) => careerForm.setData("change_reason", event.target.value)} className="ui-input" placeholder="Alasan perubahan" />
                            <textarea value={careerForm.data.notes} onChange={(event) => careerForm.setData("notes", event.target.value)} className="ui-input md:col-span-2" rows="3" placeholder="Catatan" />
                            <div className="md:col-span-2 grid gap-3 sm:flex">
                                <button className="ui-button-primary w-full sm:w-auto" type="submit">Simpan</button>
                                <button type="button" onClick={closeModal} className="ui-button-secondary w-full sm:w-auto">Batal</button>
                            </div>
                        </form>
                    </SimpleModal>
                )}
            </div>
        </AuthenticatedLayout>
    );
}
