# Rangkuman Identifikasi Alur Konvensional Bimbingan Tugas Akhir

## Tujuan Dokumen

Dokumen ini merangkum alur konvensional bimbingan tugas akhir yang menjadi dasar kebutuhan digitalisasi SIMTA. Rangkuman disusun berdasarkan fitur yang sudah tersedia dalam proyek: bimbingan TA, approval, seminar/ujian, repository, Q&A, dan DELTA-MAT.

## Alur Konvensional yang Diidentifikasi

| Tahap | Alur konvensional | Masalah yang muncul | Dukungan SIMTA |
| --- | --- | --- | --- |
| 1 | Mahasiswa mencari ide atau referensi judul secara manual. | Referensi tersebar, risiko judul mirip sulit dideteksi. | DELTA-MAT menyediakan pencarian judul dan skor kemiripan. |
| 2 | Mahasiswa menghubungi dosen atau admin untuk memastikan kelayakan judul. | Proses bergantung pada komunikasi personal. | SIMTA menyimpan data bimbingan dan Q&A. |
| 3 | Data bimbingan dicatat oleh dosen atau admin di dokumen terpisah. | Riwayat bimbingan sulit dilacak. | Tabel `thesis_guidances` menyimpan judul, status, progress, dan catatan terakhir. |
| 4 | Persetujuan seminar atau revisi judul dilakukan secara manual. | Status approval tidak selalu terlihat oleh mahasiswa. | Tabel `approvals` dan halaman persetujuan dosen. |
| 5 | Jadwal seminar dan hasil seminar diinformasikan melalui pesan atau dokumen. | Informasi jadwal dan nilai dapat tercecer. | Tabel `seminars` menyimpan jadwal, ruangan, status, nilai, dan feedback. |
| 6 | Dokumen tugas akhir disimpan pada folder atau link terpisah. | Dokumen sulit ditemukan kembali. | Tabel `repositories` menyimpan jenis dokumen, nama file, dan URL. |
| 7 | Pertanyaan mahasiswa disampaikan lewat chat atau pertemuan langsung. | Jawaban tidak terdokumentasi. | Tabel `questions` menyimpan pertanyaan dan jawaban. |

## Masalah Administrasi Konvensional

| No. | Masalah | Dampak |
| --- | --- | --- |
| 1 | Data judul tugas akhir tidak selalu terpusat. | Admin sulit melakukan pencarian dan rekapitulasi. |
| 2 | Riwayat bimbingan tidak terdokumentasi rapi. | Dosen dan mahasiswa sulit menelusuri perkembangan tugas akhir. |
| 3 | Status persetujuan belum mudah dipantau. | Mahasiswa perlu menanyakan status secara langsung. |
| 4 | Jadwal seminar atau ujian dapat berubah tanpa catatan terstruktur. | Risiko miskomunikasi jadwal. |
| 5 | Dokumen repository bergantung pada link eksternal atau folder manual. | Dokumen dapat sulit ditemukan jika tidak diberi metadata. |
| 6 | Komunikasi Q&A tidak masuk arsip akademik. | Pengetahuan dan arahan dosen tidak terdokumentasi. |

## Titik Digitalisasi

| Titik proses | Bentuk digital pada SIMTA |
| --- | --- |
| Pencarian judul | DELTA-MAT `/database-ta`. |
| Validasi awal kemiripan judul | Skor kemiripan dan status `UNIK`, `WASPADA`, `DUPLIKASI`. |
| Monitoring bimbingan | Dashboard dosen dan mahasiswa. |
| Persetujuan | Halaman `/dosen/persetujuan`. |
| Seminar/ujian | Halaman `/dosen/seminar-ujian`. |
| Repository dokumen | Halaman `/dosen/repository` dan `/mahasiswa/repository`. |
| Tanya jawab | Halaman `/dosen/qa` dan `/mahasiswa/qa`. |
| Konsultasi akademik PA | Halaman `/pa/dosen` dan `/pa/mahasiswa`. |

## Kesimpulan

Alur konvensional bimbingan tugas akhir membutuhkan digitalisasi pada aspek pencarian judul, dokumentasi bimbingan, persetujuan, seminar, repository, dan komunikasi. SIMTA sudah mengakomodasi kebutuhan dasar tersebut melalui modul-modul yang tersedia dalam proyek, dengan DELTA-MAT sebagai fitur pendukung validasi awal judul tugas akhir.
