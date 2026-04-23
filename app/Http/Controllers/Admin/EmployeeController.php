<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Department;
use App\Models\EmployeeCareerHistory;
use App\Models\EmployeeDocument;
use App\Models\EmployeeEducation;
use App\Models\EmployeeEmergencyContact;
use App\Models\EmployeeProfile;
use App\Models\EmploymentType;
use App\Models\Position;
use App\Models\User;
use App\Models\WorkShift;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Intervention\Image\ImageManager;
use Inertia\Inertia;

class EmployeeController extends Controller
{
    public function index()
    {
        $employees = User::query()
            ->where('role', 'employee')
            ->with(['profile', 'position', 'department', 'branch', 'employmentType', 'manager'])
            ->latest()
            ->get()
            ->map(function (User $employee) {
                return [
                    'id' => $employee->id,
                    'name' => $employee->name,
                    'email' => $employee->email,
                    'phone_number' => $employee->phone_number ?? $employee->profile?->phone_number,
                    'joined_at' => optional($employee->profile?->joined_at ?? $employee->joined_at)?->format('Y-m-d'),
                    'is_active' => (bool) $employee->is_active,
                    'position' => $employee->position ? ['id' => $employee->position->id, 'name' => $employee->position->name] : null,
                    'department' => $employee->department ? ['id' => $employee->department->id, 'name' => $employee->department->name] : null,
                    'branch' => $employee->branch ? ['id' => $employee->branch->id, 'name' => $employee->branch->name] : null,
                    'employment_type' => $employee->employmentType ? ['id' => $employee->employmentType->id, 'name' => $employee->employmentType->name] : null,
                    'manager' => $employee->manager ? ['id' => $employee->manager->id, 'name' => $employee->manager->name] : null,
                    'profile' => [
                        'employee_code' => $employee->profile?->employee_code,
                        'identity_number' => $employee->profile?->identity_number ?? $employee->nik,
                        'tax_number' => $employee->profile?->tax_number ?? $employee->npwp,
                        'personal_email' => $employee->profile?->personal_email,
                        'employment_status' => $employee->profile?->employment_status ?? ($employee->is_active ? 'active' : 'inactive'),
                        'face_reference_path' => $employee->profile?->face_reference_path ?? $employee->face_reference_path,
                        'contract_start_at' => optional($employee->profile?->contract_start_at)?->format('Y-m-d'),
                        'contract_end_at' => optional($employee->profile?->contract_end_at ?? $employee->contract_end_at)?->format('Y-m-d'),
                    ],
                ];
            });

        return Inertia::render('Admin/Employees', [
            'employees' => $employees,
        ]);
    }

    public function create()
    {
        return Inertia::render('Admin/EmployeeCreate', $this->formOptions());
    }

    public function show(User $employee)
    {
        $employee->load([
            'profile',
            'position',
            'department',
            'branch',
            'workShift',
            'employmentType',
            'manager',
            'emergencyContacts',
            'documents',
            'educations',
            'careerHistories.department',
            'careerHistories.position',
            'careerHistories.branch',
            'attendances' => fn ($query) => $query->latest()->limit(10),
            'leaveRequests.leaveType' => fn ($query) => $query->latest()->limit(5),
            'payrolls' => fn ($query) => $query->latest()->limit(5),
            'kpiScores' => fn ($query) => $query->latest()->limit(5),
        ]);

        $activityLogs = \App\Models\ActivityLog::with('actor:id,name,email')
            ->where('subject_type', User::class)
            ->where('subject_id', $employee->id)
            ->latest()
            ->limit(20)
            ->get();

        return Inertia::render('Admin/EmployeeShow', array_merge($this->formOptions($employee->id), [
            'employee' => $employee,
            'activityLogs' => $activityLogs,
        ]));
    }

