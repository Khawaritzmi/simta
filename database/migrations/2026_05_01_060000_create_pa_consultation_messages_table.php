<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pa_consultation_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pa_consultation_id')->constrained('pa_consultations')->cascadeOnDelete();
            $table->string('sender_role', 20);
            $table->foreignId('sender_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->text('message');
            $table->timestamps();
        });

        DB::table('pa_consultations')->orderBy('id')->get()->each(function ($consultation): void {
            if ($consultation->student_note) {
                DB::table('pa_consultation_messages')->insert([
                    'pa_consultation_id' => $consultation->id,
                    'sender_role' => 'mahasiswa',
                    'sender_user_id' => null,
                    'message' => $consultation->student_note,
                    'created_at' => $consultation->created_at,
                    'updated_at' => $consultation->created_at,
                ]);
            }

            $lecturerMessage = trim(implode("\n\n", array_filter([
                $consultation->lecturer_note,
                $consultation->recommendation ? 'Rekomendasi: '.$consultation->recommendation : null,
            ])));

            if ($lecturerMessage !== '') {
                DB::table('pa_consultation_messages')->insert([
                    'pa_consultation_id' => $consultation->id,
                    'sender_role' => 'dosen',
                    'sender_user_id' => null,
                    'message' => $lecturerMessage,
                    'created_at' => $consultation->updated_at,
                    'updated_at' => $consultation->updated_at,
                ]);
            }
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pa_consultation_messages');
    }
};
