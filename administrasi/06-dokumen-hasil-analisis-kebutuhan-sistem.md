# Dokumen Hasil Analisis Kebutuhan Sistem SIMTA

## Identitas Dokumen

| Komponen | Keterangan |
| --- | --- |
| Sistem | SIMTA - Sistem Informasi Manajemen Tugas Akhir |
| Modul utama | Bimbingan Tugas Akhir, Bimbingan PA, DELTA-MAT |
| Aplikasi | Web Laravel |
| Fokus | Analisis kebutuhan sistem berdasarkan implementasi proyek |
| Tanggal | 12 Juni 2026 |

## Ringkasan Sistem

SIMTA adalah aplikasi web untuk mendukung pengelolaan tugas akhir mahasiswa. Berdasarkan kode proyek, sistem sudah menyediakan akses untuk dosen, mahasiswa, dan admin. Sistem juga memiliki fitur publik DELTA-MAT untuk pencarian data judul tugas akhir dan pemeriksaan indikasi kemiripan judul.

Modul yang sudah terlihat pada proyek:

| Modul | Route utama | Pengguna |
| --- | --- | --- |
| Landing SIMTA | `/` | Publik |
| Login dosen | `/dosen/login` | Dosen |
| Login mahasiswa | `/mahasiswa/login` | Mahasiswa |
| Login admin | `/admin/login` | Admin |
| Dashboard dosen | `/dosen` | Dosen |
| Dashboard mahasiswa | `/mahasiswa` | Mahasiswa |
| Bimbingan PA dosen | `/pa/dosen` | Dosen |
| Bimbingan PA mahasiswa | `/pa/mahasiswa` | Mahasiswa |
| Admin bimbingan PA | `/admin/bimbingan-pa` | Admin |
| DELTA-MAT publik | `/database-ta` | Publik |
| Detail data DELTA-MAT | `/database-ta/{id}` | Publik |
| Admin DELTA-MAT | `/admin/database-ta` | Admin |

## Permasalahan Utama

| No. | Permasalahan | Dampak |
| --- | --- | --- |
| 1 | Administrasi tugas akhir masih berpotensi tersebar pada catatan manual, spreadsheet, dan komunikasi personal. | Data sulit dilacak dan proses monitoring lambat. |
| 2 | Mahasiswa membutuhkan informasi status bimbingan, seminar, repository, dan Q&A secara terpusat. | Mahasiswa harus bertanya berulang kepada dosen atau admin. |
| 3 | Dosen membutuhkan daftar mahasiswa bimbingan, approval, seminar, repository, dan pertanyaan dalam satu dashboard. | Pengelolaan bimbingan menjadi tidak efisien. |
| 4 | Admin membutuhkan pengelolaan data PA dan database judul secara langsung dari aplikasi. | Perubahan data harus dilakukan lewat file atau database manual. |
| 5 | Pemeriksaan kemiripan judul tugas akhir belum cukup cepat jika dilakukan secara manual. | Risiko judul mirip atau berulang meningkat. |

## Kebutuhan Sistem

| Kode | Kebutuhan | Implementasi yang relevan |
| --- | --- | --- |
| KS-01 | Sistem menyediakan login berdasarkan peran. | `AuthController`, role `dosen`, `mahasiswa`, `admin`. |
| KS-02 | Sistem menyediakan dashboard dosen untuk bimbingan tugas akhir. | `BimbinganController::dashboard`. |
| KS-03 | Sistem menyediakan dashboard mahasiswa untuk melihat data tugas akhir. | `MahasiswaController::dashboard`. |
| KS-04 | Sistem menyediakan fitur filter data bimbingan berdasarkan NIM, nama, dan judul. | `BimbinganController::guidance`. |
| KS-05 | Sistem menyediakan fitur approval tugas akhir. | Tabel `approvals`, route `/dosen/persetujuan`. |
| KS-06 | Sistem menyediakan fitur seminar/ujian dan input nilai. | Tabel `seminars`, route `/dosen/seminar-ujian`. |
| KS-07 | Sistem menyediakan repository dokumen tugas akhir. | Tabel `repositories`, route `/dosen/repository`, `/mahasiswa/repository`. |
| KS-08 | Sistem menyediakan fitur Q&A dosen-mahasiswa. | Tabel `questions`, route `/dosen/qa`, `/mahasiswa/qa`. |
| KS-09 | Sistem menyediakan bimbingan PA untuk konsultasi akademik. | `PaDosenController`, `PaMahasiswaController`. |
| KS-10 | Sistem menyediakan admin CRUD bimbingan PA. | `AdminBimbinganPaController`. |
| KS-11 | Sistem menyediakan DELTA-MAT untuk pencarian judul dan kemiripan. | `DatabaseTaController`. |
| KS-12 | Sistem menyediakan admin CRUD DELTA-MAT dan import Excel. | `AdminDatabaseTaController`, `DeltaMatExcelImporter`. |

## Kebutuhan Nonfungsional

| Kode | Kebutuhan | Penjelasan |
| --- | --- | --- |
| KNF-01 | Aksesibilitas | Aplikasi perlu dapat diakses melalui browser pada laptop dan smartphone. |
| KNF-02 | Keamanan | Pengelolaan data hanya dapat dilakukan oleh pengguna yang sudah login dan memiliki role sesuai. |
| KNF-03 | Konsistensi data | Validasi input diperlukan pada form profil, repository, PA, dan DELTA-MAT. |
| KNF-04 | Kemudahan penggunaan | Antarmuka harus ringkas, langsung menampilkan pekerjaan utama pengguna. |
| KNF-05 | Ketersediaan data | Data DELTA-MAT perlu diimpor dari `database/excel` dan dapat diperbarui. |
| KNF-06 | Audit manual | Untuk tahap sekarang, validasi perubahan dilakukan melalui test aplikasi dan pengecekan halaman. |

## Batasan Sistem Saat Ini

| Area | Batasan |
| --- | --- |
| Upload dokumen | Repository menyimpan nama file dan URL, belum upload file langsung ke server. |
| DELTA-MAT | Kemiripan judul masih berbasis irisan kata sederhana, bukan semantic search. |
| Admin TA | CRUD tugas akhir utama belum selengkap CRUD bimbingan PA. |
| Notifikasi | Belum ada notifikasi email atau push notification. |
| Audit log | Belum tersedia riwayat perubahan data per pengguna. |

## Kesimpulan

SIMTA sudah memiliki fondasi fungsional untuk mendukung digitalisasi tugas akhir: role pengguna, bimbingan TA, seminar/ujian, repository, Q&A, bimbingan PA, admin data, dan DELTA-MAT. Kebutuhan lanjutan yang paling penting adalah memperkuat integrasi workflow tugas akhir, memperjelas status administrasi, menambah audit log, dan meningkatkan akurasi pencarian judul pada DELTA-MAT.
