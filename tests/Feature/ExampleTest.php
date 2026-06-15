<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    protected bool $seed = true;

    public function test_landing_page_returns_a_successful_response(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }

    public function test_database_ta_page_returns_a_successful_response(): void
    {
        $this->get('/database-ta')->assertStatus(200);
        $this->get('/database-ta?q=machine%20learning')->assertStatus(200);

        $record = \Illuminate\Support\Facades\DB::table('thesis_title_databases')->first();

        $this->get("/database-ta/{$record->id}")
            ->assertStatus(200)
            ->assertSee('Dokumen PDF')
            ->assertSee($record->student_name);
    }

    public function test_admin_can_manage_database_ta(): void
    {
        $this->actingAs(User::where('email', 'admin@bimbingan.test')->firstOrFail());

        $this->get('/admin/database-ta')->assertStatus(200);

        $this->post('/admin/database-ta', [
            'submission_date' => '01 Mei 2026',
            'nim' => '9999999999',
            'student_name' => 'Mahasiswa Admin',
            'title' => 'Analisis Sistem Informasi Bimbingan Tugas Akhir',
            'email' => 'admin.input@bimbingan.test',
            'phone' => '081234567899',
            'supervisor_1' => 'Dosen Admin',
            'supervisor_1_nip' => '197001012000011001',
            'supervisor_2' => 'Dosen Pendamping',
            'supervisor_2_nip' => '198001012010011001',
            'document_url' => 'https://example.test/document',
        ])->assertRedirect('/admin/database-ta');

        $this->assertDatabaseHas('thesis_title_databases', [
            'nim' => '9999999999',
            'title' => 'Analisis Sistem Informasi Bimbingan Tugas Akhir',
        ]);

        $record = \Illuminate\Support\Facades\DB::table('thesis_title_databases')
            ->where('nim', '9999999999')
            ->first();

        $this->get("/admin/database-ta/{$record->id}/edit")->assertStatus(200);

        $this->put("/admin/database-ta/{$record->id}", [
            'submission_date' => '02 Mei 2026',
            'nim' => '9999999999',
            'student_name' => 'Mahasiswa Admin Update',
            'title' => 'Analisis Sistem Informasi Bimbingan Tugas Akhir Update',
            'email' => 'admin.update@bimbingan.test',
            'phone' => '081234567800',
            'supervisor_1' => 'Dosen Admin Update',
            'supervisor_1_nip' => '197001012000011001',
            'supervisor_2' => 'Dosen Pendamping Update',
            'supervisor_2_nip' => '198001012010011001',
            'document_url' => 'https://example.test/document-update',
        ])->assertRedirect('/admin/database-ta');

        $this->assertDatabaseHas('thesis_title_databases', [
            'id' => $record->id,
            'student_name' => 'Mahasiswa Admin Update',
            'title' => 'Analisis Sistem Informasi Bimbingan Tugas Akhir Update',
        ]);

        $this->delete("/admin/database-ta/{$record->id}")->assertRedirect('/admin/database-ta');

        $this->assertDatabaseMissing('thesis_title_databases', [
            'id' => $record->id,
        ]);
    }

    public function test_admin_can_run_collective_database_update_and_see_history(): void
    {
        $this->actingAs(User::where('email', 'admin@bimbingan.test')->firstOrFail());

        $this->get('/admin/update-kolektif')
            ->assertStatus(200)
            ->assertSee('Update Database Kolektif');

        $this->post('/admin/update-kolektif', [
            'target' => 'students',
            'mode' => 'update_existing',
            'data_text' => "nim,name,program,email\nH011201001,Aulia Rahmadani Updated,Pendidikan Matematika,aulia.updated@example.test\nH011209999,Mahasiswa Kolektif Baru,Matematika,kolektif@example.test",
        ])->assertRedirect('/admin/update-kolektif');

        $this->assertDatabaseHas('students', [
            'nim' => 'H011201001',
            'name' => 'Aulia Rahmadani Updated',
            'program' => 'Pendidikan Matematika',
        ]);

        $this->assertDatabaseHas('students', [
            'nim' => 'H011209999',
            'name' => 'Mahasiswa Kolektif Baru',
        ]);

        $this->assertDatabaseHas('update_histories', [
            'target_table' => 'students',
            'mode' => 'update',
        ]);

        $this->assertDatabaseHas('update_histories', [
            'target_table' => 'students',
            'mode' => 'insert',
        ]);

        $this->get('/admin/update-kolektif')
            ->assertStatus(200)
            ->assertSee('Riwayat Update')
            ->assertSee('students');
    }

    public function test_admin_can_manage_bimbingan_pa_data(): void
    {
        $this->actingAs(User::where('email', 'admin@bimbingan.test')->firstOrFail());

        $this->get('/admin/bimbingan-pa')->assertStatus(200);

        $studentId = \Illuminate\Support\Facades\DB::table('students')->insertGetId([
            'user_id' => null,
            'nim' => 'H011209001',
            'name' => 'Mahasiswa PA Baru',
            'program' => 'Matematika',
            'email' => 'pa.baru@student.test',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->post('/admin/bimbingan-pa/assignments', [
            'lecturer_id' => 1,
            'student_id' => $studentId,
            'academic_year' => '2026/2027',
            'status' => 'aktif',
        ])->assertRedirect('/admin/bimbingan-pa');

        $assignment = \Illuminate\Support\Facades\DB::table('pa_assignments')->where('student_id', $studentId)->first();

        $this->assertDatabaseHas('pa_assignments', [
            'id' => $assignment->id,
            'academic_year' => '2026/2027',
        ]);

        $this->put("/admin/bimbingan-pa/assignments/{$assignment->id}", [
            'lecturer_id' => 1,
            'student_id' => $studentId,
            'academic_year' => '2027/2028',
            'status' => 'nonaktif',
        ])->assertRedirect('/admin/bimbingan-pa');

        $this->post('/admin/bimbingan-pa/records', [
            'student_id' => $studentId,
            'semester' => 1,
            'ipk' => 3.25,
            'sks_semester' => 20,
            'sks_total' => 20,
            'academic_status' => 'Aktif',
        ])->assertRedirect('/admin/bimbingan-pa');

        $record = \Illuminate\Support\Facades\DB::table('pa_academic_records')->where('student_id', $studentId)->first();

        $this->put("/admin/bimbingan-pa/records/{$record->id}", [
            'student_id' => $studentId,
            'semester' => 1,
            'ipk' => 3.50,
            'sks_semester' => 22,
            'sks_total' => 22,
            'academic_status' => 'Aktif',
        ])->assertRedirect('/admin/bimbingan-pa');

        $this->post('/admin/bimbingan-pa/consultations', [
            'pa_assignment_id' => $assignment->id,
            'topic' => 'Konsultasi akademik awal',
            'student_note' => 'Mahasiswa meminta arahan pengisian KRS.',
            'requested_at' => '2026-05-10 09:00:00',
            'scheduled_at' => '2026-05-10 10:00:00',
            'status' => 'dijadwalkan',
            'lecturer_note' => null,
            'recommendation' => null,
        ])->assertRedirect('/admin/bimbingan-pa');

        $consultation = \Illuminate\Support\Facades\DB::table('pa_consultations')->where('student_id', $studentId)->first();

        $this->put("/admin/bimbingan-pa/consultations/{$consultation->id}", [
            'pa_assignment_id' => $assignment->id,
            'topic' => 'Konsultasi akademik awal',
            'student_note' => 'Mahasiswa meminta arahan pengisian KRS.',
            'requested_at' => '2026-05-10 09:00:00',
            'scheduled_at' => '2026-05-10 10:00:00',
            'status' => 'selesai',
            'lecturer_note' => 'KRS sudah sesuai.',
            'recommendation' => 'Lanjutkan pengisian KRS sesuai paket semester.',
        ])->assertRedirect('/admin/bimbingan-pa');

        $this->assertDatabaseHas('pa_consultations', [
            'id' => $consultation->id,
            'status' => 'selesai',
            'lecturer_note' => 'KRS sudah sesuai.',
        ]);

        $this->delete("/admin/bimbingan-pa/consultations/{$consultation->id}")->assertRedirect('/admin/bimbingan-pa');
        $this->delete("/admin/bimbingan-pa/records/{$record->id}")->assertRedirect('/admin/bimbingan-pa');
        $this->delete("/admin/bimbingan-pa/assignments/{$assignment->id}")->assertRedirect('/admin/bimbingan-pa');

        $this->assertDatabaseMissing('pa_assignments', ['id' => $assignment->id]);
        $this->assertDatabaseMissing('pa_academic_records', ['id' => $record->id]);
        $this->assertDatabaseMissing('pa_consultations', ['id' => $consultation->id]);
    }

    public function test_admin_creates_seminar_schedule_request_but_can_edit_existing_schedule(): void
    {
        $this->actingAs(User::where('email', 'admin@bimbingan.test')->firstOrFail());

        $this->get('/admin/seminar-ujian')
            ->assertStatus(200)
            ->assertSee('Buat Jadwal untuk Approval Dosen');

        $this->from('/admin/seminar-ujian')->post('/admin/seminar-ujian', [
            'thesis_guidance_id' => 1,
            'type' => 'Seminar Hasil',
            'proposed_at' => '2026-06-20 09:30:00',
            'room' => 'Ruang Seminar Baru',
            'note' => 'Mohon validasi jadwal seminar hasil.',
        ])->assertRedirect('/admin/seminar-ujian');

        $this->assertDatabaseMissing('seminars', [
            'room' => 'Ruang Seminar Baru',
        ]);

        $this->assertDatabaseHas('seminar_requests', [
            'thesis_guidance_id' => 1,
            'type' => 'Seminar Hasil',
            'room' => 'Ruang Seminar Baru',
            'admin_status' => 'approved',
            'status' => 'pending',
        ]);

        $seminar = \Illuminate\Support\Facades\DB::table('seminars')->where('id', 1)->first();

        $this->get("/admin/seminar-ujian/{$seminar->id}/edit")->assertStatus(200);

        $this->put("/admin/seminar-ujian/{$seminar->id}", [
            'thesis_guidance_id' => 1,
            'type' => 'Seminar Hasil',
            'scheduled_at' => '2026-06-21 10:00:00',
            'room' => 'Lab Komputasi Baru',
            'status' => 'done',
            'score' => 90,
            'feedback' => 'Jadwal dan nilai diperbarui admin.',
        ])->assertRedirect('/admin/seminar-ujian');

        $this->assertDatabaseHas('seminars', [
            'id' => $seminar->id,
            'type' => 'Seminar Hasil',
            'room' => 'Lab Komputasi Baru',
            'status' => 'graded',
            'score' => 90,
        ]);

        $this->assertDatabaseHas('thesis_guidances', [
            'id' => 1,
            'seminar_status' => 'Seminar Hasil',
        ]);

        $this->delete("/admin/seminar-ujian/{$seminar->id}")->assertRedirect('/admin/seminar-ujian');

        $this->assertDatabaseMissing('seminars', [
            'id' => $seminar->id,
        ]);
    }

    public function test_admin_cannot_schedule_seminar_before_all_guidance_approvals(): void
    {
        $this->actingAs(User::where('email', 'admin@bimbingan.test')->firstOrFail());

        $guidanceId = \Illuminate\Support\Facades\DB::table('thesis_guidances')->insertGetId([
            'lecturer_id' => 1,
            'student_id' => 3,
            'title' => 'Pengajuan Seminar Tanpa Persetujuan Lengkap',
            'status' => 'active',
            'seminar_status' => 'Belum Seminar',
            'progress' => 0,
            'started_at' => now()->toDateString(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->from('/admin/seminar-ujian')->post('/admin/seminar-ujian', [
            'thesis_guidance_id' => $guidanceId,
            'type' => 'Seminar Proposal',
            'proposed_at' => '2026-07-20 09:30:00',
            'room' => 'Ruang Seminar Ditolak',
        ])
            ->assertRedirect('/admin/seminar-ujian')
            ->assertSessionHasErrors('thesis_guidance_id');

        $this->assertDatabaseMissing('seminars', [
            'room' => 'Ruang Seminar Ditolak',
        ]);
    }

    public function test_rejected_guidance_request_requires_reason(): void
    {
        $user = User::create([
            'name' => 'Mahasiswa Alasan',
            'email' => 'mahasiswa.alasan@bimbingan.test',
            'role' => 'mahasiswa',
            'password' => Hash::make('password'),
        ]);

        \Illuminate\Support\Facades\DB::table('students')->insert([
            'user_id' => $user->id,
            'nim' => 'H011209888',
            'name' => 'Mahasiswa Alasan',
            'program' => 'Matematika',
            'email' => 'mahasiswa.alasan@bimbingan.test',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->actingAs($user);

        $this->post('/mahasiswa/bimbingan-ta/pengajuan', [
            'title' => 'Topik yang Perlu Alasan Penolakan',
            'supervisor_1_id' => 1,
            'supervisor_2_id' => 2,
            'examiner_1_id' => 3,
            'examiner_2_id' => 4,
        ])->assertRedirect('/mahasiswa/ta/pengajuan');

        $requestId = \Illuminate\Support\Facades\DB::table('thesis_guidance_requests')
            ->where('title', 'Topik yang Perlu Alasan Penolakan')
            ->value('id');

        $this->actingAs(User::where('email', 'admin@bimbingan.test')->firstOrFail());

        $this->from('/admin/profil')->post("/admin/pengajuan-ta/{$requestId}/persetujuan", [
            'status' => 'rejected',
        ])
            ->assertRedirect('/admin/profil')
            ->assertSessionHasErrors('note');

        $this->post("/admin/pengajuan-ta/{$requestId}/persetujuan", [
            'status' => 'rejected',
            'note' => 'Judul perlu dipersempit sebelum diajukan kembali.',
        ])->assertRedirect('/admin/profil');

        $this->assertDatabaseHas('thesis_guidance_requests', [
            'id' => $requestId,
            'status' => 'rejected',
            'admin_status' => 'rejected',
            'admin_note' => 'Judul perlu dipersempit sebelum diajukan kembali.',
        ]);

        $this->actingAs($user);
        $this->get('/mahasiswa/ta/pengajuan')
            ->assertStatus(200)
            ->assertSee('Alasan')
            ->assertSee('Judul perlu dipersempit sebelum diajukan kembali.');
    }

    public function test_admin_seminar_schedule_request_requires_all_lecturer_approvals(): void
    {
        $this->actingAs(User::where('email', 'mahasiswa@bimbingan.test')->firstOrFail());

        $this->from('/mahasiswa/ta/tugas-akhir-saya')->post('/mahasiswa/seminar-ujian/pengajuan', [
            'thesis_guidance_id' => 1,
            'type' => 'Seminar Hasil',
            'proposed_at' => '2026-08-01 09:00:00',
            'room' => 'Ruang Seminar Validasi',
            'student_note' => 'Mohon validasi jadwal seminar hasil.',
        ])
            ->assertRedirect('/mahasiswa/ta/tugas-akhir-saya')
            ->assertSessionHasErrors('thesis_guidance_id');

        $this->assertDatabaseMissing('seminar_requests', [
            'room' => 'Ruang Seminar Validasi',
        ]);

        $this->actingAs(User::where('email', 'admin@bimbingan.test')->firstOrFail());

        $this->post('/admin/seminar-ujian', [
            'thesis_guidance_id' => 1,
            'type' => 'Seminar Hasil',
            'proposed_at' => '2026-08-01 09:00:00',
            'room' => 'Ruang Seminar Validasi',
            'note' => 'Jadwal dibuat oleh admin untuk divalidasi dosen.',
        ])->assertRedirect('/admin/seminar-ujian');

        $requestId = \Illuminate\Support\Facades\DB::table('seminar_requests')
            ->where('room', 'Ruang Seminar Validasi')
            ->value('id');

        $this->assertNotNull($requestId);
        $this->assertDatabaseHas('seminar_requests', [
            'id' => $requestId,
            'status' => 'pending',
            'admin_status' => 'approved',
        ]);

        foreach ([2, 3, 4] as $lecturerId) {
            $userId = \Illuminate\Support\Facades\DB::table('users')->insertGetId([
                'name' => "Dosen Seminar {$lecturerId}",
                'email' => "seminar{$lecturerId}@bimbingan.test",
                'role' => 'dosen',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            \Illuminate\Support\Facades\DB::table('lecturers')->where('id', $lecturerId)->update([
                'user_id' => $userId,
                'updated_at' => now(),
            ]);
        }

        foreach ([1, 2, 3, 4] as $lecturerId) {
            $userId = \Illuminate\Support\Facades\DB::table('lecturers')->where('id', $lecturerId)->value('user_id');

            $this->actingAs(User::findOrFail($userId));

            if ($lecturerId === 1) {
                $this->from('/dosen/seminar-ujian')->post("/dosen/seminar-ujian/pengajuan/{$requestId}/persetujuan", [
                    'status' => 'rejected',
                ])
                    ->assertRedirect('/dosen/seminar-ujian')
                    ->assertSessionHasErrors('note');
            }

            $this->post("/dosen/seminar-ujian/pengajuan/{$requestId}/persetujuan", [
                'status' => 'approved',
            ])->assertRedirect('/dosen/seminar-ujian');
        }

        $this->assertDatabaseHas('seminar_requests', [
            'id' => $requestId,
            'status' => 'approved',
            'admin_status' => 'approved',
            'supervisor_1_status' => 'approved',
            'supervisor_2_status' => 'approved',
            'examiner_1_status' => 'approved',
            'examiner_2_status' => 'approved',
        ]);

        $this->assertDatabaseHas('seminars', [
            'thesis_guidance_id' => 1,
            'type' => 'Seminar Hasil',
            'room' => 'Ruang Seminar Validasi',
            'status' => 'scheduled',
        ]);
    }

    public function test_admin_can_update_guidance_target_and_export_report(): void
    {
        $this->actingAs(User::where('email', 'admin@bimbingan.test')->firstOrFail());

        $this->get('/admin/settings')->assertStatus(200);

        $this->put('/admin/settings', [
            'guidance_target_default' => 12,
        ])->assertRedirect('/admin/settings');

        $this->assertDatabaseHas('settings', [
            'key' => 'guidance_target_default',
            'value' => '12',
        ]);

        $response = $this->get('/admin/export-report')
            ->assertStatus(200)
            ->assertHeader('content-type', 'text/csv; charset=UTF-8');

        $this->assertStringContainsString('attachment; filename=laporan-simta-', $response->headers->get('content-disposition'));
        $this->assertStringContainsString('Nama Mahasiswa', $response->getContent());
        $this->assertStringContainsString('Progress PA', $response->getContent());
        $this->assertStringContainsString('Progress TA', $response->getContent());
        $this->assertStringContainsString('Aulia Rahmadani', $response->getContent());
    }

    public function test_lecturer_can_export_their_ta_and_pa_guidance_report(): void
    {
        $this->actingAs(User::where('email', 'dosen@bimbingan.test')->firstOrFail());

        $response = $this->get('/dosen/export-report')
            ->assertStatus(200)
            ->assertHeader('content-type', 'text/csv; charset=UTF-8');

        $this->assertStringContainsString('attachment; filename=laporan-bimbingan-dosen-', $response->headers->get('content-disposition'));
        $this->assertStringContainsString('Nama Mahasiswa', $response->getContent());
        $this->assertStringContainsString('Dosen PA', $response->getContent());
        $this->assertStringContainsString('Dosen TA', $response->getContent());
        $this->assertStringContainsString('Progress PA', $response->getContent());
        $this->assertStringContainsString('Progress TA', $response->getContent());
        $this->assertStringContainsString('Peran Dosen', $response->getContent());
        $this->assertStringContainsString('Aulia Rahmadani', $response->getContent());
    }

    public function test_student_can_upload_thesis_pdf_after_approval(): void
    {
        Storage::fake('public');
        $this->actingAs(User::where('email', 'mahasiswa@bimbingan.test')->firstOrFail());

        $this->post('/mahasiswa/ta/uploads', [
            'thesis_guidance_id' => 1,
            'category' => 'proposal',
            'file' => UploadedFile::fake()->create('proposal.pdf', 120, 'application/pdf'),
        ])->assertRedirect('/mahasiswa/ta/tugas-akhir-saya');

        $this->assertDatabaseHas('thesis_uploads', [
            'student_id' => 1,
            'thesis_guidance_id' => 1,
            'category' => 'proposal',
            'original_name' => 'proposal.pdf',
        ]);

        Storage::disk('public')->assertExists('uploads/1/proposal.pdf');
    }

    public function test_lecturer_can_open_student_uploaded_documents_from_seminar_page(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('uploads/1/proposal.pdf', 'PDF');

        \Illuminate\Support\Facades\DB::table('thesis_uploads')->updateOrInsert(
            [
                'student_id' => 1,
                'thesis_guidance_id' => 1,
                'category' => 'proposal',
            ],
            [
                'path' => 'uploads/1/proposal.pdf',
                'original_name' => 'proposal-aulia.pdf',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        );
        $uploadId = \Illuminate\Support\Facades\DB::table('thesis_uploads')
            ->where('student_id', 1)
            ->where('thesis_guidance_id', 1)
            ->where('category', 'proposal')
            ->value('id');

        $this->actingAs(User::where('email', 'dosen@bimbingan.test')->firstOrFail());

        $this->get('/dosen/seminar-ujian')
            ->assertStatus(200)
            ->assertSee('Lihat dokumen')
            ->assertSee('Isi/Edit Penilaian')
            ->assertSee('Catatan penilaian lengkap')
            ->assertSee('Proposal')
            ->assertSee('proposal-aulia.pdf')
            ->assertSee("/ta/uploads/{$uploadId}", false);

        $this->get("/ta/uploads/{$uploadId}")
            ->assertStatus(200)
            ->assertHeader('content-type', 'application/pdf')
            ->assertHeader('content-disposition', 'inline; filename="proposal-aulia.pdf"')
            ->assertSee('PDF', false);
    }

    public function test_student_profile_photo_upload_is_displayed_from_public_photo_route(): void
    {
        Storage::fake('public');
        $user = User::where('email', 'mahasiswa@bimbingan.test')->firstOrFail();

        $this->actingAs($user);

        $this->post('/mahasiswa/profil', [
            'photo' => UploadedFile::fake()->image('avatar.jpg', 160, 160),
        ])->assertRedirect('/mahasiswa/profil');

        $photoPath = $user->fresh()->profile_photo_path;

        $this->assertNotNull($photoPath);
        Storage::disk('public')->assertExists($photoPath);

        $this->get('/mahasiswa/profil')
            ->assertStatus(200)
            ->assertSee('/profile-photos/'.basename($photoPath), false);

        $this->get(route('profile-photos.show', ['file' => basename($photoPath)]))
            ->assertStatus(200)
            ->assertHeader('content-type', 'image/jpeg');
    }

    public function test_login_page_returns_a_successful_response(): void
    {
        $this->get('/dosen/login')->assertStatus(200);
        $this->get('/mahasiswa/login')->assertStatus(200);
        $this->get('/admin/login')->assertStatus(200);
    }

    public function test_register_page_returns_a_successful_response(): void
    {
        $this->get('/dosen/register')->assertStatus(200);
        $this->get('/mahasiswa/register')->assertStatus(200);
    }

    public function test_main_pages_return_successful_responses(): void
    {
        $this->actingAs(User::where('email', 'dosen@bimbingan.test')->firstOrFail());

        foreach ([
            '/dosen',
            '/dosen/profil',
            '/dosen/ta/pengajuan',
            '/dosen/ta/bimbingan-saya',
            '/dosen/pa',
            '/dosen/seminar-ujian',
            '/dosen/repository',
            '/dosen/qa',
            '/dosen/manual-aplikasi',
        ] as $path) {
            $this->get($path)->assertStatus(200);
        }

        $this->get('/dosen/bimbingan-ta')->assertRedirect('/dosen/ta/bimbingan-saya');
        $this->get('/dosen/persetujuan')->assertRedirect('/dosen/ta/pengajuan');
    }

    public function test_mahasiswa_pages_return_successful_responses(): void
    {
        $this->actingAs(User::where('email', 'mahasiswa@bimbingan.test')->firstOrFail());

        foreach ([
            '/mahasiswa',
            '/mahasiswa/profil',
            '/mahasiswa/ta/pengajuan',
            '/mahasiswa/ta/tugas-akhir-saya',
            '/mahasiswa/pa',
            '/mahasiswa/repository',
            '/mahasiswa/qa',
        ] as $path) {
            $this->get($path)->assertStatus(200);
        }

        $this->get('/mahasiswa/bimbingan-ta')->assertRedirect('/mahasiswa/ta/tugas-akhir-saya');
    }

    public function test_pa_pages_return_successful_responses_for_each_role(): void
    {
        $this->actingAs(User::where('email', 'dosen@bimbingan.test')->firstOrFail());
        $this->get('/pa/dosen')->assertRedirect('/dosen/pa');

        $this->actingAs(User::where('email', 'mahasiswa@bimbingan.test')->firstOrFail());
        $this->get('/pa/mahasiswa')->assertRedirect('/mahasiswa/pa');
    }

    public function test_pa_login_links_redirect_to_unified_profiles(): void
    {
        $this->post('/dosen/login', [
            'email' => 'dosen@bimbingan.test',
            'password' => 'password',
            'next' => 'pa.dosen.dashboard',
        ])->assertRedirect('/dosen/profil');

        $this->post('/logout');

        $this->post('/mahasiswa/login', [
            'email' => 'mahasiswa@bimbingan.test',
            'password' => 'password',
            'next' => 'pa.mahasiswa.dashboard',
        ])->assertRedirect('/mahasiswa/profil');
    }

    public function test_authenticated_user_login_links_return_to_active_portal(): void
    {
        $this->actingAs(User::where('email', 'admin@bimbingan.test')->firstOrFail());

        $this->get('/dosen/login')->assertRedirect('/portal');
        $this->get('/mahasiswa/login')->assertRedirect('/portal');
        $this->get('/portal')->assertRedirect('/admin/profil');
    }

    public function test_student_can_submit_pa_consultation(): void
    {
        $this->actingAs(User::where('email', 'mahasiswa@bimbingan.test')->firstOrFail());

        $this->post('/pa/mahasiswa/konsultasi', [
            'topic' => 'Konsultasi pengambilan mata kuliah pilihan',
            'student_note' => 'Saya ingin memastikan mata kuliah pilihan sesuai dengan rencana tugas akhir.',
            'requested_at' => '2026-05-06 09:00:00',
        ])->assertRedirect('/mahasiswa/pa');

        $this->assertDatabaseHas('pa_consultations', [
            'student_id' => 1,
            'lecturer_id' => 1,
            'topic' => 'Konsultasi pengambilan mata kuliah pilihan',
            'status' => 'diajukan',
        ]);

        $consultationId = \Illuminate\Support\Facades\DB::table('pa_consultations')
            ->where('topic', 'Konsultasi pengambilan mata kuliah pilihan')
            ->value('id');

        $this->assertDatabaseHas('pa_consultation_messages', [
            'pa_consultation_id' => $consultationId,
            'sender_role' => 'mahasiswa',
            'message' => 'Saya ingin memastikan mata kuliah pilihan sesuai dengan rencana tugas akhir.',
        ]);
    }

    public function test_lecturer_can_save_pa_guidance_note(): void
    {
        $this->actingAs(User::where('email', 'dosen@bimbingan.test')->firstOrFail());

        $consultation = \Illuminate\Support\Facades\DB::table('pa_consultations')->where('student_id', 1)->first();

        $this->post("/pa/dosen/konsultasi/{$consultation->id}", [
            'status' => 'selesai',
            'scheduled_at' => '2026-05-03 10:00:00',
            'lecturer_note' => 'Mahasiswa disarankan menjaga beban SKS tetap realistis.',
            'recommendation' => 'Ambil maksimal 20 SKS dan prioritaskan mata kuliah wajib.',
        ])->assertRedirect('/dosen/pa');

        $this->assertDatabaseHas('pa_consultations', [
            'id' => $consultation->id,
            'status' => 'selesai',
            'lecturer_note' => 'Mahasiswa disarankan menjaga beban SKS tetap realistis.',
        ]);

        $this->post("/pa/dosen/konsultasi/{$consultation->id}/pesan", [
            'status' => 'selesai',
            'scheduled_at' => '2026-05-03 10:00:00',
            'message' => 'Silakan lanjutkan rencana KRS tersebut.',
            'recommendation' => 'Pantau prasyarat mata kuliah pilihan.',
        ])->assertRedirect('/dosen/pa');

        $this->assertDatabaseHas('pa_consultation_messages', [
            'pa_consultation_id' => $consultation->id,
            'sender_role' => 'dosen',
            'message' => "Silakan lanjutkan rencana KRS tersebut.\n\nRekomendasi: Pantau prasyarat mata kuliah pilihan.",
        ]);
    }

    public function test_pa_chat_authorization_uses_profile_ownership_not_only_role_string(): void
    {
        $user = User::create([
            'name' => 'Dosen PA Examiner',
            'email' => 'dosen.pa.examiner@bimbingan.test',
            'role' => 'examiner',
            'password' => Hash::make('password'),
        ]);

        \Illuminate\Support\Facades\DB::table('lecturers')->where('id', 1)->update([
            'user_id' => $user->id,
            'updated_at' => now(),
        ]);

        $consultation = \Illuminate\Support\Facades\DB::table('pa_consultations')->where('lecturer_id', 1)->first();

        $this->actingAs($user);

        $this->post("/pa/dosen/konsultasi/{$consultation->id}/pesan", [
            'status' => 'dijadwalkan',
            'scheduled_at' => '2026-05-04 09:00:00',
            'message' => 'Pesan PA tetap bisa dikirim selama dosen memiliki profil dan konsultasi ini.',
            'recommendation' => null,
        ])->assertRedirect('/dosen/pa');

        $this->assertDatabaseHas('pa_consultation_messages', [
            'pa_consultation_id' => $consultation->id,
            'sender_role' => 'dosen',
            'sender_user_id' => $user->id,
            'message' => 'Pesan PA tetap bisa dikirim selama dosen memiliki profil dan konsultasi ini.',
        ]);
    }

    public function test_user_can_login_and_logout(): void
    {
        $this->post('/dosen/login', [
            'email' => 'dosen@bimbingan.test',
            'password' => 'password',
        ])->assertRedirect('/dosen/profil');

        $this->assertAuthenticated();

        $this->post('/logout')->assertRedirect('/');

        $this->assertGuest();
    }

    public function test_all_roles_can_change_password(): void
    {
        foreach ([
            'dosen@bimbingan.test',
            'mahasiswa@bimbingan.test',
            'admin@bimbingan.test',
        ] as $email) {
            $user = User::where('email', $email)->firstOrFail();
            $newPassword = "password-baru-{$user->id}";

            $this->actingAs($user);
            $this->get('/password')
                ->assertStatus(200)
                ->assertSee('Ubah password akun');

            $this->from('/password')->put('/password', [
                'current_password' => 'password',
                'password' => $newPassword,
                'password_confirmation' => $newPassword,
            ])->assertRedirect('/password');

            $this->assertTrue(Hash::check($newPassword, $user->fresh()->password));

            $this->post('/logout');
        }
    }

    public function test_user_can_register_with_lecturer_profile(): void
    {
        $this->post('/dosen/register', [
            'name' => 'Dosen Baru',
            'email' => 'dosen.baru@bimbingan.test',
            'password' => 'password-baru',
            'password_confirmation' => 'password-baru',
            'nip' => '199001012026051001',
            'nidn' => '0011223344',
            'employment_status' => 'Dosen Tetap',
            'expertise' => 'Aljabar',
            'gender' => 'Perempuan',
            'birth_place' => 'Makassar',
            'birth_date' => '1990-01-01',
            'phone' => '081111111111',
            'address' => 'Makassar',
        ])->assertRedirect('/dosen/profil');

        $this->assertAuthenticated();
        $this->assertDatabaseHas('users', [
            'email' => 'dosen.baru@bimbingan.test',
        ]);
        $this->assertDatabaseHas('lecturers', [
            'email' => 'dosen.baru@bimbingan.test',
            'nip' => '199001012026051001',
            'expertise' => 'Aljabar',
        ]);
    }

    public function test_student_can_register_with_student_profile(): void
    {
        $this->post('/mahasiswa/register', [
            'name' => 'Mahasiswa Baru',
            'email' => 'mahasiswa.baru@bimbingan.test',
            'password' => 'password-baru',
            'password_confirmation' => 'password-baru',
            'nim' => 'H011209999',
            'program' => 'Matematika',
        ])->assertRedirect('/mahasiswa/profil');

        $this->assertAuthenticated();
        $this->assertDatabaseHas('users', [
            'email' => 'mahasiswa.baru@bimbingan.test',
            'role' => 'mahasiswa',
        ]);
        $this->assertDatabaseHas('students', [
            'email' => 'mahasiswa.baru@bimbingan.test',
            'nim' => 'H011209999',
        ]);
    }

    public function test_student_can_submit_ta_guidance_request(): void
    {
        $user = User::create([
            'name' => 'Mahasiswa Pengajuan Baru',
            'email' => 'mahasiswa.pengajuan@bimbingan.test',
            'role' => 'mahasiswa',
            'password' => Hash::make('password'),
        ]);

        $studentId = \Illuminate\Support\Facades\DB::table('students')->insertGetId([
            'user_id' => $user->id,
            'nim' => 'H011209887',
            'name' => 'Mahasiswa Pengajuan Baru',
            'program' => 'Matematika',
            'email' => 'mahasiswa.pengajuan@bimbingan.test',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->actingAs($user);

        $this->post('/mahasiswa/bimbingan-ta/pengajuan', [
            'title' => 'Analisis Numerik untuk Optimasi Jadwal Bimbingan',
            'supervisor_1_id' => 1,
            'supervisor_2_id' => 2,
            'examiner_1_id' => 3,
            'examiner_2_id' => 4,
        ])->assertRedirect('/mahasiswa/ta/pengajuan');

        $this->assertDatabaseHas('thesis_guidance_requests', [
            'student_id' => $studentId,
            'title' => 'Analisis Numerik untuk Optimasi Jadwal Bimbingan',
            'status' => 'pending',
            'admin_status' => 'pending',
        ]);
    }

    public function test_ta_guidance_request_requires_admin_and_all_lecturer_approvals(): void
    {
        $title = 'Model Prediksi Kesiapan Seminar Tugas Akhir';

        $studentUser = User::create([
            'name' => 'Mahasiswa Approval Baru',
            'email' => 'mahasiswa.approval@bimbingan.test',
            'role' => 'mahasiswa',
            'password' => Hash::make('password'),
        ]);

        $studentId = \Illuminate\Support\Facades\DB::table('students')->insertGetId([
            'user_id' => $studentUser->id,
            'nim' => 'H011209886',
            'name' => 'Mahasiswa Approval Baru',
            'program' => 'Matematika',
            'email' => 'mahasiswa.approval@bimbingan.test',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->actingAs($studentUser);
        $this->post('/mahasiswa/bimbingan-ta/pengajuan', [
            'title' => $title,
            'supervisor_1_id' => 1,
            'supervisor_2_id' => 2,
            'examiner_1_id' => 3,
            'examiner_2_id' => 4,
        ]);

        $requestId = \Illuminate\Support\Facades\DB::table('thesis_guidance_requests')->where('title', $title)->value('id');

        $this->actingAs(User::where('email', 'admin@bimbingan.test')->firstOrFail());
        $this->post("/admin/pengajuan-ta/{$requestId}/persetujuan", [
            'status' => 'approved',
        ])->assertRedirect('/admin/profil');

        foreach ([2, 3, 4] as $lecturerId) {
            $userId = \Illuminate\Support\Facades\DB::table('users')->insertGetId([
                'name' => "Dosen Penguji {$lecturerId}",
                'email' => "dosen{$lecturerId}@bimbingan.test",
                'role' => 'dosen',
                'password' => \Illuminate\Support\Facades\Hash::make('password'),
                'email_verified_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            \Illuminate\Support\Facades\DB::table('lecturers')->where('id', $lecturerId)->update([
                'user_id' => $userId,
                'updated_at' => now(),
            ]);
        }

        foreach ([1, 2, 3, 4] as $lecturerId) {
            $userId = \Illuminate\Support\Facades\DB::table('lecturers')->where('id', $lecturerId)->value('user_id');

            $this->actingAs(User::findOrFail($userId));
            $this->post("/dosen/pengajuan-ta/{$requestId}/persetujuan", [
                'status' => 'approved',
            ])->assertRedirect('/dosen/ta/pengajuan');
        }

        $this->assertDatabaseHas('thesis_guidance_requests', [
            'id' => $requestId,
            'status' => 'approved',
        ]);

        $this->assertDatabaseHas('thesis_guidances', [
            'student_id' => $studentId,
            'lecturer_id' => 1,
            'title' => $title,
            'status' => 'active',
        ]);

        $this->assertDatabaseHas('thesis_guidances', [
            'student_id' => $studentId,
            'lecturer_id' => 2,
            'title' => $title,
            'status' => 'active',
        ]);
    }
}
