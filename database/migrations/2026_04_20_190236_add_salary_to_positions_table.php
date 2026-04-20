<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('positions', function (Blueprint $blueprint) {
            $blueprint->decimal('salary', 15, 2)->default(0)->after('name');
        });
    }

    public function down(): void
    {
        Schema::table('positions', function (Blueprint $blueprint) {
            $blueprint->dropColumn('salary');
        });
    }
};
