# Daftar Fitur SIMTA

## Ringkasan

SIMTA memiliki fitur utama untuk tiga jenis pengguna: dosen, mahasiswa, dan admin. Selain itu, SIMTA menyediakan fitur publik DELTA-MAT untuk pencarian judul tugas akhir.

## Fitur Publik

| No. | Fitur | Route | Keterangan |
| --- | --- | --- | --- |
| 1 | Landing SIMTA | `/` | Halaman awal untuk memilih akses layanan. |
| 2 | DELTA-MAT | `/database-ta` | Pencarian judul tugas akhir dan analisis kemiripan. |
| 3 | Detail DELTA-MAT | `/database-ta/{id}` | Detail data judul dan preview dokumen PDF. |
| 4 | Login dosen | `/dosen/login` | Autentikasi role dosen. |
| 5 | Login mahasiswa | `/mahasiswa/login` | Autentikasi role mahasiswa. |
| 6 | Login admin | `/admin/login` | Autentikasi role admin. |

## Fitur Dosen

| No. | Fitur | Route | Keterangan |
| --- | --- | --- | --- |
| 1 | Dashboard dosen | `/dosen` | Menampilkan mahasiswa bimbingan aktif. |
| 2 | Profil dosen | `/dosen/profil` | Menampilkan dan memperbarui data profil dosen. |
| 3 | Bimbingan TA | `/dosen/bimbingan-ta` | Menampilkan daftar bimbingan dan filter NIM/nama/judul. |
| 4 | Persetujuan | `/dosen/persetujuan` | Dosen menyetujui atau menolak approval. |
| 5 | Seminar/ujian | `/dosen/seminar-ujian` | Dosen melihat jadwal dan mengisi nilai/feedback. |
| 6 | Repository | `/dosen/repository` | Dosen menambahkan dokumen repository. |
| 7 | Q&A | `/dosen/qa` | Dosen menjawab pertanyaan mahasiswa. |
| 8 | Manual aplikasi | `/dosen/manual-aplikasi` | Panduan penggunaan aplikasi. |
| 9 | Dashboard PA dosen | `/pa/dosen` | Dosen PA mengelola konsultasi akademik. |

## Fitur Mahasiswa

| No. | Fitur | Route | Keterangan |
| --- | --- | --- | --- |
| 1 | Dashboard mahasiswa | `/mahasiswa` | Ringkasan bimbingan dan seminar. |
| 2 | Profil mahasiswa | `/mahasiswa/profil` | Menampilkan data mahasiswa. |
| 3 | Bimbingan TA | `/mahasiswa/bimbingan-ta` | Menampilkan data bimbingan mahasiswa. |
| 4 | Repository | `/mahasiswa/repository` | Menampilkan dokumen tugas akhir mahasiswa. |
| 5 | Q&A | `/mahasiswa/qa` | Menampilkan pertanyaan dan jawaban dosen. |
| 6 | Dashboard PA mahasiswa | `/pa/mahasiswa` | Pengajuan dan riwayat konsultasi PA. |

## Fitur Admin

| No. | Fitur | Route | Keterangan |
| --- | --- | --- | --- |
| 1 | Dashboard admin | `/admin` | Akses awal admin. |
| 2 | Kelola DELTA-MAT | `/admin/database-ta` | CRUD data judul tugas akhir. |
| 3 | Edit DELTA-MAT | `/admin/database-ta/{id}/edit` | Mode edit data judul. |
| 4 | Kelola bimbingan PA | `/admin/bimbingan-pa` | CRUD assignment PA, IPK/SKS, dan konsultasi. |
| 5 | Import Excel DELTA-MAT | `deltamat:import-excel` | Merge data dari `database/excel`. |

## Fitur Data dan Validasi

| No. | Fitur | Implementasi |
| --- | --- | --- |
| 1 | Role pengguna | Kolom `role` pada tabel `users`. |
| 2 | Validasi input profil dosen | `BimbinganController::updateProfile`. |
| 3 | Validasi repository | `BimbinganController::storeRepository`. |
| 4 | Validasi konsultasi PA | `PaMahasiswaController`, `PaDosenController`, `AdminBimbinganPaController`. |
| 5 | Validasi DELTA-MAT | `AdminDatabaseTaController::validated`. |
| 6 | Test fitur utama | `tests/Feature/ExampleTest.php`. |

## Kesimpulan

Daftar fitur SIMTA sudah mencakup kebutuhan dasar manajemen tugas akhir dan bimbingan akademik. DELTA-MAT menjadi fitur pendukung penting untuk pencarian referensi judul dan validasi awal kemiripan judul.
