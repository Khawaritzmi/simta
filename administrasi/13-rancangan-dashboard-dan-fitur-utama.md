# Rancangan Dashboard dan Fitur Utama SIMTA

## Tujuan Dokumen

Dokumen ini menjelaskan rancangan dashboard dan fitur utama SIMTA berdasarkan halaman yang sudah tersedia di proyek.

## Rancangan Dashboard Dosen

| Komponen | Isi |
| --- | --- |
| Header | Nama sistem, tombol logout, akses cepat. |
| Sidebar | Home, Profil Dosen, Bimbingan TA, Persetujuan, Seminar/Ujian, Repository, Q&A, Manual Aplikasi. |
| Konten utama | Daftar mahasiswa bimbingan aktif. |
| Data yang ditampilkan | Nama mahasiswa, NIM, judul, status, progres, status seminar, catatan terakhir. |

Fitur utama dashboard dosen:

| No. | Fitur | Keterangan |
| --- | --- | --- |
| 1 | Monitoring bimbingan | Dosen melihat mahasiswa bimbingan aktif. |
| 2 | Filter bimbingan | Pencarian berdasarkan NIM, nama, dan judul. |
| 3 | Approval | Dosen menyetujui atau menolak pengajuan. |
| 4 | Seminar/ujian | Dosen mengisi nilai dan feedback. |
| 5 | Repository | Dosen menambahkan dokumen tugas akhir. |
| 6 | Q&A | Dosen menjawab pertanyaan mahasiswa. |

## Rancangan Dashboard Mahasiswa

| Komponen | Isi |
| --- | --- |
| Header | SIMTA Mahasiswa dan tombol logout. |
| Sidebar | Home, Profil Mahasiswa, Bimbingan TA, Repository, Q&A. |
| Konten utama | Ringkasan bimbingan dan seminar mahasiswa. |
| Data yang ditampilkan | Judul, dosen pembimbing, progres, status seminar, jadwal seminar. |

Fitur utama dashboard mahasiswa:

| No. | Fitur | Keterangan |
| --- | --- | --- |
| 1 | Melihat bimbingan TA | Mahasiswa melihat data judul, dosen, status, dan progres. |
| 2 | Melihat repository | Mahasiswa melihat dokumen yang terkait tugas akhir. |
| 3 | Melihat Q&A | Mahasiswa melihat pertanyaan dan jawaban dosen. |
| 4 | Melihat seminar | Mahasiswa melihat jadwal dan status seminar. |

## Rancangan Dashboard Admin

| Komponen | Isi |
| --- | --- |
| Panel admin | Tombol Kelola Bimbingan PA, Kelola DELTA-MAT, logout. |
| Admin Bimbingan PA | Form assignment PA, form IPK/SKS, form konsultasi, tabel data. |
| Admin DELTA-MAT | Form tambah/edit data judul, pencarian data, tabel data, aksi edit/hapus. |

Fitur utama admin:

| No. | Fitur | Keterangan |
| --- | --- | --- |
| 1 | Kelola assignment PA | Menetapkan dosen PA untuk mahasiswa. |
| 2 | Kelola data akademik PA | Menambah dan mengubah IPK/SKS mahasiswa. |
| 3 | Kelola konsultasi PA | Menambah, mengubah, dan menghapus riwayat konsultasi. |
| 4 | Kelola DELTA-MAT | Menambah, mengubah, menghapus, dan mencari data judul. |
| 5 | Import Excel DELTA-MAT | Merge data judul dari `database/excel`. |

## Rancangan Dashboard DELTA-MAT

| Komponen | Isi |
| --- | --- |
| Header | SIMTA, Beranda, Login Admin atau Kelola DELTA-MAT. |
| Judul halaman | DELTA-MAT. |
| Form pencarian | Input calon judul atau kata kunci. |
| Kartu analisis | Skor kemiripan dan status UNIK/WASPADA/DUPLIKASI. |
| Daftar rekomendasi | Judul, mahasiswa, NIM, pembimbing, tanggal, skor, tombol detail/dokumen. |
| Halaman detail | Metadata lengkap dan preview dokumen PDF. |

## Prioritas Tampilan

| Prioritas | Rancangan |
| --- | --- |
| Tinggi | Setiap dashboard langsung menampilkan pekerjaan utama sesuai role. |
| Tinggi | Navigasi sidebar harus konsisten antara halaman dosen dan mahasiswa. |
| Tinggi | DELTA-MAT harus memudahkan pencarian judul dan akses dokumen. |
| Sedang | Admin perlu tabel yang mudah discan dan form input ringkas. |
| Sedang | Tampilan mobile tetap dapat membaca tabel dengan scroll horizontal. |

## Kesimpulan

Rancangan dashboard SIMTA berorientasi pada peran pengguna. Dosen fokus pada pengelolaan bimbingan, mahasiswa fokus pada pemantauan status dan dokumen, admin fokus pada manajemen data, sedangkan DELTA-MAT fokus pada pencarian referensi judul dan dokumen.
