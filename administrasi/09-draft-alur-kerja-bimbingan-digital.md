# Draft Alur Kerja Bimbingan Digital SIMTA

## Tujuan Dokumen

Dokumen ini menjelaskan rancangan alur kerja bimbingan digital berdasarkan fitur yang sudah tersedia di SIMTA.

## Aktor

| Aktor | Peran dalam alur digital |
| --- | --- |
| Mahasiswa | Melihat data bimbingan, repository, Q&A, PA, dan mencari judul melalui DELTA-MAT. |
| Dosen | Memantau mahasiswa bimbingan, approval, seminar, repository, Q&A, dan konsultasi PA. |
| Admin | Mengelola data PA dan DELTA-MAT. |

## Alur Digital Bimbingan Tugas Akhir

| Tahap | Aktivitas | Fitur SIMTA |
| --- | --- | --- |
| 1 | Mahasiswa mencari referensi judul tugas akhir. | DELTA-MAT `/database-ta`. |
| 2 | Mahasiswa memeriksa indikasi kemiripan judul. | Skor kemiripan dan status `UNIK`, `WASPADA`, `DUPLIKASI`. |
| 3 | Data tugas akhir mahasiswa tersimpan pada sistem. | Tabel `thesis_guidances`. |
| 4 | Mahasiswa melihat data bimbingan dan progres. | `/mahasiswa/bimbingan-ta`. |
| 5 | Dosen melihat daftar mahasiswa bimbingan. | `/dosen/bimbingan-ta`. |
| 6 | Dosen memproses persetujuan seminar atau revisi. | `/dosen/persetujuan`. |
| 7 | Dosen melihat seminar/ujian dan mengisi nilai/feedback. | `/dosen/seminar-ujian`. |
| 8 | Dosen menambahkan dokumen repository. | `/dosen/repository`. |
| 9 | Mahasiswa melihat dokumen repository. | `/mahasiswa/repository`. |
| 10 | Mahasiswa dan dosen menggunakan Q&A sebagai arsip pertanyaan dan jawaban. | `/mahasiswa/qa`, `/dosen/qa`. |

## Alur Digital Bimbingan PA

| Tahap | Aktivitas | Fitur SIMTA |
| --- | --- | --- |
| 1 | Admin menetapkan dosen PA untuk mahasiswa. | `/admin/bimbingan-pa`. |
| 2 | Admin memasukkan data akademik IPK/SKS. | `/admin/bimbingan-pa`. |
| 3 | Mahasiswa membuka dashboard PA. | `/pa/mahasiswa`. |
| 4 | Mahasiswa mengajukan konsultasi PA. | Form konsultasi PA mahasiswa. |
| 5 | Dosen PA melihat pengajuan konsultasi. | `/pa/dosen`. |
| 6 | Dosen PA menjadwalkan, memberi catatan, dan rekomendasi. | Form update konsultasi PA dosen. |
| 7 | Mahasiswa melihat status dan catatan konsultasi. | Riwayat konsultasi PA mahasiswa. |

## Alur Digital DELTA-MAT

| Tahap | Aktivitas | Fitur SIMTA |
| --- | --- | --- |
| 1 | Admin mengimpor data Excel dari `database/excel`. | `deltamat:import-excel`. |
| 2 | Sistem menyimpan data judul ke `thesis_title_databases`. | Importer `DeltaMatExcelImporter`. |
| 3 | Pengguna membuka halaman DELTA-MAT. | `/database-ta`. |
| 4 | Pengguna memasukkan calon judul atau kata kunci. | Form pencarian. |
| 5 | Sistem menghitung kemiripan judul. | `DatabaseTaController::similarity`. |
| 6 | Sistem menampilkan daftar rekomendasi. | View `database-ta.index`. |
| 7 | Pengguna membuka halaman detail. | `/database-ta/{id}`. |
| 8 | Sistem menampilkan metadata dan preview PDF. | View `database-ta.show`. |

## Status Digitalisasi

| Area | Status |
| --- | --- |
| Bimbingan TA | Sudah tersedia untuk dosen dan mahasiswa. |
| Approval | Sudah tersedia untuk dosen. |
| Seminar/ujian | Sudah tersedia untuk dosen. |
| Repository | Sudah tersedia untuk dosen dan mahasiswa. |
| Q&A | Sudah tersedia untuk dosen dan mahasiswa. |
| Bimbingan PA | Sudah tersedia untuk dosen, mahasiswa, dan admin. |
| DELTA-MAT | Sudah tersedia untuk publik dan admin. |

## Catatan Pengembangan

Alur digital saat ini sudah mendukung proses inti. Pengembangan lanjutan yang direkomendasikan adalah notifikasi status, audit log, upload dokumen langsung, export laporan, dan workflow pengajuan judul dari mahasiswa ke admin atau dosen.
