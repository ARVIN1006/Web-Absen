<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            if (!Schema::hasColumn('attendances', 'face_verified')) {
                $table->boolean('face_verified')->default(false)->after('status');
            }

            if (!Schema::hasColumn('attendances', 'face_match_score')) {
                $table->decimal('face_match_score', 5, 2)->default(0)->after('face_verified');
            }

            if (!Schema::hasColumn('attendances', 'distance_meters')) {
                $table->unsignedInteger('distance_meters')->default(0)->after('face_match_score');
            }
        });

        Schema::table('locations', function (Blueprint $table) {
            if (!Schema::hasColumn('locations', 'enforce_face_verification')) {
                $table->boolean('enforce_face_verification')->default(true)->after('is_active');
            }
        });
    }

    public function down(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            if (Schema::hasColumn('attendances', 'distance_meters')) {
                $table->dropColumn('distance_meters');
            }

            if (Schema::hasColumn('attendances', 'face_match_score')) {
                $table->dropColumn('face_match_score');
            }

            if (Schema::hasColumn('attendances', 'face_verified')) {
                $table->dropColumn('face_verified');
            }
        });

        Schema::table('locations', function (Blueprint $table) {
            if (Schema::hasColumn('locations', 'enforce_face_verification')) {
                $table->dropColumn('enforce_face_verification');
            }
        });
    }
};