    public function store(Request $request)
    {
        $validated = $request->validate($this->rules());

        DB::transaction(function () use ($validated) {
            $faceReferencePath = $this->storeFaceReference($validated['face_reference_data'] ?? null);

            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => $validated['password'],
                'role' => 'employee',
                'position_id' => $validated['position_id'],
                'department_id' => $validated['department_id'] ?: null,
                'branch_id' => $validated['branch_id'] ?: null,
                'work_shift_id' => $validated['work_shift_id'] ?: null,
                'employment_type_id' => $validated['employment_type_id'] ?: null,
                'manager_id' => $validated['manager_id'] ?: null,
                'phone_number' => $validated['phone_number'] ?: null,
                'address' => $validated['current_address'] ?: null,
                'gender' => $validated['gender'] ?: null,
                'birth_date' => $validated['birth_date'] ?: null,
                'nik' => $validated['identity_number'] ?: null,
                'npwp' => $validated['tax_number'] ?: null,
                'joined_at' => $validated['joined_at'] ?: null,
                'contract_end_at' => $validated['contract_end_at'] ?: null,
                'is_active' => $validated['is_active'],
                'face_reference_path' => $faceReferencePath,
                'face_descriptor' => $validated['face_descriptor'] ?? null,
            ]);

            EmployeeProfile::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'employee_code' => $validated['employee_code'],
                    'identity_number' => $validated['identity_number'] ?: null,
                    'tax_number' => $validated['tax_number'] ?: null,
                    'place_of_birth' => $validated['place_of_birth'] ?: null,
                    'birth_date' => $validated['birth_date'] ?: null,
                    'gender' => $validated['gender'] ?: null,
                    'marital_status' => $validated['marital_status'] ?: null,
                    'religion' => $validated['religion'] ?: null,
                    'nationality' => $validated['nationality'] ?: null,
                    'current_address' => $validated['current_address'] ?: null,
                    'domicile_address' => $validated['domicile_address'] ?: null,
                    'phone_number' => $validated['phone_number'] ?: null,
                    'alternate_phone_number' => $validated['alternate_phone_number'] ?: null,
                    'personal_email' => $validated['personal_email'] ?: null,
                    'face_reference_path' => $faceReferencePath,
                    'face_descriptor' => $validated['face_descriptor'] ?? null,
                    'joined_at' => $validated['joined_at'] ?: null,
                    'contract_start_at' => $validated['contract_start_at'] ?: null,
                    'contract_end_at' => $validated['contract_end_at'] ?: null,
                    'employment_status' => $validated['employment_status'],
                    'bank_name' => $validated['bank_name'] ?: null,
                    'bank_account_number' => $validated['bank_account_number'] ?: null,
                    'bank_account_name' => $validated['bank_account_name'] ?: null,
                ]
            );

            app(\App\Services\ActivityLogService::class)->log(
                auth()->id(),
                $user,
                'employee.created',
                'Membuat data karyawan baru untuk ' . $user->name,
                [
                    'employee_code' => $validated['employee_code'],
                    'branch_id' => $validated['branch_id'],
                    'position_id' => $validated['position_id'],
                ]
            );
        });

        return redirect()->route('admin.employees.index')->with('success', 'Karyawan berhasil ditambahkan.');
    }

    public function edit(User $employee)
    {
        $employee->load(['profile', 'position', 'department', 'branch', 'workShift', 'employmentType', 'manager']);

        return Inertia::render('Admin/EmployeeEdit', array_merge($this->formOptions($employee->id), [
            'employee' => [
                'id' => $employee->id,
                'name' => $employee->name,
                'email' => $employee->email,
                'position_id' => $employee->position_id,
                'department_id' => $employee->department_id,
                'branch_id' => $employee->branch_id,
                'work_shift_id' => $employee->work_shift_id,
                'employment_type_id' => $employee->employment_type_id,
                'manager_id' => $employee->manager_id,
                'phone_number' => $employee->phone_number ?? $employee->profile?->phone_number,
                'is_active' => (bool) $employee->is_active,
                'profile' => [
                    'employee_code' => $employee->profile?->employee_code,
                    'identity_number' => $employee->profile?->identity_number ?? $employee->nik,
                    'tax_number' => $employee->profile?->tax_number ?? $employee->npwp,
                    'place_of_birth' => $employee->profile?->place_of_birth,
                    'birth_date' => optional($employee->profile?->birth_date ?? $employee->birth_date)?->format('Y-m-d'),
                    'gender' => $employee->profile?->gender ?? $employee->gender,
                    'marital_status' => $employee->profile?->marital_status,
                    'religion' => $employee->profile?->religion,
                    'nationality' => $employee->profile?->nationality,
                    'current_address' => $employee->profile?->current_address ?? $employee->address,
                    'domicile_address' => $employee->profile?->domicile_address,
                    'alternate_phone_number' => $employee->profile?->alternate_phone_number,
                    'personal_email' => $employee->profile?->personal_email,
                    'joined_at' => optional($employee->profile?->joined_at ?? $employee->joined_at)?->format('Y-m-d'),
                    'contract_start_at' => optional($employee->profile?->contract_start_at)?->format('Y-m-d'),
                    'contract_end_at' => optional($employee->profile?->contract_end_at ?? $employee->contract_end_at)?->format('Y-m-d'),
                    'employment_status' => $employee->profile?->employment_status ?? ($employee->is_active ? 'active' : 'inactive'),
                    'bank_name' => $employee->profile?->bank_name,
                    'bank_account_number' => $employee->profile?->bank_account_number,
                    'bank_account_name' => $employee->profile?->bank_account_name,
                ],
            ],
        ]));
    }

    public function update(Request $request, User $employee)
    {
        $validated = $request->validate($this->rules($employee));

        DB::transaction(function () use ($employee, $validated) {
            $faceReferencePath = $employee->profile?->face_reference_path ?: $employee->face_reference_path;
            $faceDescriptor = $employee->profile?->face_descriptor ?: $employee->face_descriptor;

            if (!empty($validated['face_reference_data'])) {
                $faceReferencePath = $this->storeFaceReference($validated['face_reference_data'], $faceReferencePath);
                $faceDescriptor = $validated['face_descriptor'] ?? null;
            }

            $employee->update([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'position_id' => $validated['position_id'],
                'department_id' => $validated['department_id'] ?: null,
                'branch_id' => $validated['branch_id'] ?: null,
                'work_shift_id' => $validated['work_shift_id'] ?: null,
                'employment_type_id' => $validated['employment_type_id'] ?: null,
                'manager_id' => $validated['manager_id'] ?: null,
                'phone_number' => $validated['phone_number'] ?: null,
                'address' => $validated['current_address'] ?: null,
                'gender' => $validated['gender'] ?: null,
                'birth_date' => $validated['birth_date'] ?: null,
                'nik' => $validated['identity_number'] ?: null,
                'npwp' => $validated['tax_number'] ?: null,
                'joined_at' => $validated['joined_at'] ?: null,
                'contract_end_at' => $validated['contract_end_at'] ?: null,
                'is_active' => $validated['is_active'],
                'face_reference_path' => $faceReferencePath,
                'face_descriptor' => $faceDescriptor,
            ]);

            if (!empty($validated['password'])) {
                $employee->update(['password' => $validated['password']]);
            }

            EmployeeProfile::updateOrCreate(
                ['user_id' => $employee->id],
                [
                    'employee_code' => $validated['employee_code'],
                    'identity_number' => $validated['identity_number'] ?: null,
                    'tax_number' => $validated['tax_number'] ?: null,
                    'place_of_birth' => $validated['place_of_birth'] ?: null,
                    'birth_date' => $validated['birth_date'] ?: null,
                    'gender' => $validated['gender'] ?: null,
                    'marital_status' => $validated['marital_status'] ?: null,
                    'religion' => $validated['religion'] ?: null,
                    'nationality' => $validated['nationality'] ?: null,
                    'current_address' => $validated['current_address'] ?: null,
                    'domicile_address' => $validated['domicile_address'] ?: null,
                    'phone_number' => $validated['phone_number'] ?: null,
                    'alternate_phone_number' => $validated['alternate_phone_number'] ?: null,
                    'personal_email' => $validated['personal_email'] ?: null,
                    'face_reference_path' => $faceReferencePath,
                    'face_descriptor' => $faceDescriptor,
                    'joined_at' => $validated['joined_at'] ?: null,
                    'contract_start_at' => $validated['contract_start_at'] ?: null,
                    'contract_end_at' => $validated['contract_end_at'] ?: null,
                    'employment_status' => $validated['employment_status'],
                    'bank_name' => $validated['bank_name'] ?: null,
                    'bank_account_number' => $validated['bank_account_number'] ?: null,
                    'bank_account_name' => $validated['bank_account_name'] ?: null,
                ]
            );

            app(\App\Services\ActivityLogService::class)->log(
                auth()->id(),
                $employee,
                'employee.updated',
                'Memperbarui data karyawan ' . $employee->name,
                [
                    'branch_id' => $validated['branch_id'],
                    'position_id' => $validated['position_id'],
                    'employment_status' => $validated['employment_status'],
                    'is_active' => $validated['is_active'],
                ]
            );
        });

        return redirect()->route('admin.employees.index')->with('success', 'Data karyawan berhasil diperbarui.');
    }

    public function destroy(User $employee)
    {
        $employee->delete();

        return redirect()->route('admin.employees.index')->with('success', 'Karyawan berhasil dihapus.');
    }

    public function storeEmergencyContact(Request $request, User $employee)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'relationship' => ['required', 'string', 'max:100'],
            'phone_number' => ['required', 'string', 'max:30'],
            'address' => ['nullable', 'string'],
            'is_primary' => ['required', 'boolean'],
        ]);

        if ($validated['is_primary']) {
            $employee->emergencyContacts()->update(['is_primary' => false]);
        }

        $employee->emergencyContacts()->create($validated);

        return redirect()->route('admin.employees.show', $employee)->with('success', 'Kontak darurat berhasil ditambahkan.');
    }

    public function updateEmergencyContact(Request $request, User $employee, EmployeeEmergencyContact $contact)
    {
        abort_unless($contact->user_id === $employee->id, 404);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'relationship' => ['required', 'string', 'max:100'],
            'phone_number' => ['required', 'string', 'max:30'],
            'address' => ['nullable', 'string'],
            'is_primary' => ['required', 'boolean'],
        ]);

        if ($validated['is_primary']) {
            $employee->emergencyContacts()->where('id', '!=', $contact->id)->update(['is_primary' => false]);
        }

        $contact->update($validated);

        return redirect()->route('admin.employees.show', $employee)->with('success', 'Kontak darurat berhasil diperbarui.');
    }

    public function destroyEmergencyContact(User $employee, EmployeeEmergencyContact $contact)
    {
        abort_unless($contact->user_id === $employee->id, 404);
        $contact->delete();

        return redirect()->route('admin.employees.show', $employee)->with('success', 'Kontak darurat berhasil dihapus.');
    }

    public function storeDocument(Request $request, User $employee)
    {
        $validated = $request->validate([
            'document_type' => ['required', 'string', 'max:100'],
            'document_number' => ['nullable', 'string', 'max:100'],
            'document_file' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:4096'],
            'issued_at' => ['nullable', 'date'],
            'expired_at' => ['nullable', 'date'],
            'notes' => ['nullable', 'string'],
            'is_verified' => ['required', 'boolean'],
        ]);

        $path = null;
        if ($request->hasFile('document_file')) {
            $path = $request->file('document_file')->store('employee_documents', 'public');
        }

        $document = $employee->documents()->create([
            'document_type' => $validated['document_type'],
            'document_number' => $validated['document_number'] ?? null,
            'file_path' => $path,
            'issued_at' => $validated['issued_at'] ?? null,
            'expired_at' => $validated['expired_at'] ?? null,
            'notes' => $validated['notes'] ?? null,
            'is_verified' => $validated['is_verified'],
        ]);

        app(\App\Services\ActivityLogService::class)->log(
            auth()->id(),
            $employee,
            'employee.document.created',
            'Menambahkan dokumen ' . $document->document_type . ' untuk ' . $employee->name,
            ['document_id' => $document->id]
        );

        return redirect()->route('admin.employees.show', $employee)->with('success', 'Dokumen berhasil ditambahkan.');
    }

    public function updateDocument(Request $request, User $employee, EmployeeDocument $document)
    {
        abort_unless($document->user_id === $employee->id, 404);

        $validated = $request->validate([
            'document_type' => ['required', 'string', 'max:100'],
            'document_number' => ['nullable', 'string', 'max:100'],
            'document_file' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:4096'],
            'issued_at' => ['nullable', 'date'],
            'expired_at' => ['nullable', 'date'],
            'notes' => ['nullable', 'string'],
            'is_verified' => ['required', 'boolean'],
        ]);

        $path = $document->file_path;

        if ($request->hasFile('document_file')) {
            if ($path && Storage::disk('public')->exists($path)) {
                Storage::disk('public')->delete($path);
            }

            $path = $request->file('document_file')->store('employee_documents', 'public');
        }

        $document->update([
            'document_type' => $validated['document_type'],
            'document_number' => $validated['document_number'] ?? null,
            'file_path' => $path,
            'issued_at' => $validated['issued_at'] ?? null,
            'expired_at' => $validated['expired_at'] ?? null,
            'notes' => $validated['notes'] ?? null,
            'is_verified' => $validated['is_verified'],
        ]);

        app(\App\Services\ActivityLogService::class)->log(
            auth()->id(),
            $employee,
            'employee.document.updated',
            'Memperbarui dokumen ' . $document->document_type . ' untuk ' . $employee->name,
            ['document_id' => $document->id]
        );

        return redirect()->route('admin.employees.show', $employee)->with('success', 'Dokumen berhasil diperbarui.');
    }

    public function destroyDocument(User $employee, EmployeeDocument $document)
    {
        abort_unless($document->user_id === $employee->id, 404);

        if ($document->file_path && Storage::disk('public')->exists($document->file_path)) {
            Storage::disk('public')->delete($document->file_path);
        }

        app(\App\Services\ActivityLogService::class)->log(
            auth()->id(),
            $employee,
            'employee.document.deleted',
            'Menghapus dokumen ' . $document->document_type . ' dari ' . $employee->name,
            ['document_id' => $document->id]
        );

        $document->delete();

        return redirect()->route('admin.employees.show', $employee)->with('success', 'Dokumen berhasil dihapus.');
    }

    public function storeEducation(Request $request, User $employee)
    {
        $validated = $request->validate([
            'education_level' => ['required', 'string', 'max:100'],
            'institution_name' => ['required', 'string', 'max:255'],
            'major' => ['nullable', 'string', 'max:255'],
            'start_year' => ['nullable', 'integer', 'min:1900', 'max:2100'],
            'end_year' => ['nullable', 'integer', 'min:1900', 'max:2100'],
            'gpa' => ['nullable', 'numeric', 'min:0', 'max:4'],
            'is_latest' => ['required', 'boolean'],
        ]);

        if ($validated['is_latest']) {
            $employee->educations()->update(['is_latest' => false]);
        }

        $employee->educations()->create($validated);

        return redirect()->route('admin.employees.show', $employee)->with('success', 'Riwayat pendidikan berhasil ditambahkan.');
    }

    public function updateEducation(Request $request, User $employee, EmployeeEducation $education)
    {
        abort_unless($education->user_id === $employee->id, 404);

        $validated = $request->validate([
            'education_level' => ['required', 'string', 'max:100'],
            'institution_name' => ['required', 'string', 'max:255'],
            'major' => ['nullable', 'string', 'max:255'],
            'start_year' => ['nullable', 'integer', 'min:1900', 'max:2100'],
            'end_year' => ['nullable', 'integer', 'min:1900', 'max:2100'],
            'gpa' => ['nullable', 'numeric', 'min:0', 'max:4'],
            'is_latest' => ['required', 'boolean'],
        ]);

        if ($validated['is_latest']) {
            $employee->educations()->where('id', '!=', $education->id)->update(['is_latest' => false]);
        }

        $education->update($validated);

        return redirect()->route('admin.employees.show', $employee)->with('success', 'Riwayat pendidikan berhasil diperbarui.');
    }

    public function destroyEducation(User $employee, EmployeeEducation $education)
    {
        abort_unless($education->user_id === $employee->id, 404);
        $education->delete();

        return redirect()->route('admin.employees.show', $employee)->with('success', 'Riwayat pendidikan berhasil dihapus.');
    }

    public function storeCareerHistory(Request $request, User $employee)
    {
        $validated = $request->validate([
            'department_id' => ['nullable', 'exists:departments,id'],
            'position_id' => ['nullable', 'exists:positions,id'],
            'branch_id' => ['nullable', 'exists:branches,id'],
            'start_date' => ['required', 'date'],
            'end_date' => ['nullable', 'date'],
            'employment_status' => ['nullable', 'string', 'max:30'],
            'change_reason' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string'],
        ]);

        $employee->careerHistories()->create($validated);

        return redirect()->route('admin.employees.show', $employee)->with('success', 'Riwayat karier berhasil ditambahkan.');
    }

    public function updateCareerHistory(Request $request, User $employee, EmployeeCareerHistory $careerHistory)
    {
        abort_unless($careerHistory->user_id === $employee->id, 404);

        $validated = $request->validate([
            'department_id' => ['nullable', 'exists:departments,id'],
            'position_id' => ['nullable', 'exists:positions,id'],
            'branch_id' => ['nullable', 'exists:branches,id'],
            'start_date' => ['required', 'date'],
            'end_date' => ['nullable', 'date'],
            'employment_status' => ['nullable', 'string', 'max:30'],
            'change_reason' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string'],
        ]);

        $careerHistory->update($validated);

        return redirect()->route('admin.employees.show', $employee)->with('success', 'Riwayat karier berhasil diperbarui.');
    }

    public function destroyCareerHistory(User $employee, EmployeeCareerHistory $careerHistory)
    {
        abort_unless($careerHistory->user_id === $employee->id, 404);
        $careerHistory->delete();

        return redirect()->route('admin.employees.show', $employee)->with('success', 'Riwayat karier berhasil dihapus.');
    }

    private function formOptions(?int $excludeUserId = null): array
    {
        $managerQuery = User::query()
            ->where('role', 'employee')
            ->orderBy('name');

        if ($excludeUserId) {
            $managerQuery->where('id', '!=', $excludeUserId);
        }

        return [
            'departments' => Department::query()->where('is_active', true)->orderBy('name')->get(['id', 'name']),
            'positions' => Position::query()->where('is_active', true)->orderBy('name')->get(['id', 'name', 'grade']),
            'workShifts' => WorkShift::query()->orderBy('name')->get(['id', 'name', 'clock_in_time', 'clock_out_time']),
            'branches' => Branch::query()->where('is_active', true)->orderBy('name')->get(['id', 'name']),
            'employmentTypes' => EmploymentType::query()->where('is_active', true)->orderBy('name')->get(['id', 'name', 'category']),
            'managers' => $managerQuery->get(['id', 'name']),
        ];
    }

    private function rules(?User $employee = null): array
    {
        $employeeId = $employee?->id;

        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($employeeId)],
            'password' => [$employee ? 'nullable' : 'required', Password::min(6)],
            'employee_code' => [
                'required',
                'string',
                'max:50',
                Rule::unique('employee_profiles', 'employee_code')->ignore($employee?->profile?->id),
            ],
            'position_id' => ['required', 'exists:positions,id'],
            'department_id' => ['nullable', 'exists:departments,id'],
            'branch_id' => ['nullable', 'exists:branches,id'],
            'work_shift_id' => ['nullable', 'exists:work_shifts,id'],
            'employment_type_id' => ['nullable', 'exists:employment_types,id'],
            'manager_id' => ['nullable', 'exists:users,id'],
            'phone_number' => ['nullable', 'string', 'max:30'],
            'alternate_phone_number' => ['nullable', 'string', 'max:30'],
            'identity_number' => ['nullable', 'string', 'max:50'],
            'tax_number' => ['nullable', 'string', 'max:50'],
            'place_of_birth' => ['nullable', 'string', 'max:100'],
            'birth_date' => ['nullable', 'date'],
            'gender' => ['nullable', 'in:male,female'],
            'marital_status' => ['nullable', 'string', 'max:30'],
            'religion' => ['nullable', 'string', 'max:50'],
            'nationality' => ['nullable', 'string', 'max:50'],
            'current_address' => ['nullable', 'string'],
            'domicile_address' => ['nullable', 'string'],
            'personal_email' => ['nullable', 'email', 'max:255'],
            'joined_at' => ['nullable', 'date'],
            'contract_start_at' => ['nullable', 'date'],
            'contract_end_at' => ['nullable', 'date'],
            'employment_status' => ['required', 'string', 'max:30'],
            'bank_name' => ['nullable', 'string', 'max:100'],
            'bank_account_number' => ['nullable', 'string', 'max:50'],
            'bank_account_name' => ['nullable', 'string', 'max:100'],
            'is_active' => ['required', 'boolean'],
            'face_reference_data' => ['nullable', 'string'],
            'face_descriptor' => ['nullable', 'array', 'size:128'],
            'face_descriptor.*' => ['numeric'],
        ];
    }

    private function storeFaceReference(?string $payload, ?string $previousPath = null): ?string
    {
        if (!$payload) {
            return $previousPath;
        }

        $imageData = explode(';base64,', $payload);
        $encoded = count($imageData) === 2 ? $imageData[1] : $payload;
        $binary = base64_decode(str_replace(' ', '+', $encoded), true);

        if ($binary === false) {
            return $previousPath;
        }

        $fileName = 'face_reference_' . now()->timestamp . '_' . uniqid() . '.jpg';
        $path = 'faces/' . $fileName;

        $optimizedImage = ImageManager::gd()->read($binary)
            ->scale(width: 640)
            ->toJpeg(quality: 82);

        Storage::disk('public')->put($path, (string) $optimizedImage);

        if ($previousPath && Storage::disk('public')->exists($previousPath)) {
            Storage::disk('public')->delete($previousPath);
        }

        return $path;
    }
}
