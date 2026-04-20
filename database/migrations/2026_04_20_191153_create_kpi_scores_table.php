<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kpi_scores', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('period'); // e.g. "Q1 2024" or "Monthly Jan 2024"
            $table->integer('attendance_score')->default(0);
            $table->integer('performance_score')->default(0);
            $table->integer('attitude_score')->default(0);
            $table->text('feedback')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kpi_scores');
    }
};
