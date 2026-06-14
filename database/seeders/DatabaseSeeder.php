<?php

namespace Database\Seeders;

use App\Support\DeltaMatExcelImporter;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        DB::table('users')->updateOrInsert(
            ['email' => 'dosen@bimbingan.test'],
            [
                'name' => 'Khawaritzmi Abdallah Ahmad,, S.Si., M.Eng',
                'role' => 'dosen',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
        );

        $userId = DB::table('users')->where('email', 'dosen@bimbingan.test')->value('id');

        DB::table('lecturers')->insert([
            'user_id' => $userId,
            'nip' => '7371110904990002',
            'nidn' => null,
            'certificate_number' => null,
            'employment_status' => 'Dosen Tetap Non PNS',
            'expertise' => 'Machine Learning',
            'name' => 'Khawaritzmi Abdallah Ahmad,, S.Si., M.Eng',
            'gender' => 'Laki-Laki',
            'birth_place' => 'Makassar',
            'birth_date' => '1999-04-09',
            'email' => 'khawaritzmi@gmail.com',
            'phone' => '081234567890',
            'address' => 'Univeristas Negeri Makassar',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('lecturers')->insert([
            [
                'user_id' => null,
                'nip' => '197602012005011001',
                'nidn' => '0001027601',
                'certificate_number' => null,
                'employment_status' => 'Dosen Tetap PNS',
                'expertise' => 'Statistika',
                'name' => 'Dr. Nurul Fadhilah, M.Si.',
                'gender' => 'Perempuan',
                'birth_place' => 'Makassar',
                'birth_date' => '1976-02-01',
                'email' => 'nurul.fadhilah@unm.test',
                'phone' => '081234567891',
                'address' => 'Universitas Negeri Makassar',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => null,
                'nip' => '198004122008121002',
                'nidn' => '0012048002',
                'certificate_number' => null,
                'employment_status' => 'Dosen Tetap PNS',
                'expertise' => 'Aljabar',
                'name' => 'Dr. Andi Saputra, M.Pd.',
                'gender' => 'Laki-Laki',
                'birth_place' => 'Parepare',
                'birth_date' => '1980-04-12',
                'email' => 'andi.saputra@unm.test',
                'phone' => '081234567892',
                'address' => 'Universitas Negeri Makassar',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => null,
                'nip' => '198709232012122003',
                'nidn' => '0023098703',
                'certificate_number' => null,
                'employment_status' => 'Dosen Tetap PNS',
                'expertise' => 'Analisis Numerik',
                'name' => 'Dr. Sri Wahyuni, M.Si.',
                'gender' => 'Perempuan',
                'birth_place' => 'Bone',
                'birth_date' => '1987-09-23',
                'email' => 'sri.wahyuni@unm.test',
                'phone' => '081234567893',
                'address' => 'Universitas Negeri Makassar',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        DB::table('users')->updateOrInsert(
            ['email' => 'mahasiswa@bimbingan.test'],
            [
                'name' => 'Aulia Rahmadani',
                'role' => 'mahasiswa',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
        );

        DB::table('users')->updateOrInsert(
            ['email' => 'admin@bimbingan.test'],
            [
                'name' => 'Admin Sistem',
                'role' => 'admin',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
        );

        $studentUserId = DB::table('users')->where('email', 'mahasiswa@bimbingan.test')->value('id');

        DB::table('students')->insert([
            [
                'user_id' => $studentUserId,
                'nim' => 'H011201001',
                'name' => 'Aulia Rahmadani',
                'program' => 'Matematika',
                'email' => 'aulia@student.test',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => null,
                'nim' => 'H011201018',
                'name' => 'Fikri Pratama',
                'program' => 'Matematika',
                'email' => 'fikri@student.test',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => null,
                'nim' => 'H011201027',
                'name' => 'Nur Azizah',
                'program' => 'Statistika',
                'email' => 'azizah@student.test',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        DB::table('thesis_guidances')->insert([
            [
                'lecturer_id' => 1,
                'student_id' => 1,
                'title' => 'Klasifikasi Citra Daun Menggunakan Convolutional Neural Network',
                'status' => 'active',
                'seminar_status' => 'Proposal',
                'progress' => 62,
                'started_at' => '2026-02-10',
                'last_note' => 'Perbaiki bagian metodologi dan tambahkan metrik evaluasi.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'lecturer_id' => 1,
                'student_id' => 2,
                'title' => 'Prediksi Kelulusan Mahasiswa Berbasis Random Forest',
                'status' => 'active',
                'seminar_status' => 'Hasil',
                'progress' => 78,
                'started_at' => '2026-01-18',
                'last_note' => 'Dataset sudah baik, lanjutkan analisis fitur penting.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'lecturer_id' => 1,
                'student_id' => 3,
                'title' => 'Optimasi Penjadwalan Seminar Menggunakan Algoritma Genetika',
                'status' => 'completed',
                'seminar_status' => 'Ujian Tutup',
                'progress' => 100,
                'started_at' => '2025-10-01',
                'last_note' => 'Finalisasi dokumen repository.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        DB::table('approvals')->insert([
            [
                'thesis_guidance_id' => 1,
                'type' => 'Persetujuan Seminar Proposal',
                'description' => 'Mahasiswa mengajukan seminar proposal.',
                'status' => 'pending',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'thesis_guidance_id' => 2,
                'type' => 'Persetujuan Revisi Judul',
                'description' => 'Perubahan judul setelah validasi data.',
                'status' => 'pending',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        DB::table('thesis_guidance_requests')->insert([
            [
                'student_id' => 1,
                'supervisor_1_id' => 1,
                'supervisor_2_id' => 2,
                'examiner_1_id' => 3,
                'examiner_2_id' => 4,
                'title' => 'Klasifikasi Citra Daun Menggunakan Convolutional Neural Network',
                'admin_status' => 'approved',
                'supervisor_1_status' => 'approved',
                'supervisor_2_status' => 'approved',
                'examiner_1_status' => 'approved',
                'examiner_2_status' => 'approved',
                'admin_decided_at' => now(),
                'supervisor_1_decided_at' => now(),
                'supervisor_2_decided_at' => now(),
                'examiner_1_decided_at' => now(),
                'examiner_2_decided_at' => now(),
                'status' => 'approved',
                'activated_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'student_id' => 2,
                'supervisor_1_id' => 1,
                'supervisor_2_id' => 2,
                'examiner_1_id' => 3,
                'examiner_2_id' => 4,
                'title' => 'Prediksi Kelulusan Mahasiswa Berbasis Random Forest',
                'admin_status' => 'approved',
                'supervisor_1_status' => 'approved',
                'supervisor_2_status' => 'approved',
                'examiner_1_status' => 'approved',
                'examiner_2_status' => 'approved',
                'admin_decided_at' => now(),
                'supervisor_1_decided_at' => now(),
                'supervisor_2_decided_at' => now(),
                'examiner_1_decided_at' => now(),
                'examiner_2_decided_at' => now(),
                'status' => 'approved',
                'activated_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        DB::table('seminars')->insert([
            [
                'thesis_guidance_id' => 1,
                'type' => 'Seminar Proposal',
                'scheduled_at' => '2026-05-08 09:00:00',
                'room' => 'Ruang Seminar Matematika',
                'status' => 'scheduled',
                'score' => null,
                'feedback' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'thesis_guidance_id' => 2,
                'type' => 'Seminar Hasil',
                'scheduled_at' => '2026-04-20 13:30:00',
                'room' => 'Lab Komputasi',
                'status' => 'graded',
                'score' => 88,
                'feedback' => 'Presentasi jelas, tambahkan pembahasan error model.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        DB::table('repositories')->insert([
            [
                'thesis_guidance_id' => 2,
                'document_type' => 'Draft Skripsi',
                'file_name' => 'draft-fikri-pratama.pdf',
                'url' => 'https://repository.example.test/draft-fikri-pratama',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'thesis_guidance_id' => 3,
                'document_type' => 'Artikel',
                'file_name' => 'artikel-nur-azizah.pdf',
                'url' => 'https://repository.example.test/artikel-nur-azizah',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        DB::table('questions')->insert([
            [
                'lecturer_id' => 1,
                'student_id' => 1,
                'subject' => 'Validasi dataset',
                'question' => 'Apakah jumlah kelas pada dataset perlu diseimbangkan sebelum training?',
                'answer' => null,
                'answered_at' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'lecturer_id' => 1,
                'student_id' => 2,
                'subject' => 'Revisi bab 4',
                'question' => 'Bagian mana yang perlu saya tambahkan pada analisis confusion matrix?',
                'answer' => 'Tambahkan interpretasi per kelas dan jelaskan penyebab false positive terbesar.',
                'answered_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        DB::table('pa_assignments')->insert([
            [
                'lecturer_id' => 1,
                'student_id' => 1,
                'academic_year' => '2025/2026',
                'status' => 'aktif',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'lecturer_id' => 1,
                'student_id' => 2,
                'academic_year' => '2025/2026',
                'status' => 'aktif',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'lecturer_id' => 1,
                'student_id' => 3,
                'academic_year' => '2025/2026',
                'status' => 'aktif',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        DB::table('pa_academic_records')->insert([
            [
                'student_id' => 1,
                'semester' => 5,
                'ipk' => 3.62,
                'sks_semester' => 21,
                'sks_total' => 106,
                'academic_status' => 'Aktif',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'student_id' => 1,
                'semester' => 6,
                'ipk' => 3.68,
                'sks_semester' => 20,
                'sks_total' => 126,
                'academic_status' => 'Aktif',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'student_id' => 2,
                'semester' => 6,
                'ipk' => 3.41,
                'sks_semester' => 18,
                'sks_total' => 122,
                'academic_status' => 'Aktif',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'student_id' => 3,
                'semester' => 8,
                'ipk' => 3.77,
                'sks_semester' => 12,
                'sks_total' => 144,
                'academic_status' => 'Aktif',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        DB::table('pa_consultations')->insert([
            [
                'pa_assignment_id' => 1,
                'student_id' => 1,
                'lecturer_id' => 1,
                'topic' => 'Rencana KRS semester depan',
                'student_note' => 'Saya ingin memastikan beban SKS semester depan masih aman dengan jadwal tugas akhir.',
                'requested_at' => '2026-05-03 10:00:00',
                'scheduled_at' => '2026-05-03 10:00:00',
                'status' => 'dijadwalkan',
                'lecturer_note' => null,
                'recommendation' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'pa_assignment_id' => 2,
                'student_id' => 2,
                'lecturer_id' => 1,
                'topic' => 'Evaluasi IPK dan SKS',
                'student_note' => 'Mohon arahan untuk menjaga IPK dan menyelesaikan SKS pilihan.',
                'requested_at' => '2026-04-22 13:00:00',
                'scheduled_at' => '2026-04-22 13:00:00',
                'status' => 'selesai',
                'lecturer_note' => 'Mahasiswa sudah berada pada jalur akademik yang baik.',
                'recommendation' => 'Ambil mata kuliah pilihan yang mendukung topik tugas akhir.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        DB::table('settings')->updateOrInsert(
            ['key' => 'guidance_target_default'],
            ['value' => '16', 'created_at' => now(), 'updated_at' => now()],
        );

        foreach (range(1, 8) as $index) {
            DB::table('guidances')->insert([
                'student_id' => 1,
                'type' => 'TA',
                'completed_at' => now()->subDays(20 - $index),
                'notes' => "Bimbingan TA ke-{$index}",
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        foreach (range(1, 5) as $index) {
            DB::table('guidances')->insert([
                'student_id' => 1,
                'type' => 'PA',
                'completed_at' => now()->subDays(30 - $index),
                'notes' => "Bimbingan PA ke-{$index}",
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        app(DeltaMatExcelImporter::class)->import();
    }
}
