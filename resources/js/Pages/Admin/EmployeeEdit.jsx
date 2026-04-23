import { useForm } from "@inertiajs/react";
import { EmployeeForm } from "./EmployeeCreate";

export default function EmployeeEdit(props) {
    const { employee } = props;

    const form = useForm({
        name: employee.name || "",
        email: employee.email || "",
        password: "",
        employee_code: employee.profile?.employee_code || "",
        position_id: employee.position_id || "",
        department_id: employee.department_id || "",
        branch_id: employee.branch_id || "",
        work_shift_id: employee.work_shift_id || "",
        employment_type_id: employee.employment_type_id || "",
        manager_id: employee.manager_id || "",
        phone_number: employee.phone_number || "",
        personal_email: employee.profile?.personal_email || "",
        identity_number: employee.profile?.identity_number || "",
        tax_number: employee.profile?.tax_number || "",
        place_of_birth: employee.profile?.place_of_birth || "",
        birth_date: employee.profile?.birth_date || "",
        gender: employee.profile?.gender || "",
        marital_status: employee.profile?.marital_status || "",
        religion: employee.profile?.religion || "",
        nationality: employee.profile?.nationality || "Indonesia",
        joined_at: employee.profile?.joined_at || "",
        contract_start_at: employee.profile?.contract_start_at || "",
        contract_end_at: employee.profile?.contract_end_at || "",
        current_address: employee.profile?.current_address || "",
        domicile_address: employee.profile?.domicile_address || "",
        employment_status: employee.profile?.employment_status || "active",
        bank_name: employee.profile?.bank_name || "",
        bank_account_number: employee.profile?.bank_account_number || "",
        bank_account_name: employee.profile?.bank_account_name || "",
        alternate_phone_number: employee.profile?.alternate_phone_number || "",
        is_active: employee.is_active,
        face_reference_data: "",
        face_descriptor: [],
    });

    return (
        <EmployeeForm
            title="Edit Data Karyawan"
            subtitle="Perbarui data organisasi, identitas, dan administrasi karyawan agar modul attendance, leave, dan payroll tetap sinkron."
            actionLabel="Update Karyawan"
            form={form}
            options={props}
            submitRoute="admin.employees.update"
            method="put"
            employeeId={employee.id}
        />
    );
}
