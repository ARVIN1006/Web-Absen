<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('positions', function (Blueprint $blueprint) {
            $blueprint->decimal('overtime_rate', 15, 2)->default(20000)->after('salary');
        });
    }

    public function down(): void
    {
        Schema::table('positions', function (Blueprint $blueprint) {
            $blueprint->dropColumn('overtime_rate');
        });
    }
};
