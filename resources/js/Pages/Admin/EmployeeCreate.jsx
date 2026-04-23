import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout";
import { Head, Link, useForm } from "@inertiajs/react";
import { useEffect, useRef, useState } from "react";
import { extractFaceDescriptorFromImage, loadFaceModels } from "@/Utils/faceDescriptor";

function FaceReferenceCapture({ data, setData }) {
    const [cameraOpen, setCameraOpen] = useState(false);
    const [preview, setPreview] = useState(data.face_reference_data || "");
    const [faceMessage, setFaceMessage] = useState("Memuat model verifikasi wajah...");
    const [capturingFace, setCapturingFace] = useState(false);
    const videoRef = useRef(null);
    const canvasRef = useRef(null);

    useEffect(() => {
        loadFaceModels()
            .then(() => setFaceMessage("Model wajah siap. Ambil foto wajah karyawan."))
            .catch(() => setFaceMessage("Model wajah gagal dimuat. Refresh halaman lalu coba lagi."));
    }, []);

    const openCamera = async () => {
        setCameraOpen(true);

        try {
            const stream = await navigator.mediaDevices.getUserMedia({ video: true });
            if (videoRef.current) {
                videoRef.current.srcObject = stream;
            }
        } catch (error) {
            console.error("Camera error:", error);
            alert("Kamera tidak dapat diakses.");
        }
    };

    const closeCamera = () => {
        if (videoRef.current?.srcObject) {
            videoRef.current.srcObject.getTracks().forEach((track) => track.stop());
            videoRef.current.srcObject = null;
        }

        setCameraOpen(false);
    };

    const capture = async () => {
        if (!videoRef.current || !canvasRef.current) {
            return;
        }

        setCapturingFace(true);
        setFaceMessage("Mendeteksi wajah dan membuat descriptor...");

        const video = videoRef.current;
        const canvas = canvasRef.current;
        canvas.width = video.videoWidth;
        canvas.height = video.videoHeight;

        const context = canvas.getContext("2d");
        context.translate(canvas.width, 0);
        context.scale(-1, 1);
        context.drawImage(video, 0, 0, canvas.width, canvas.height);

        const image = canvas.toDataURL("image/jpeg", 0.85);

        try {
            const descriptor = await extractFaceDescriptorFromImage(image);
            setPreview(image);
            setData({
                ...data,
                face_reference_data: image,
                face_descriptor: descriptor,
            });
            setFaceMessage("Wajah berhasil didaftarkan. Descriptor siap dipakai untuk presensi.");
            closeCamera();
        } catch (error) {
            setFaceMessage(error.message);
        } finally {
            setCapturingFace(false);
        }
    };

    return (
        <div className="rounded-xl border border-[var(--border-line)] bg-[var(--bg-subtle)] p-5">
            <div className="ui-section-title">Face Reference</div>
            <p className="mt-2 text-sm text-[var(--text-muted)]">
                Foto ini dipakai untuk memastikan absensi hanya bisa dilakukan oleh pemilik akun.
            </p>

            {preview ? (
                <div className="mt-4 overflow-hidden rounded-xl border border-[var(--border-line)] bg-slate-950">
                    <img src={preview} alt="Face reference preview" className="aspect-[4/3] w-full object-cover" />
                </div>
            ) : (
                <div className="mt-4 rounded-xl border border-dashed border-[var(--border-line)] bg-white px-4 py-10 text-center text-sm text-[var(--text-muted)]">
                    Belum ada foto referensi wajah.
                </div>
            )}

            <div className="mt-4 flex flex-wrap gap-3">
                <button type="button" onClick={openCamera} className="ui-button-secondary">
                    {preview ? "Ambil Ulang Foto Wajah" : "Ambil Foto Wajah"}
                </button>
                {preview && (
                    <button type="button" onClick={() => { setPreview(""); setData({ ...data, face_reference_data: "", face_descriptor: [] }); }} className="rounded-md border border-red-200 px-4 py-2 text-sm font-semibold text-red-600 transition hover:bg-red-50">
                        Hapus Foto
                    </button>
                )}
            </div>

            <p className="mt-3 rounded-lg border border-[var(--border-line)] bg-white px-3 py-2 text-sm text-[var(--text-muted)]">
                {faceMessage}
            </p>

            {cameraOpen && (
                <div className="mt-4 space-y-3">
                    <div className="overflow-hidden rounded-xl border border-[var(--border-line)] bg-slate-950">
                        <video ref={videoRef} autoPlay playsInline muted className="aspect-[4/3] w-full object-cover" />
                    </div>
                    <div className="flex gap-3">
                        <button type="button" onClick={capture} disabled={capturingFace} className="ui-button-primary disabled:cursor-not-allowed disabled:opacity-60">
                            {capturingFace ? "Memproses wajah..." : "Simpan Foto Wajah"}
                        </button>
                        <button type="button" onClick={closeCamera} className="ui-button-secondary">
                            Batal
                        </button>
                    </div>
                </div>
            )}

            <canvas ref={canvasRef} className="hidden" />
        </div>
    );
}

