# Rancangan Struktur Database dan Skema Relasi Data SIMTA

## Tujuan Dokumen

Dokumen ini menjelaskan struktur database SIMTA berdasarkan migration yang tersedia pada proyek.

## Daftar Tabel

| No. | Tabel | Fungsi |
| --- | --- | --- |
| 1 | `users` | Akun login dan role pengguna. |
| 2 | `lecturers` | Profil dosen. |
| 3 | `students` | Profil mahasiswa. |
| 4 | `thesis_guidances` | Data bimbingan tugas akhir. |
| 5 | `approvals` | Persetujuan seminar atau revisi. |
| 6 | `seminars` | Data seminar/ujian. |
| 7 | `repositories` | Dokumen repository tugas akhir. |
| 8 | `questions` | Pertanyaan dan jawaban mahasiswa-dosen. |
| 9 | `thesis_title_databases` | Database judul DELTA-MAT. |
| 10 | `pa_assignments` | Penetapan dosen PA. |
| 11 | `pa_academic_records` | Data IPK dan SKS mahasiswa. |
| 12 | `pa_consultations` | Riwayat konsultasi PA. |

## Struktur Tabel Utama

### `users`

| Field | Keterangan |
| --- | --- |
| `id` | Primary key. |
| `name` | Nama pengguna. |
| `email` | Email login. |
| `role` | Role pengguna: dosen, mahasiswa, admin. |
| `password` | Password terenkripsi. |

### `lecturers`

| Field | Keterangan |
| --- | --- |
| `user_id` | Relasi ke `users`. |
| `nip` | NIP dosen, unik. |
| `nidn` | NIDN dosen. |
| `employment_status` | Status kepegawaian. |
| `expertise` | Bidang keahlian. |
| `name` | Nama dosen. |
| `email`, `phone`, `address` | Kontak dosen. |

### `students`

| Field | Keterangan |
| --- | --- |
| `user_id` | Relasi ke `users`. |
| `nim` | NIM mahasiswa, unik. |
| `name` | Nama mahasiswa. |
| `program` | Program studi. |
| `email` | Email mahasiswa. |

### `thesis_guidances`

| Field | Keterangan |
| --- | --- |
| `lecturer_id` | Relasi ke `lecturers`. |
| `student_id` | Relasi ke `students`. |
| `title` | Judul tugas akhir. |
| `status` | Status bimbingan. |
| `seminar_status` | Status seminar. |
| `progress` | Persentase progres. |
| `started_at` | Tanggal mulai. |
| `last_note` | Catatan terakhir. |

### `thesis_title_databases`

| Field | Keterangan |
| --- | --- |
| `submission_date` | Tanggal pengajuan. |
| `phone` | Nomor WA. |
| `email` | Email mahasiswa. |
| `nim` | NIM mahasiswa. |
| `student_name` | Nama mahasiswa. |
| `title` | Judul tugas akhir. |
| `supervisor_1`, `supervisor_1_nip` | Pembimbing 1. |
| `supervisor_2`, `supervisor_2_nip` | Pembimbing 2. |
| `document_url` | Link dokumen PDF/Google Drive. |

## Skema Relasi Data

```text
users
  |-- lecturers.user_id
  |-- students.user_id

lecturers
  |-- thesis_guidances.lecturer_id
  |-- questions.lecturer_id
  |-- pa_assignments.lecturer_id
  |-- pa_consultations.lecturer_id

students
  |-- thesis_guidances.student_id
  |-- questions.student_id
  |-- pa_assignments.student_id
  |-- pa_academic_records.student_id
  |-- pa_consultations.student_id

thesis_guidances
  |-- approvals.thesis_guidance_id
  |-- seminars.thesis_guidance_id
  |-- repositories.thesis_guidance_id

pa_assignments
  |-- pa_consultations.pa_assignment_id

thesis_title_databases
  |-- berdiri sendiri sebagai basis data DELTA-MAT
```

## Aturan Kunci dan Constraint

| Tabel | Constraint |
| --- | --- |
| `lecturers` | `user_id` unik, `nip` unik. |
| `students` | `user_id` unik, `nim` unik. |
| `thesis_guidances` | Hapus cascade jika dosen atau mahasiswa dihapus. |
| `approvals` | Hapus cascade jika data bimbingan TA dihapus. |
| `seminars` | Hapus cascade jika data bimbingan TA dihapus. |
| `repositories` | Hapus cascade jika data bimbingan TA dihapus. |
| `questions` | Hapus cascade jika dosen dihapus, mahasiswa boleh null. |
| `thesis_title_databases` | Kombinasi `nim` dan `title` unik. |
| `pa_assignments` | Satu mahasiswa hanya memiliki satu assignment PA aktif pada tabel. |
| `pa_academic_records` | Kombinasi `student_id` dan `semester` unik. |

## Catatan Desain Database

Tabel `thesis_title_databases` belum direlasikan langsung ke `students` karena data DELTA-MAT berasal dari Excel dan dapat memuat mahasiswa historis yang belum memiliki akun pada sistem. Desain ini tepat untuk kebutuhan repository judul dan pencarian publik.

## Rekomendasi Pengembangan

| Area | Rekomendasi |
| --- | --- |
| Audit | Tambahkan tabel audit log untuk mencatat perubahan data oleh admin atau dosen. |
| File | Tambahkan tabel file lokal jika dokumen tidak lagi bergantung pada URL eksternal. |
| DELTA-MAT | Tambahkan kolom `program` dan `source_file` agar data Excel dapat ditelusuri. |
| Workflow TA | Tambahkan tabel pengajuan judul jika mahasiswa dapat mengajukan judul langsung dari aplikasi. |
