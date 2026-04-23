import { useForm } from "@inertiajs/react";
import { WorkShiftForm } from "./WorkShiftCreate";

export default function WorkShiftEdit({ workShift, branches }) {
    const form = useForm({
        branch_id: workShift.branch_id || "",
        name: workShift.name || "",
        clock_in_time: workShift.clock_in_time || "",
        clock_out_time: workShift.clock_out_time || "",
        break_start_time: workShift.break_start_time || "",
        break_end_time: workShift.break_end_time || "",
        late_tolerance_minutes: workShift.late_tolerance_minutes || 10,
        work_days: workShift.work_days || [],
        is_default: workShift.is_default || false,
    });

    return <WorkShiftForm title="Edit Shift Kerja" submitLabel="Update Shift" branches={branches} form={form} submit={(event) => { event.preventDefault(); form.put(route("admin.work-shifts.update", workShift.id)); }} />;
}
