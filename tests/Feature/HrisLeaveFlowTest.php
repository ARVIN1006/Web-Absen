<?php

namespace Tests\Feature;

use App\Models\ActivityLog;
use App\Models\Branch;
use App\Models\Department;
use App\Models\Holiday;
use App\Models\LeaveRequest;
use App\Models\LeaveType;
use App\Models\Position;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HrisLeaveFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_leave_request_counts_only_working_days(): void
    {
        $branch = Branch::create([
            'name' => 'HQ',
            'code' => 'HQ',
            'is_head_office' => true,
            'is_active' => true,
        ]);

        $department = Department::create([
            'name' => 'Human Capital',
            'code' => 'HC',
            'is_active' => true,
            'branch_id' => $branch->id,
        ]);

        $position = Position::create([
            'name' => 'HR Officer',
            'code' => 'HR_OFF',
            'salary' => 5000000,
            'allowance' => 500000,
            'overtime_rate' => 25000,
            'is_active' => true,
        ]);

        $employee = User::factory()->create([
            'role' => 'employee',
            'branch_id' => $branch->id,
            'department_id' => $department->id,
            'position_id' => $position->id,
            'is_active' => true,
        ]);

        $leaveType = LeaveType::create([
            'name' => 'Cuti Tahunan',
            'code' => 'ANNUAL',
            'max_days_per_year' => 12,
            'is_paid' => true,
            'requires_attachment' => false,
            'requires_balance' => true,
            'is_active' => true,
        ]);

        Holiday::create([
            'name' => 'Libur Perusahaan',
            'holiday_date' => '2026-05-01',
            'type' => 'company',
            'branch_id' => $branch->id,
            'is_recurring' => false,
        ]);

        $response = $this->actingAs($employee)->post(route('employee.leave-requests.store'), [
            'leave_type_id' => $leaveType->id,
            'start_date' => '2026-05-01',
            'end_date' => '2026-05-04',
            'reason' => 'Family trip',
        ]);

        $response->assertRedirect(route('employee.leave-requests.index'));

        $this->assertDatabaseHas('leave_requests', [
            'user_id' => $employee->id,
            'leave_type_id' => $leaveType->id,
            'total_days' => 1,
        ]);

        $this->assertNotNull(LeaveRequest::first()?->request_number);
    }

    public function test_leave_approval_creates_activity_log(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
            'is_active' => true,
        ]);

        $employee = User::factory()->create([
            'role' => 'employee',
            'is_active' => true,
        ]);

        $leaveType = LeaveType::create([
            'name' => 'Cuti Tahunan',
            'code' => 'ANNUAL',
            'max_days_per_year' => 12,
            'is_paid' => true,
            'requires_attachment' => false,
            'requires_balance' => true,
            'is_active' => true,
        ]);

        $leaveRequest = LeaveRequest::create([
            'user_id' => $employee->id,
            'leave_type_id' => $leaveType->id,
            'request_number' => 'LV-TEST-001',
            'start_date' => '2026-06-10',
            'end_date' => '2026-06-11',
            'total_days' => 2,
            'reason' => 'Family event',
            'status' => 'pending',
        ]);

        $response = $this->actingAs($admin)->post(route('admin.leave-requests.approve', $leaveRequest), [
            'admin_note' => 'Approved by HR',
        ]);

        $response->assertRedirect(route('admin.leave-requests.index'));

        $this->assertDatabaseHas('activity_logs', [
            'actor_id' => $admin->id,
            'subject_type' => LeaveRequest::class,
            'subject_id' => $leaveRequest->id,
            'event' => 'leave.approved',
        ]);

        $this->assertTrue(ActivityLog::where('event', 'leave.approved')->exists());
    }
}