export function EmployeeForm({ title, subtitle, actionLabel, form, options, submitRoute, method = "post", employeeId = null }) {
    const { data, setData, post, put, processing, errors } = form;

    const submit = (event) => {
        event.preventDefault();

        if (method === "put") {
            put(route(submitRoute, employeeId));
            return;
        }

        post(route(submitRoute));
    };

    return (
        <AuthenticatedLayout>
            <Head title={title} />

            <div className="mx-auto max-w-6xl">
                <Link href={route("admin.employees.index")} className="mb-6 inline-flex items-center text-sm font-medium text-[var(--text-muted)] hover:text-[var(--text-main)]">
                    Kembali ke master karyawan
                </Link>

                <section className="ui-card overflow-hidden">
                    <div className="border-b border-[var(--border-line)] p-6 lg:p-8">
                        <div className="ui-section-title">Employee Form</div>
                        <h1 className="font-heading mt-3 text-3xl font-bold text-black">{title}</h1>
                        <p className="mt-3 max-w-2xl text-sm leading-6 text-[var(--text-muted)]">{subtitle}</p>
                    </div>

                    <form onSubmit={submit} className="grid gap-8 p-6 lg:grid-cols-2 lg:p-8">
                        <section className="space-y-5">
                            <div className="ui-section-title">Akun dan Organisasi</div>

                            <div>
                                <label className="mb-2 block text-sm font-semibold text-[var(--text-main)]">Nama lengkap</label>
                                <input value={data.name} onChange={(event) => setData("name", event.target.value)} className="ui-input" />
                                {errors.name && <p className="mt-1 text-sm text-red-600">{errors.name}</p>}
                            </div>

                            <div className="grid gap-5 md:grid-cols-2">
                                <div>
                                    <label className="mb-2 block text-sm font-semibold text-[var(--text-main)]">Email kerja</label>
                                    <input type="email" value={data.email} onChange={(event) => setData("email", event.target.value)} className="ui-input" />
                                    {errors.email && <p className="mt-1 text-sm text-red-600">{errors.email}</p>}
                                </div>
                                <div>
                                    <label className="mb-2 block text-sm font-semibold text-[var(--text-main)]">Password {method === "put" ? "(opsional)" : ""}</label>
                                    <input type="password" value={data.password} onChange={(event) => setData("password", event.target.value)} className="ui-input" />
                                    {errors.password && <p className="mt-1 text-sm text-red-600">{errors.password}</p>}
                                </div>
                            </div>

                            <div className="grid gap-5 md:grid-cols-2">
                                <div>
                                    <label className="mb-2 block text-sm font-semibold text-[var(--text-main)]">Employee code</label>
                                    <input value={data.employee_code} onChange={(event) => setData("employee_code", event.target.value)} className="ui-input" />
                                    {errors.employee_code && <p className="mt-1 text-sm text-red-600">{errors.employee_code}</p>}
                                </div>
                                <div>
                                    <label className="mb-2 block text-sm font-semibold text-[var(--text-main)]">Employment status</label>
                                    <select value={data.employment_status} onChange={(event) => setData("employment_status", event.target.value)} className="ui-input">
                                        <option value="active">Active</option>
                                        <option value="probation">Probation</option>
                                        <option value="contract">Contract</option>
                                        <option value="inactive">Inactive</option>
                                    </select>
                                </div>
                            </div>

                            <div className="grid gap-5 md:grid-cols-2">
                                <div>
                                    <label className="mb-2 block text-sm font-semibold text-[var(--text-main)]">Jabatan</label>
                                    <select value={data.position_id} onChange={(event) => setData("position_id", event.target.value)} className="ui-input">
                                        <option value="">Pilih jabatan</option>
                                        {options.positions.map((item) => (
                                            <option key={item.id} value={item.id}>{item.name}</option>
                                        ))}
                                    </select>
                                </div>
                                <div>
                                    <label className="mb-2 block text-sm font-semibold text-[var(--text-main)]">Departemen</label>
                                    <select value={data.department_id} onChange={(event) => setData("department_id", event.target.value)} className="ui-input">
                                        <option value="">Pilih departemen</option>
                                        {options.departments.map((item) => (
                                            <option key={item.id} value={item.id}>{item.name}</option>
                                        ))}
                                    </select>
                                </div>
                            </div>

                            <div className="grid gap-5 md:grid-cols-2">
                                <div>
                                    <label className="mb-2 block text-sm font-semibold text-[var(--text-main)]">Cabang</label>
                                    <select value={data.branch_id} onChange={(event) => setData("branch_id", event.target.value)} className="ui-input">
                                        <option value="">Pilih cabang</option>
                                        {options.branches.map((item) => (
                                            <option key={item.id} value={item.id}>{item.name}</option>
                                        ))}
                                    </select>
                                </div>
                                <div>
                                    <label className="mb-2 block text-sm font-semibold text-[var(--text-main)]">Shift kerja</label>
                                    <select value={data.work_shift_id} onChange={(event) => setData("work_shift_id", event.target.value)} className="ui-input">
                                        <option value="">Pilih shift</option>
                                        {options.workShifts.map((item) => (
                                            <option key={item.id} value={item.id}>{item.name} ({item.clock_in_time} - {item.clock_out_time})</option>
                                        ))}
                                    </select>
                                </div>
                            </div>

                            <div className="grid gap-5 md:grid-cols-2">
                                <div>
                                    <label className="mb-2 block text-sm font-semibold text-[var(--text-main)]">Employment type</label>
                                    <select value={data.employment_type_id} onChange={(event) => setData("employment_type_id", event.target.value)} className="ui-input">
                                        <option value="">Pilih jenis employment</option>
                                        {options.employmentTypes.map((item) => (
                                            <option key={item.id} value={item.id}>{item.name}</option>
                                        ))}
                                    </select>
                                </div>
                                <div>
                                    <label className="mb-2 block text-sm font-semibold text-[var(--text-main)]">Atasan langsung</label>
                                    <select value={data.manager_id} onChange={(event) => setData("manager_id", event.target.value)} className="ui-input">
                                        <option value="">Pilih atasan</option>
                                        {options.managers.map((item) => (
                                            <option key={item.id} value={item.id}>{item.name}</option>
                                        ))}
                                    </select>
                                </div>
                            </div>
                        </section>

                        <section className="space-y-5">
                            <div className="ui-section-title">Profil HRIS</div>

                            <div className="grid gap-5 md:grid-cols-2">
                                <div>
                                    <label className="mb-2 block text-sm font-semibold text-[var(--text-main)]">Nomor HP</label>
                                    <input value={data.phone_number} onChange={(event) => setData("phone_number", event.target.value)} className="ui-input" />
                                </div>
                                <div>
                                    <label className="mb-2 block text-sm font-semibold text-[var(--text-main)]">Email personal</label>
                                    <input type="email" value={data.personal_email} onChange={(event) => setData("personal_email", event.target.value)} className="ui-input" />
                                </div>
                            </div>

                            <div className="grid gap-5 md:grid-cols-2">
                                <div>
                                    <label className="mb-2 block text-sm font-semibold text-[var(--text-main)]">NIK</label>
                                    <input value={data.identity_number} onChange={(event) => setData("identity_number", event.target.value)} className="ui-input" />
                                </div>
                                <div>
                                    <label className="mb-2 block text-sm font-semibold text-[var(--text-main)]">NPWP</label>
                                    <input value={data.tax_number} onChange={(event) => setData("tax_number", event.target.value)} className="ui-input" />
                                </div>
                            </div>

                            <div className="grid gap-5 md:grid-cols-2">
                                <div>
                                    <label className="mb-2 block text-sm font-semibold text-[var(--text-main)]">Tempat lahir</label>
                                    <input value={data.place_of_birth} onChange={(event) => setData("place_of_birth", event.target.value)} className="ui-input" />
                                </div>
                                <div>
                                    <label className="mb-2 block text-sm font-semibold text-[var(--text-main)]">Tanggal lahir</label>
                                    <input type="date" value={data.birth_date} onChange={(event) => setData("birth_date", event.target.value)} className="ui-input" />
                                </div>
                            </div>

                            <div className="grid gap-5 md:grid-cols-3">
                                <div>
                                    <label className="mb-2 block text-sm font-semibold text-[var(--text-main)]">Gender</label>
                                    <select value={data.gender} onChange={(event) => setData("gender", event.target.value)} className="ui-input">
                                        <option value="">Pilih gender</option>
                                        <option value="male">Laki-laki</option>
                                        <option value="female">Perempuan</option>
                                    </select>
                                </div>
                                <div>
                                    <label className="mb-2 block text-sm font-semibold text-[var(--text-main)]">Status menikah</label>
                                    <input value={data.marital_status} onChange={(event) => setData("marital_status", event.target.value)} className="ui-input" />
                                </div>
                                <div>
                                    <label className="mb-2 block text-sm font-semibold text-[var(--text-main)]">Kewarganegaraan</label>
                                    <input value={data.nationality} onChange={(event) => setData("nationality", event.target.value)} className="ui-input" />
                                </div>
                            </div>

                            <div className="grid gap-5 md:grid-cols-2">
                                <div>
                                    <label className="mb-2 block text-sm font-semibold text-[var(--text-main)]">Tanggal bergabung</label>
                                    <input type="date" value={data.joined_at} onChange={(event) => setData("joined_at", event.target.value)} className="ui-input" />
                                </div>
                                <div>
                                    <label className="mb-2 block text-sm font-semibold text-[var(--text-main)]">Mulai kontrak</label>
                                    <input type="date" value={data.contract_start_at} onChange={(event) => setData("contract_start_at", event.target.value)} className="ui-input" />
                                </div>
                            </div>

                            <div className="grid gap-5 md:grid-cols-2">
                                <div>
                                    <label className="mb-2 block text-sm font-semibold text-[var(--text-main)]">Akhir kontrak</label>
                                    <input type="date" value={data.contract_end_at} onChange={(event) => setData("contract_end_at", event.target.value)} className="ui-input" />
                                </div>
                                <div>
                                    <label className="mb-2 block text-sm font-semibold text-[var(--text-main)]">Agama</label>
                                    <input value={data.religion} onChange={(event) => setData("religion", event.target.value)} className="ui-input" />
                                </div>
                            </div>

                            <div>
                                <label className="mb-2 block text-sm font-semibold text-[var(--text-main)]">Alamat saat ini</label>
                                <textarea rows="3" value={data.current_address} onChange={(event) => setData("current_address", event.target.value)} className="ui-input" />
                            </div>

                            <div className="grid gap-5 md:grid-cols-3">
                                <div>
                                    <label className="mb-2 block text-sm font-semibold text-[var(--text-main)]">Bank</label>
                                    <input value={data.bank_name} onChange={(event) => setData("bank_name", event.target.value)} className="ui-input" />
                                </div>
                                <div>
                                    <label className="mb-2 block text-sm font-semibold text-[var(--text-main)]">No. rekening</label>
                                    <input value={data.bank_account_number} onChange={(event) => setData("bank_account_number", event.target.value)} className="ui-input" />
                                </div>
                                <div>
                                    <label className="mb-2 block text-sm font-semibold text-[var(--text-main)]">Nama pemilik rekening</label>
                                    <input value={data.bank_account_name} onChange={(event) => setData("bank_account_name", event.target.value)} className="ui-input" />
                                </div>
                            </div>

                            <label className="flex items-center gap-3 rounded-lg border border-[var(--border-line)] bg-[var(--bg-subtle)] px-4 py-3 text-sm font-medium text-[var(--text-main)]">
                                <input type="checkbox" checked={data.is_active} onChange={(event) => setData("is_active", event.target.checked)} />
                                Karyawan aktif
                            </label>

                            <FaceReferenceCapture data={data} setData={setData} />
                            {errors.face_reference_data && <p className="text-sm text-red-600">{errors.face_reference_data}</p>}
                        </section>

                        <div className="lg:col-span-2">
                            <div className="flex flex-wrap gap-3 border-t border-[var(--border-line)] pt-6">
                                <button type="submit" disabled={processing} className="ui-button-primary">
                                    {processing ? "Menyimpan..." : actionLabel}
                                </button>
                                <Link href={route("admin.employees.index")} className="ui-button-secondary">
                                    Batal
                                </Link>
                            </div>
                        </div>
                    </form>
                </section>
            </div>
        </AuthenticatedLayout>
    );
}

export default function EmployeeCreate(props) {
    const form = useForm({
        name: "",
        email: "",
        password: "",
        employee_code: "",
        position_id: "",
        department_id: "",
        branch_id: "",
        work_shift_id: "",
        employment_type_id: "",
        manager_id: "",
        phone_number: "",
        personal_email: "",
        identity_number: "",
        tax_number: "",
        place_of_birth: "",
        birth_date: "",
        gender: "",
        marital_status: "",
        religion: "",
        nationality: "Indonesia",
        joined_at: "",
        contract_start_at: "",
        contract_end_at: "",
        current_address: "",
        domicile_address: "",
        employment_status: "active",
        bank_name: "",
        bank_account_number: "",
        bank_account_name: "",
        alternate_phone_number: "",
        is_active: true,
        face_reference_data: "",
        face_descriptor: [],
    });

    return (
        <EmployeeForm
            title="Tambah Karyawan Baru"
            subtitle="Form ini sudah mengikuti struktur data HRIS yang lebih lengkap, termasuk employee code, cabang, employment type, dan profil administratif."
            actionLabel="Simpan Karyawan"
            form={form}
            options={props}
            submitRoute="admin.employees.store"
        />
    );
}
