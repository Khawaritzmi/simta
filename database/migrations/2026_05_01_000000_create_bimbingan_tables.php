<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('role')->default('dosen')->after('email');
        });

        Schema::create('lecturers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->unique()->constrained()->nullOnDelete();
            $table->string('nip')->unique();
            $table->string('nidn')->nullable();
            $table->string('certificate_number')->nullable();
            $table->string('employment_status')->nullable();
            $table->string('expertise')->nullable();
            $table->string('name');
            $table->string('gender')->nullable();
            $table->string('birth_place')->nullable();
            $table->date('birth_date')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->text('address')->nullable();
            $table->timestamps();
        });

        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->unique()->constrained()->nullOnDelete();
            $table->string('nim')->unique();
            $table->string('name');
            $table->string('program')->default('Matematika');
            $table->string('email')->nullable();
            $table->timestamps();
        });

        Schema::create('thesis_guidances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lecturer_id')->constrained()->cascadeOnDelete();
            $table->foreignId('student_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->string('status')->default('active');
            $table->string('seminar_status')->default('Belum Seminar');
            $table->unsignedInteger('progress')->default(0);
            $table->date('started_at')->nullable();
            $table->text('last_note')->nullable();
            $table->timestamps();
        });

        Schema::create('approvals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('thesis_guidance_id')->constrained()->cascadeOnDelete();
            $table->string('type');
            $table->text('description')->nullable();
            $table->string('status')->default('pending');
            $table->text('note')->nullable();
            $table->timestamp('decided_at')->nullable();
            $table->timestamps();
        });

        Schema::create('seminars', function (Blueprint $table) {
            $table->id();
            $table->foreignId('thesis_guidance_id')->constrained()->cascadeOnDelete();
            $table->string('type');
            $table->timestamp('scheduled_at');
            $table->string('room')->nullable();
            $table->string('status')->default('scheduled');
            $table->unsignedInteger('score')->nullable();
            $table->text('feedback')->nullable();
            $table->timestamps();
        });

        Schema::create('repositories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('thesis_guidance_id')->constrained()->cascadeOnDelete();
            $table->string('document_type');
            $table->string('file_name');
            $table->string('url')->nullable();
            $table->timestamps();
        });

        Schema::create('questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lecturer_id')->constrained()->cascadeOnDelete();
            $table->foreignId('student_id')->nullable()->constrained()->nullOnDelete();
            $table->string('subject');
            $table->text('question');
            $table->text('answer')->nullable();
            $table->timestamp('answered_at')->nullable();
            $table->timestamps();
        });

        Schema::create('thesis_title_databases', function (Blueprint $table) {
            $table->id();
            $table->string('submission_date')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->string('nim')->index();
            $table->string('student_name');
            $table->text('title');
            $table->string('supervisor_1')->nullable();
            $table->string('supervisor_1_nip')->nullable();
            $table->string('supervisor_2')->nullable();
            $table->string('supervisor_2_nip')->nullable();
            $table->string('document_url')->nullable();
            $table->timestamps();

            $table->unique(['nim', 'title']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('thesis_title_databases');
        Schema::dropIfExists('questions');
        Schema::dropIfExists('repositories');
        Schema::dropIfExists('seminars');
        Schema::dropIfExists('approvals');
        Schema::dropIfExists('thesis_guidances');
        Schema::dropIfExists('students');
        Schema::dropIfExists('lecturers');

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('role');
        });
    }
};
