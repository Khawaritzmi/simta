<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
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
            '/dosen/bimbingan-ta',
            '/dosen/persetujuan',
            '/dosen/seminar-ujian',
            '/dosen/repository',
            '/dosen/qa',
            '/dosen/manual-aplikasi',
        ] as $path) {
            $this->get($path)->assertStatus(200);
        }
    }

    public function test_mahasiswa_pages_return_successful_responses(): void
    {
        $this->actingAs(User::where('email', 'mahasiswa@bimbingan.test')->firstOrFail());

        foreach ([
            '/mahasiswa',
            '/mahasiswa/profil',
            '/mahasiswa/bimbingan-ta',
            '/mahasiswa/repository',
            '/mahasiswa/qa',
        ] as $path) {
            $this->get($path)->assertStatus(200);
        }
    }

    public function test_pa_pages_return_successful_responses_for_each_role(): void
    {
        $this->actingAs(User::where('email', 'dosen@bimbingan.test')->firstOrFail());
        $this->get('/pa/dosen')->assertStatus(200);

        $this->actingAs(User::where('email', 'mahasiswa@bimbingan.test')->firstOrFail());
        $this->get('/pa/mahasiswa')->assertStatus(200);
    }

    public function test_pa_login_links_redirect_to_pa_dashboards(): void
    {
        $this->post('/dosen/login', [
            'email' => 'dosen@bimbingan.test',
            'password' => 'password',
            'next' => 'pa.dosen.dashboard',
        ])->assertRedirect('/pa/dosen');

        $this->post('/logout');

        $this->post('/mahasiswa/login', [
            'email' => 'mahasiswa@bimbingan.test',
            'password' => 'password',
            'next' => 'pa.mahasiswa.dashboard',
        ])->assertRedirect('/pa/mahasiswa');
    }

    public function test_student_can_submit_pa_consultation(): void
    {
        $this->actingAs(User::where('email', 'mahasiswa@bimbingan.test')->firstOrFail());

        $this->post('/pa/mahasiswa/konsultasi', [
            'topic' => 'Konsultasi pengambilan mata kuliah pilihan',
            'student_note' => 'Saya ingin memastikan mata kuliah pilihan sesuai dengan rencana tugas akhir.',
            'requested_at' => '2026-05-06 09:00:00',
        ])->assertRedirect('/pa/mahasiswa');

        $this->assertDatabaseHas('pa_consultations', [
            'student_id' => 1,
            'lecturer_id' => 1,
            'topic' => 'Konsultasi pengambilan mata kuliah pilihan',
            'status' => 'diajukan',
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
        ])->assertRedirect('/pa/dosen');

        $this->assertDatabaseHas('pa_consultations', [
            'id' => $consultation->id,
            'status' => 'selesai',
            'lecturer_note' => 'Mahasiswa disarankan menjaga beban SKS tetap realistis.',
        ]);
    }

    public function test_user_can_login_and_logout(): void
    {
        $this->post('/dosen/login', [
            'email' => 'dosen@bimbingan.test',
            'password' => 'password',
        ])->assertRedirect('/dosen');

        $this->assertAuthenticated();

        $this->post('/logout')->assertRedirect('/');

        $this->assertGuest();
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
        ])->assertRedirect('/dosen');

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
        ])->assertRedirect('/mahasiswa');

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
}
