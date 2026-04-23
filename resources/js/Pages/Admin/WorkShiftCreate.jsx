import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout";
import { Head, Link, useForm } from "@inertiajs/react";

const dayOptions = [
    { value: "mon", label: "Senin" },
    { value: "tue", label: "Selasa" },
    { value: "wed", label: "Rabu" },
    { value: "thu", label: "Kamis" },
    { value: "fri", label: "Jumat" },
    { value: "sat", label: "Sabtu" },
    { value: "sun", label: "Minggu" },
];

export function WorkShiftForm({ title, submitLabel, branches, form, submit }) {
    const { data, setData, processing, errors } = form;

    const toggleWorkDay = (value) => {
        if (data.work_days.includes(value)) {
            setData("work_days", data.work_days.filter((item) => item !== value));
            return;
        }
        setData("work_days", [...data.work_days, value]);
    };

    return (
        <AuthenticatedLayout>
            <Head title={title} />

            <div className="mx-auto max-w-4xl">
                <Link href={route("admin.work-shifts.index")} className="mb-6 inline-flex items-center text-sm font-medium text-[var(--text-muted)] hover:text-[var(--text-main)]">
                    Kembali ke daftar shift
                </Link>

                <section className="ui-card overflow-hidden">
                    <div className="border-b border-[var(--border-line)] p-6 lg:p-8">
                        <div className="ui-section-title">Work Shift Form</div>
                        <h1 className="font-heading mt-3 text-3xl font-bold text-black">{title}</h1>
                    </div>

                    <form onSubmit={submit} className="grid gap-6 p-6 lg:grid-cols-2 lg:p-8">
                        <div>
                            <label className="mb-2 block text-sm font-semibold">Cabang</label>
                            <select value={data.branch_id} onChange={(event) => setData("branch_id", event.target.value)} className="ui-input">
                                <option value="">Semua cabang</option>
                                {branches.map((branch) => <option key={branch.id} value={branch.id}>{branch.name}</option>)}
                            </select>
                        </div>
                        <div>
                            <label className="mb-2 block text-sm font-semibold">Nama shift</label>
                            <input value={data.name} onChange={(event) => setData("name", event.target.value)} className="ui-input" />
                            {errors.name && <p className="mt-1 text-sm text-red-600">{errors.name}</p>}
                        </div>
                        <div>
                            <label className="mb-2 block text-sm font-semibold">Jam masuk</label>
                            <input type="time" value={data.clock_in_time} onChange={(event) => setData("clock_in_time", event.target.value)} className="ui-input" />
                        </div>
                        <div>
                            <label className="mb-2 block text-sm font-semibold">Jam pulang</label>
                            <input type="time" value={data.clock_out_time} onChange={(event) => setData("clock_out_time", event.target.value)} className="ui-input" />
                        </div>
                        <div>
                            <label className="mb-2 block text-sm font-semibold">Mulai istirahat</label>
                            <input type="time" value={data.break_start_time} onChange={(event) => setData("break_start_time", event.target.value)} className="ui-input" />
                        </div>
                        <div>
                            <label className="mb-2 block text-sm font-semibold">Selesai istirahat</label>
                            <input type="time" value={data.break_end_time} onChange={(event) => setData("break_end_time", event.target.value)} className="ui-input" />
                        </div>
                        <div>
                            <label className="mb-2 block text-sm font-semibold">Toleransi terlambat</label>
                            <input type="number" value={data.late_tolerance_minutes} onChange={(event) => setData("late_tolerance_minutes", event.target.value)} className="ui-input" />
                        </div>
                        <label className="flex items-center gap-3 rounded-lg border border-[var(--border-line)] bg-[var(--bg-subtle)] px-4 py-3 text-sm font-medium">
                            <input type="checkbox" checked={data.is_default} onChange={(event) => setData("is_default", event.target.checked)} />
                            Jadikan shift default
                        </label>
                        <div className="lg:col-span-2">
                            <label className="mb-2 block text-sm font-semibold">Hari kerja</label>
                            <div className="flex flex-wrap gap-3">
                                {dayOptions.map((day) => (
                                    <label key={day.value} className="flex items-center gap-2 rounded-lg border border-[var(--border-line)] px-4 py-2 text-sm">
                                        <input type="checkbox" checked={data.work_days.includes(day.value)} onChange={() => toggleWorkDay(day.value)} />
                                        {day.label}
                                    </label>
                                ))}
                            </div>
                        </div>
                        <div className="lg:col-span-2 flex gap-3 pt-2">
                            <button type="submit" disabled={processing} className="ui-button-primary">{processing ? "Menyimpan..." : submitLabel}</button>
                            <Link href={route("admin.work-shifts.index")} className="ui-button-secondary">Batal</Link>
                        </div>
                    </form>
                </section>
            </div>
        </AuthenticatedLayout>
    );
}

export default function WorkShiftCreate({ branches }) {
    const form = useForm({
        branch_id: "",
        name: "",
        clock_in_time: "",
        clock_out_time: "",
        break_start_time: "",
        break_end_time: "",
        late_tolerance_minutes: 10,
        work_days: ["mon", "tue", "wed", "thu", "fri"],
        is_default: false,
    });

    return <WorkShiftForm title="Tambah Shift Kerja" submitLabel="Simpan Shift" branches={branches} form={form} submit={(event) => { event.preventDefault(); form.post(route("admin.work-shifts.store")); }} />;
}
