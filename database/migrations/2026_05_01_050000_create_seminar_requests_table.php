<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('seminar_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->cascadeOnDelete();
            $table->foreignId('thesis_guidance_id')->constrained('thesis_guidances')->cascadeOnDelete();
            $table->foreignId('supervisor_1_id')->constrained('lecturers')->cascadeOnDelete();
            $table->foreignId('supervisor_2_id')->constrained('lecturers')->cascadeOnDelete();
            $table->foreignId('examiner_1_id')->constrained('lecturers')->cascadeOnDelete();
            $table->foreignId('examiner_2_id')->constrained('lecturers')->cascadeOnDelete();
            $table->foreignId('seminar_id')->nullable()->constrained('seminars')->nullOnDelete();
            $table->string('type');
            $table->timestamp('proposed_at');
            $table->string('room')->nullable();
            $table->text('student_note')->nullable();
            $table->string('admin_status')->default('pending');
            $table->string('supervisor_1_status')->default('pending');
            $table->string('supervisor_2_status')->default('pending');
            $table->string('examiner_1_status')->default('pending');
            $table->string('examiner_2_status')->default('pending');
            $table->text('admin_note')->nullable();
            $table->text('supervisor_1_note')->nullable();
            $table->text('supervisor_2_note')->nullable();
            $table->text('examiner_1_note')->nullable();
            $table->text('examiner_2_note')->nullable();
            $table->timestamp('admin_decided_at')->nullable();
            $table->timestamp('supervisor_1_decided_at')->nullable();
            $table->timestamp('supervisor_2_decided_at')->nullable();
            $table->timestamp('examiner_1_decided_at')->nullable();
            $table->timestamp('examiner_2_decided_at')->nullable();
            $table->string('status')->default('pending');
            $table->timestamp('scheduled_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('seminar_requests');
    }
};
