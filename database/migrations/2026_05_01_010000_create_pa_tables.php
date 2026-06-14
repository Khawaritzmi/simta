<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pa_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lecturer_id')->constrained()->cascadeOnDelete();
            $table->foreignId('student_id')->unique()->constrained()->cascadeOnDelete();
            $table->string('academic_year')->default('2025/2026');
            $table->string('status')->default('aktif');
            $table->timestamps();
        });

        Schema::create('pa_academic_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->cascadeOnDelete();
            $table->unsignedTinyInteger('semester');
            $table->decimal('ipk', 3, 2);
            $table->unsignedSmallInteger('sks_semester');
            $table->unsignedSmallInteger('sks_total');
            $table->string('academic_status')->default('Aktif');
            $table->timestamps();

            $table->unique(['student_id', 'semester']);
        });

        Schema::create('pa_consultations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pa_assignment_id')->constrained()->cascadeOnDelete();
            $table->foreignId('student_id')->constrained()->cascadeOnDelete();
            $table->foreignId('lecturer_id')->constrained()->cascadeOnDelete();
            $table->string('topic');
            $table->text('student_note');
            $table->dateTime('requested_at')->nullable();
            $table->dateTime('scheduled_at')->nullable();
            $table->string('status')->default('diajukan');
            $table->text('lecturer_note')->nullable();
            $table->text('recommendation')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pa_consultations');
        Schema::dropIfExists('pa_academic_records');
        Schema::dropIfExists('pa_assignments');
    }
};
