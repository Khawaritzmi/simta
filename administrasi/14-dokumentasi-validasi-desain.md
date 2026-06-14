# Dokumentasi Validasi Desain SIMTA

## Tujuan Dokumen

Dokumen ini menjelaskan rencana dan ringkasan validasi desain SIMTA berdasarkan fitur dan antarmuka yang tersedia pada proyek.

## Objek Validasi

| No. | Objek | Area validasi |
| --- | --- | --- |
| 1 | Landing SIMTA | Kejelasan akses dosen, mahasiswa, admin, PA, dan DELTA-MAT. |
| 2 | Dashboard dosen | Kesesuaian menu bimbingan TA, approval, seminar, repository, Q&A. |
| 3 | Dashboard mahasiswa | Kesesuaian menu profil, bimbingan TA, repository, Q&A. |
| 4 | Dashboard PA | Kesesuaian alur konsultasi PA dosen dan mahasiswa. |
| 5 | Admin bimbingan PA | Kesesuaian form dan tabel CRUD data PA. |
| 6 | DELTA-MAT | Kejelasan pencarian, skor kemiripan, rekomendasi, detail, dan preview PDF. |
| 7 | Admin DELTA-MAT | Kesesuaian form tambah/edit dan tabel data judul. |

## Metode Validasi

| Metode | Tujuan |
| --- | --- |
| Review ahli materi | Memastikan istilah, alur tugas akhir, dan data akademik sesuai kebutuhan program studi. |
| Review ahli media/UI | Memastikan tampilan mudah dipahami dan navigasi jelas. |
| Uji fungsional | Memastikan setiap route utama dapat dibuka dan operasi penting berjalan. |
| Uji pengguna terbatas | Memastikan dosen, mahasiswa, dan admin dapat menyelesaikan tugas utama. |

## Instrumen Validasi Desain

| No. | Aspek | Indikator | Skor 1-5 |
| --- | --- | --- | --- |
| 1 | Kejelasan identitas | Nama SIMTA dan DELTA-MAT mudah dikenali. |  |
| 2 | Navigasi | Menu sesuai peran pengguna. |  |
| 3 | Konsistensi | Warna, tombol, tabel, dan form digunakan secara konsisten. |  |
| 4 | Keterbacaan | Teks, judul, dan data tabel mudah dibaca. |  |
| 5 | Kemudahan tugas | Pengguna dapat menemukan fitur utama tanpa langkah berlebih. |  |
| 6 | Kelengkapan informasi | Data yang ditampilkan cukup untuk pengambilan keputusan. |  |
| 7 | Umpan balik sistem | Pesan berhasil dan error mudah dipahami. |  |
| 8 | Responsivitas | Halaman tetap dapat digunakan pada layar kecil. |  |
| 9 | Akses dokumen | Link dan preview dokumen mudah ditemukan. |  |
| 10 | Kelayakan implementasi | Desain sesuai dengan fitur yang tersedia dalam proyek. |  |

## Validasi Fungsional Berdasarkan Proyek

| Area | Bukti implementasi | Status |
| --- | --- | --- |
| Landing | Route `/` dan view `landing.blade.php`. | Tersedia |
| Login role | `AuthController` dan route login dosen/mahasiswa/admin. | Tersedia |
| Dashboard dosen | `BimbinganController::dashboard`. | Tersedia |
| Dashboard mahasiswa | `MahasiswaController::dashboard`. | Tersedia |
| Admin PA | `AdminBimbinganPaController`. | Tersedia |
| DELTA-MAT publik | `DatabaseTaController::index`. | Tersedia |
| Detail DELTA-MAT | `DatabaseTaController::show`. | Tersedia |
| Import Excel DELTA-MAT | `DeltaMatExcelImporter` dan command `deltamat:import-excel`. | Tersedia |
| Test fitur | `tests/Feature/ExampleTest.php`. | Tersedia |

## Catatan Hasil Validasi Internal

| No. | Temuan | Tindak lanjut |
| --- | --- | --- |
| 1 | DELTA-MAT sudah menampilkan skor kemiripan dan status. | Tingkatkan algoritma jika dataset makin besar. |
| 2 | Preview dokumen PDF bergantung pada akses Google Drive. | Tambahkan fallback link asli dan rencana upload lokal. |
| 3 | Admin sudah dapat mengelola DELTA-MAT dan PA. | Tambahkan audit log pada pengembangan berikutnya. |
| 4 | Bimbingan TA sudah tersedia untuk dosen dan mahasiswa. | Tambahkan workflow pengajuan judul dari mahasiswa jika dibutuhkan. |
| 5 | Layout responsif dasar sudah tersedia. | Lakukan uji mobile lebih lanjut pada tabel panjang. |

## Kriteria Desain Dinyatakan Layak

Desain SIMTA dinyatakan layak digunakan jika:

- Pengguna dapat mengenali fungsi aplikasi dari landing page.
- Dosen dapat menemukan menu bimbingan, approval, seminar, repository, dan Q&A.
- Mahasiswa dapat melihat status bimbingan, repository, Q&A, dan konsultasi PA.
- Admin dapat mengelola data PA dan DELTA-MAT.
- Pengguna publik dapat mencari judul pada DELTA-MAT dan membuka detail dokumen.
- Tidak ada route utama yang gagal dibuka pada uji fungsional.

## Kesimpulan

Desain SIMTA sudah sesuai dengan kebutuhan dasar sistem manajemen tugas akhir. Validasi lanjutan sebaiknya dilakukan melalui review ahli dan uji pengguna terbatas untuk memastikan tampilan, istilah, dan workflow sudah sesuai dengan kebutuhan operasional program studi.
