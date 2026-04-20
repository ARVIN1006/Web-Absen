<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $blueprint) {
            $blueprint->string('phone_number')->nullable()->after('email');
            $blueprint->text('address')->nullable()->after('phone_number');
            $blueprint->enum('gender', ['male', 'female'])->nullable()->after('address');
            $blueprint->date('birth_date')->nullable()->after('gender');
            $blueprint->string('nik')->nullable()->after('birth_date'); // KTP
            $blueprint->string('npwp')->nullable()->after('nik');
            $blueprint->date('joined_at')->nullable()->after('npwp');
            $blueprint->date('contract_end_at')->nullable()->after('joined_at');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $blueprint) {
            $blueprint->dropColumn(['phone_number', 'address', 'gender', 'birth_date', 'nik', 'npwp', 'joined_at', 'contract_end_at']);
        });
    }
};
