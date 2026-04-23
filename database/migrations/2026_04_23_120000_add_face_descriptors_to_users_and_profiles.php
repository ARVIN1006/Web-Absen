<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'face_descriptor')) {
                $table->json('face_descriptor')->nullable()->after('face_reference_path');
            }
        });

        Schema::table('employee_profiles', function (Blueprint $table) {
            if (!Schema::hasColumn('employee_profiles', 'face_descriptor')) {
                $table->json('face_descriptor')->nullable()->after('face_reference_path');
            }
        });
    }

    public function down(): void
    {
        Schema::table('employee_profiles', function (Blueprint $table) {
            if (Schema::hasColumn('employee_profiles', 'face_descriptor')) {
                $table->dropColumn('face_descriptor');
            }
        });

        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'face_descriptor')) {
                $table->dropColumn('face_descriptor');
            }
        });
    }
};
