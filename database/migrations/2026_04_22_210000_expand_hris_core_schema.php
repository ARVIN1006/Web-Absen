<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('branches')) {
            Schema::create('branches', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('code')->unique();
                $table->string('phone_number')->nullable();
                $table->string('email')->nullable();
                $table->text('address')->nullable();
                $table->string('city')->nullable();
                $table->string('province')->nullable();
                $table->string('postal_code')->nullable();
                $table->boolean('is_head_office')->default(false);
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('locations')) {
            Schema::create('locations', function (Blueprint $table) {
                $table->id();
                $table->foreignId('branch_id')->nullable()->constrained('branches')->nullOnDelete();
                $table->string('name');
                $table->string('code')->unique();
                $table->decimal('latitude', 10, 7);
                $table->decimal('longitude', 10, 7);
                $table->unsignedInteger('radius')->default(100);
                $table->text('address')->nullable();
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('employment_types')) {
            Schema::create('employment_types', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('code')->unique();
                $table->string('category')->default('permanent');
                $table->text('description')->nullable();
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('employee_profiles')) {
            Schema::create('employee_profiles', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();
                $table->string('employee_code')->unique();
                $table->string('identity_number')->nullable()->index();
                $table->string('tax_number')->nullable();
                $table->string('passport_number')->nullable();
                $table->string('place_of_birth')->nullable();
                $table->date('birth_date')->nullable();
                $table->string('gender', 20)->nullable();
                $table->string('marital_status', 30)->nullable();
                $table->string('religion', 50)->nullable();
                $table->string('nationality', 50)->nullable();
                $table->text('current_address')->nullable();
                $table->text('domicile_address')->nullable();
                $table->string('phone_number')->nullable();
                $table->string('alternate_phone_number')->nullable();
                $table->string('personal_email')->nullable();
                $table->string('avatar_path')->nullable();
                $table->string('face_reference_path')->nullable();
                $table->date('joined_at')->nullable();
                $table->date('probation_end_at')->nullable();
                $table->date('contract_start_at')->nullable();
                $table->date('contract_end_at')->nullable();
                $table->date('resigned_at')->nullable();
                $table->string('employment_status', 30)->default('active');
                $table->string('blood_type', 5)->nullable();
                $table->string('shirt_size', 10)->nullable();
                $table->string('bank_name')->nullable();
                $table->string('bank_account_number')->nullable();
                $table->string('bank_account_name')->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('employee_emergency_contacts')) {
            Schema::create('employee_emergency_contacts', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->cascadeOnDelete();
                $table->string('name');
                $table->string('relationship');
                $table->string('phone_number');
                $table->text('address')->nullable();
                $table->boolean('is_primary')->default(false);
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('employee_documents')) {
            Schema::create('employee_documents', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->cascadeOnDelete();
                $table->string('document_type');
                $table->string('document_number')->nullable();
                $table->string('file_path')->nullable();
                $table->date('issued_at')->nullable();
                $table->date('expired_at')->nullable();
                $table->text('notes')->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('employee_educations')) {
            Schema::create('employee_educations', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->cascadeOnDelete();
                $table->string('education_level');
                $table->string('institution_name');
                $table->string('major')->nullable();
                $table->year('start_year')->nullable();
                $table->year('end_year')->nullable();
                $table->decimal('gpa', 4, 2)->nullable();
                $table->boolean('is_latest')->default(false);
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('employee_career_histories')) {
            Schema::create('employee_career_histories', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->cascadeOnDelete();
                $table->foreignId('department_id')->nullable()->constrained()->nullOnDelete();
                $table->foreignId('position_id')->nullable()->constrained()->nullOnDelete();
                $table->foreignId('branch_id')->nullable()->constrained('branches')->nullOnDelete();
                $table->date('start_date');
                $table->date('end_date')->nullable();
                $table->string('employment_status', 30)->nullable();
                $table->string('change_reason')->nullable();
                $table->text('notes')->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('leave_balances')) {
            Schema::create('leave_balances', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->cascadeOnDelete();
                $table->foreignId('leave_type_id')->constrained()->cascadeOnDelete();
                $table->year('year');
                $table->decimal('allocated_days', 8, 2)->default(0);
                $table->decimal('used_days', 8, 2)->default(0);
                $table->decimal('reserved_days', 8, 2)->default(0);
                $table->decimal('remaining_days', 8, 2)->default(0);
                $table->timestamps();
                $table->unique(['user_id', 'leave_type_id', 'year'], 'leave_balances_unique');
            });
        }

        if (!Schema::hasTable('attendance_corrections')) {
            Schema::create('attendance_corrections', function (Blueprint $table) {
                $table->id();
                $table->foreignId('attendance_id')->nullable()->constrained()->nullOnDelete();
                $table->foreignId('user_id')->constrained()->cascadeOnDelete();
                $table->date('attendance_date');
                $table->dateTime('requested_check_in_at')->nullable();
                $table->dateTime('requested_check_out_at')->nullable();
                $table->text('reason');
                $table->string('attachment_path')->nullable();
                $table->string('status', 30)->default('pending');
                $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamp('approved_at')->nullable();
                $table->text('admin_note')->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('holidays')) {
            Schema::create('holidays', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->date('holiday_date');
                $table->string('type', 30)->default('national');
                $table->foreignId('branch_id')->nullable()->constrained('branches')->nullOnDelete();
                $table->boolean('is_recurring')->default(false);
                $table->text('notes')->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('payroll_components')) {
            Schema::create('payroll_components', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('code')->unique();
                $table->string('type', 30);
                $table->string('calculation_method', 30)->default('manual');
                $table->boolean('is_taxable')->default(true);
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('payroll_component_items')) {
            Schema::create('payroll_component_items', function (Blueprint $table) {
                $table->id();
                $table->foreignId('payroll_id')->constrained()->cascadeOnDelete();
                $table->foreignId('payroll_component_id')->constrained()->cascadeOnDelete();
                $table->decimal('amount', 15, 2)->default(0);
                $table->text('notes')->nullable();
                $table->timestamps();
            });
        }

        Schema::table('departments', function (Blueprint $table) {
            if (!Schema::hasColumn('departments', 'parent_id')) {
                $table->foreignId('parent_id')->nullable()->after('head_id')->constrained('departments')->nullOnDelete();
            }
            if (!Schema::hasColumn('departments', 'branch_id')) {
                $table->foreignId('branch_id')->nullable()->after('parent_id')->constrained('branches')->nullOnDelete();
            }
        });

        Schema::table('positions', function (Blueprint $table) {
            if (!Schema::hasColumn('positions', 'code')) {
                $table->string('code')->nullable()->after('id');
            }
            if (!Schema::hasColumn('positions', 'grade')) {
                $table->string('grade')->nullable()->after('name');
            }
            if (!Schema::hasColumn('positions', 'allowance')) {
                $table->decimal('allowance', 15, 2)->default(0)->after('salary');
            }
            if (!Schema::hasColumn('positions', 'is_active')) {
                $table->boolean('is_active')->default(true)->after('overtime_rate');
            }
        });

        Schema::table('work_shifts', function (Blueprint $table) {
            if (!Schema::hasColumn('work_shifts', 'branch_id')) {
                $table->foreignId('branch_id')->nullable()->after('id')->constrained('branches')->nullOnDelete();
            }
            if (!Schema::hasColumn('work_shifts', 'work_days')) {
                $table->json('work_days')->nullable()->after('late_tolerance_minutes');
            }
            if (!Schema::hasColumn('work_shifts', 'break_start_time')) {
                $table->time('break_start_time')->nullable()->after('clock_out_time');
            }
            if (!Schema::hasColumn('work_shifts', 'break_end_time')) {
                $table->time('break_end_time')->nullable()->after('break_start_time');
            }
        });

        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'branch_id')) {
                $table->foreignId('branch_id')->nullable()->after('department_id')->constrained('branches')->nullOnDelete();
            }
            if (!Schema::hasColumn('users', 'employment_type_id')) {
                $table->foreignId('employment_type_id')->nullable()->after('branch_id')->constrained('employment_types')->nullOnDelete();
            }
            if (!Schema::hasColumn('users', 'manager_id')) {
                $table->foreignId('manager_id')->nullable()->after('employment_type_id')->constrained('users')->nullOnDelete();
            }
            if (!Schema::hasColumn('users', 'is_active')) {
                $table->boolean('is_active')->default(true)->after('role');
            }
            if (!Schema::hasColumn('users', 'last_login_at')) {
                $table->timestamp('last_login_at')->nullable()->after('remember_token');
            }
        });

        Schema::table('attendances', function (Blueprint $table) {
            if (!Schema::hasColumn('attendances', 'attendance_date')) {
                $table->date('attendance_date')->nullable()->after('user_id');
            }
            if (!Schema::hasColumn('attendances', 'check_in_at')) {
                $table->dateTime('check_in_at')->nullable()->after('type');
            }
            if (!Schema::hasColumn('attendances', 'check_out_at')) {
                $table->dateTime('check_out_at')->nullable()->after('check_in_at');
            }
            if (!Schema::hasColumn('attendances', 'location_id')) {
                $table->foreignId('location_id')->nullable()->after('longitude')->constrained('locations')->nullOnDelete();
            }
            if (!Schema::hasColumn('attendances', 'work_minutes')) {
                $table->unsignedInteger('work_minutes')->default(0)->after('overtime_minutes');
            }
            if (!Schema::hasColumn('attendances', 'notes')) {
                $table->text('notes')->nullable()->after('work_minutes');
            }
        });

        Schema::table('leave_types', function (Blueprint $table) {
            if (!Schema::hasColumn('leave_types', 'code')) {
                $table->string('code')->nullable()->after('name');
            }
            if (!Schema::hasColumn('leave_types', 'is_paid')) {
                $table->boolean('is_paid')->default(true)->after('max_days_per_year');
            }
            if (!Schema::hasColumn('leave_types', 'requires_balance')) {
                $table->boolean('requires_balance')->default(true)->after('requires_attachment');
            }
        });

        Schema::table('leave_requests', function (Blueprint $table) {
            if (!Schema::hasColumn('leave_requests', 'request_number')) {
                $table->string('request_number')->nullable()->after('id');
            }
            if (!Schema::hasColumn('leave_requests', 'days_unit')) {
                $table->string('days_unit', 20)->default('day')->after('total_days');
            }
            if (!Schema::hasColumn('leave_requests', 'contact_number')) {
                $table->string('contact_number')->nullable()->after('reason');
            }
        });

        Schema::table('reimbursements', function (Blueprint $table) {
            if (!Schema::hasColumn('reimbursements', 'request_number')) {
                $table->string('request_number')->nullable()->after('id');
            }
            if (!Schema::hasColumn('reimbursements', 'approved_by')) {
                $table->foreignId('approved_by')->nullable()->after('status')->constrained('users')->nullOnDelete();
            }
            if (!Schema::hasColumn('reimbursements', 'approved_at')) {
                $table->timestamp('approved_at')->nullable()->after('approved_by');
            }
            if (!Schema::hasColumn('reimbursements', 'reimbursed_at')) {
                $table->date('reimbursed_at')->nullable()->after('approved_at');
            }
        });

        Schema::table('payrolls', function (Blueprint $table) {
            if (!Schema::hasColumn('payrolls', 'payroll_number')) {
                $table->string('payroll_number')->nullable()->after('id');
            }
            if (!Schema::hasColumn('payrolls', 'attendance_days')) {
                $table->unsignedInteger('attendance_days')->default(0)->after('year');
            }
            if (!Schema::hasColumn('payrolls', 'total_earnings')) {
                $table->decimal('total_earnings', 15, 2)->default(0)->after('deductions');
            }
            if (!Schema::hasColumn('payrolls', 'total_deductions')) {
                $table->decimal('total_deductions', 15, 2)->default(0)->after('total_earnings');
            }
            if (!Schema::hasColumn('payrolls', 'period_start')) {
                $table->date('period_start')->nullable()->after('paid_at');
            }
            if (!Schema::hasColumn('payrolls', 'period_end')) {
                $table->date('period_end')->nullable()->after('period_start');
            }
        });

        Schema::table('announcements', function (Blueprint $table) {
            if (!Schema::hasColumn('announcements', 'audience')) {
                $table->string('audience', 30)->default('all')->after('type');
            }
            if (!Schema::hasColumn('announcements', 'published_at')) {
                $table->timestamp('published_at')->nullable()->after('audience');
            }
            if (!Schema::hasColumn('announcements', 'expired_at')) {
                $table->timestamp('expired_at')->nullable()->after('published_at');
            }
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payroll_component_items');
        Schema::dropIfExists('payroll_components');
        Schema::dropIfExists('holidays');
        Schema::dropIfExists('attendance_corrections');
        Schema::dropIfExists('leave_balances');
        Schema::dropIfExists('employee_career_histories');
        Schema::dropIfExists('employee_educations');
        Schema::dropIfExists('employee_documents');
        Schema::dropIfExists('employee_emergency_contacts');
        Schema::dropIfExists('employee_profiles');
        Schema::dropIfExists('employment_types');
        Schema::dropIfExists('locations');
        Schema::dropIfExists('branches');
    }
};
