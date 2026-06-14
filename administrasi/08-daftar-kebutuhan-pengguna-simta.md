# Daftar Kebutuhan Pengguna SIMTA

## Tujuan Dokumen

Dokumen ini merangkum kebutuhan pengguna SIMTA berdasarkan peran yang sudah tersedia pada aplikasi, yaitu dosen, mahasiswa, dan admin.

## Kebutuhan Pengguna Dosen

| Kode | Kebutuhan | Fitur terkait |
| --- | --- | --- |
| KD-01 | Dosen dapat login menggunakan akun dosen. | `/dosen/login` |
| KD-02 | Dosen dapat melihat dashboard mahasiswa bimbingan aktif. | `/dosen` |
| KD-03 | Dosen dapat melihat profil dan memperbarui kontak, alamat, status, dan keahlian. | `/dosen/profil` |
| KD-04 | Dosen dapat mencari data bimbingan berdasarkan NIM, nama, dan judul. | `/dosen/bimbingan-ta` |
| KD-05 | Dosen dapat melihat dan memutuskan persetujuan seminar atau revisi. | `/dosen/persetujuan` |
| KD-06 | Dosen dapat melihat jadwal seminar/ujian dan memasukkan nilai serta feedback. | `/dosen/seminar-ujian` |
| KD-07 | Dosen dapat menambahkan dokumen repository berupa jenis dokumen, nama file, dan URL. | `/dosen/repository` |
| KD-08 | Dosen dapat menjawab pertanyaan mahasiswa. | `/dosen/qa` |
| KD-09 | Dosen PA dapat melihat mahasiswa PA, status akademik, konsultasi, dan rekomendasi. | `/pa/dosen` |

## Kebutuhan Pengguna Mahasiswa

| Kode | Kebutuhan | Fitur terkait |
| --- | --- | --- |
| KM-01 | Mahasiswa dapat login dan registrasi akun. | `/mahasiswa/login`, `/mahasiswa/register` |
| KM-02 | Mahasiswa dapat melihat dashboard status bimbingan dan seminar. | `/mahasiswa` |
| KM-03 | Mahasiswa dapat melihat profil mahasiswa. | `/mahasiswa/profil` |
| KM-04 | Mahasiswa dapat melihat data bimbingan tugas akhir. | `/mahasiswa/bimbingan-ta` |
| KM-05 | Mahasiswa dapat melihat dokumen repository tugas akhir. | `/mahasiswa/repository` |
| KM-06 | Mahasiswa dapat melihat daftar pertanyaan dan jawaban dosen. | `/mahasiswa/qa` |
| KM-07 | Mahasiswa dapat mengajukan konsultasi PA. | `/pa/mahasiswa` |
| KM-08 | Mahasiswa dapat melihat riwayat konsultasi PA, IPK, dan total SKS. | `/pa/mahasiswa` |
| KM-09 | Mahasiswa dapat mencari referensi judul dan memeriksa kemiripan judul. | `/database-ta` |
| KM-10 | Mahasiswa dapat membuka detail data judul dan preview dokumen PDF. | `/database-ta/{id}` |

## Kebutuhan Pengguna Admin

| Kode | Kebutuhan | Fitur terkait |
| --- | --- | --- |
| KA-01 | Admin dapat login menggunakan akun admin. | `/admin/login` |
| KA-02 | Admin dapat membuka dashboard admin. | `/admin` |
| KA-03 | Admin dapat mengelola penetapan dosen PA. | `/admin/bimbingan-pa` |
| KA-04 | Admin dapat mengelola data IPK/SKS mahasiswa. | `/admin/bimbingan-pa` |
| KA-05 | Admin dapat mengelola konsultasi PA. | `/admin/bimbingan-pa` |
| KA-06 | Admin dapat menambah data judul DELTA-MAT. | `/admin/database-ta` |
| KA-07 | Admin dapat mengubah data judul DELTA-MAT. | `/admin/database-ta/{id}/edit` |
| KA-08 | Admin dapat menghapus data judul DELTA-MAT. | `/admin/database-ta` |
| KA-09 | Admin dapat mencari data DELTA-MAT berdasarkan judul, NIM, mahasiswa, dan pembimbing. | `/admin/database-ta?q=...` |
| KA-10 | Admin dapat mengimpor data judul dari file Excel pada `database/excel`. | Command `deltamat:import-excel` |

## Prioritas Kebutuhan

| Prioritas | Kebutuhan |
| --- | --- |
| Tinggi | Login role, dashboard, bimbingan TA, DELTA-MAT, admin data, repository. |
| Sedang | Q&A, seminar/ujian, bimbingan PA, preview dokumen PDF. |
| Lanjutan | Audit log, notifikasi, upload file langsung, export laporan, pencarian semantik. |

## Kesimpulan

Kebutuhan pengguna SIMTA berpusat pada transparansi status tugas akhir, kemudahan dokumentasi bimbingan, pengelolaan data oleh admin, dan pencarian judul melalui DELTA-MAT. Implementasi proyek sudah menyediakan fitur utama untuk kebutuhan dasar ketiga peran.
