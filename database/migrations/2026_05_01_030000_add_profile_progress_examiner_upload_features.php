<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('profile_photo_path')->nullable()->after('password');
        });

        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->string('value');
            $table->timestamps();
        });

        Schema::create('guidances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->cascadeOnDelete();
            $table->string('type', 10);
            $table->timestamp('completed_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('examiner_thesis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('thesis_guidance_id')->constrained('thesis_guidances')->cascadeOnDelete();
            $table->foreignId('examiner_user_id')->constrained('users')->cascadeOnDelete();
            $table->timestamps();
            $table->unique(['thesis_guidance_id', 'examiner_user_id']);
        });

        Schema::create('thesis_uploads', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->cascadeOnDelete();
            $table->foreignId('thesis_guidance_id')->constrained('thesis_guidances')->cascadeOnDelete();
            $table->string('category');
            $table->string('path');
            $table->string('original_name');
            $table->timestamps();
            $table->unique(['student_id', 'thesis_guidance_id', 'category']);
        });

        DB::table('settings')->insertOrIgnore([
            'key' => 'guidance_target_default',
            'value' => '16',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('thesis_uploads');
        Schema::dropIfExists('examiner_thesis');
        Schema::dropIfExists('guidances');
        Schema::dropIfExists('settings');

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('profile_photo_path');
        });
    }
};
