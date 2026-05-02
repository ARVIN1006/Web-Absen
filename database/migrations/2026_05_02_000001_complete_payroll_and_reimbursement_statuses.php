<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payroll_components', function (Blueprint $table) {
            if (!Schema::hasColumn('payroll_components', 'default_amount')) {
                $table->decimal('default_amount', 15, 2)->default(0)->after('calculation_method');
            }
        });

        $driver = DB::connection()->getDriverName();

        if ($driver === 'mysql') {
            DB::statement("ALTER TABLE payrolls MODIFY status VARCHAR(30) NOT NULL DEFAULT 'draft'");
            DB::statement("ALTER TABLE reimbursements MODIFY status VARCHAR(30) NOT NULL DEFAULT 'pending'");
        } elseif ($driver === 'pgsql') {
            DB::statement("ALTER TABLE payrolls ALTER COLUMN status TYPE VARCHAR(30)");
            DB::statement("ALTER TABLE reimbursements ALTER COLUMN status TYPE VARCHAR(30)");
        }
    }

    public function down(): void
    {
        Schema::table('payroll_components', function (Blueprint $table) {
            if (Schema::hasColumn('payroll_components', 'default_amount')) {
                $table->dropColumn('default_amount');
            }
        });
    }
};
