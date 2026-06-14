# Dokumen Analisis Kebutuhan dan Identifikasi Permasalahan Administrasi Tugas Akhir

## Identitas Dokumen

| Komponen | Keterangan |
| --- | --- |
| Nama fitur | DELTA-MAT |
| Kepanjangan | Database Tugas Akhir Matematika |
| Modul aplikasi | DELTA-MAT |
| Sistem induk | SIMTA - Sistem Informasi Manajemen Tugas Akhir |
| Fokus dokumen | Analisis kebutuhan dan identifikasi permasalahan administrasi tugas akhir |
| Tanggal penyusunan | 11 Juni 2026 |

## Ringkasan

DELTA-MAT adalah fitur yang digunakan untuk menyimpan, menelusuri, dan mengelola data judul tugas akhir mahasiswa. Fitur ini mendukung proses administrasi tugas akhir dengan menyediakan repositori data judul yang dapat dicari oleh pengguna dan dikelola oleh admin.

Dalam implementasi proyek saat ini, DELTA-MAT memiliki dua area utama:

- Halaman publik `DELTA-MAT` pada rute `/database-ta` untuk pencarian judul dan analisis kemiripan.
- Halaman admin `Kelola DELTA-MAT` pada rute `/admin/database-ta` untuk menambah, mengubah, menghapus, dan mencari data judul tugas akhir.

Dokumen ini membahas kebutuhan pengguna dan masalah administrasi yang ingin diselesaikan oleh DELTA-MAT, khususnya pada proses pengarsipan, pencarian, validasi, dan rekapitulasi judul tugas akhir.

## Latar Belakang

Administrasi tugas akhir pada program studi membutuhkan data yang rapi, mudah dicari, dan dapat digunakan sebagai dasar pengambilan keputusan. Data judul tugas akhir biasanya dibutuhkan untuk:

- Mengecek apakah judul yang diajukan mahasiswa sudah pernah digunakan atau mirip dengan judul sebelumnya.
- Melihat riwayat penelitian berdasarkan mahasiswa, NIM, dosen pembimbing, dan tanggal pengajuan.
- Menyiapkan rekap data untuk kebutuhan administrasi program studi.
- Membantu dosen dan pengelola program studi mengarahkan topik penelitian mahasiswa.

Tanpa sistem database yang terpusat, proses tersebut cenderung bergantung pada file spreadsheet, pencarian manual, atau arsip terpisah. Kondisi ini meningkatkan risiko data ganda, keterlambatan pencarian, dan kesalahan administrasi.

## Tujuan DELTA-MAT

Tujuan utama DELTA-MAT adalah menyediakan basis data tugas akhir yang terstruktur dan mudah digunakan. Secara khusus, fitur ini bertujuan untuk:

- Menyediakan pusat data judul tugas akhir mahasiswa.
- Memudahkan pencarian judul berdasarkan kata kunci.
- Menampilkan indikasi kemiripan judul untuk mendukung validasi awal.
- Membantu admin mengelola data tugas akhir secara langsung melalui aplikasi.
- Mengurangi ketergantungan pada pencatatan manual berbasis file.
- Mendukung transparansi informasi judul tugas akhir bagi mahasiswa, dosen, dan pengelola program studi.

## Ruang Lingkup

Ruang lingkup DELTA-MAT pada dokumen ini meliputi:

- Data identitas mahasiswa yang terkait dengan tugas akhir.
- Data judul tugas akhir.
- Data dosen pembimbing 1 dan pembimbing 2.
- Data kontak mahasiswa, seperti email dan nomor WhatsApp.
- Link dokumen tugas akhir atau dokumen pendukung.
- Pencarian data judul oleh pengguna.
- Analisis kemiripan judul berbasis kata kunci.
- Pengelolaan data oleh admin.

Ruang lingkup yang belum dibahas sebagai fitur utama pada dokumen ini:

- Unggah file langsung ke server aplikasi.
- Verifikasi substansi akademik oleh tim penguji.
- Workflow persetujuan formal judul.
- Integrasi dengan sistem akademik eksternal.
- Audit log perubahan data.

## Pemangku Kepentingan

| Pemangku kepentingan | Kebutuhan utama |
| --- | --- |
| Admin program studi | Mengelola data judul tugas akhir, memperbarui data pembimbing, dan menjaga kerapian database. |
| Mahasiswa | Mencari referensi judul dan memeriksa indikasi kemiripan sebelum mengajukan topik. |
| Dosen pembimbing | Melihat riwayat judul dan topik mahasiswa yang relevan dengan bidang keahlian. |
| Koordinator tugas akhir | Memantau sebaran topik, pembimbing, dan data administrasi tugas akhir. |
| Pimpinan program studi | Mendapatkan dasar data untuk monitoring dan pelaporan akademik. |

## Kondisi Sistem Saat Ini

Berdasarkan struktur proyek, fitur DELTA-MAT telah memiliki komponen berikut:

- Rute publik `/database-ta` untuk halaman pencarian DELTA-MAT.
- Rute admin `/admin/database-ta` untuk pengelolaan DELTA-MAT.
- Controller publik `DatabaseTaController`.
- Controller admin `AdminDatabaseTaController`.
- View publik `resources/views/database-ta/index.blade.php`.
- View admin `resources/views/admin/database-ta/index.blade.php`.
- Tabel database `thesis_title_databases`.
- Seed data awal dari `database/seeders/data/database_judul.json`.

Kolom data yang digunakan pada tabel `thesis_title_databases` adalah:

| Kolom | Keterangan |
| --- | --- |
| `id` | Identitas unik data. |
| `submission_date` | Tanggal pengajuan atau pencatatan judul. |
| `phone` | Nomor kontak mahasiswa. |
| `email` | Email mahasiswa. |
| `nim` | Nomor induk mahasiswa. |
| `student_name` | Nama mahasiswa. |
| `title` | Judul tugas akhir. |
| `supervisor_1` | Nama pembimbing 1. |
| `supervisor_1_nip` | NIP pembimbing 1. |
| `supervisor_2` | Nama pembimbing 2. |
| `supervisor_2_nip` | NIP pembimbing 2. |
| `document_url` | Link dokumen tugas akhir atau dokumen pendukung. |
| `created_at` | Waktu data dibuat. |
| `updated_at` | Waktu data terakhir diperbarui. |

## Identifikasi Permasalahan Administrasi

### 1. Data Judul Tugas Akhir Tersebar

Permasalahan:

- Data judul sering tersimpan dalam file terpisah, seperti spreadsheet, dokumen lokal, atau arsip per periode.
- Akses data bergantung pada pemilik file.
- Pencarian data lama membutuhkan waktu.

Dampak:

- Admin sulit memastikan kelengkapan data.
- Mahasiswa dan dosen tidak memiliki rujukan cepat untuk mencari riwayat judul.
- Risiko kehilangan atau duplikasi data meningkat.

Kebutuhan:

- Sistem database terpusat yang menyimpan data judul tugas akhir secara konsisten.
- Halaman pencarian yang dapat digunakan tanpa membuka file spreadsheet manual.

### 2. Validasi Judul Masih Manual

Permasalahan:

- Pengecekan kemiripan judul dilakukan dengan membaca daftar judul secara manual.
- Judul yang mirip dapat terlewat jika kata yang digunakan sedikit berbeda.
- Tidak ada indikator cepat untuk membedakan judul unik, perlu perhatian, atau berisiko duplikasi.

Dampak:

- Proses pengajuan judul membutuhkan waktu lebih lama.
- Potensi topik berulang tidak terdeteksi sejak awal.
- Keputusan awal mengenai kelayakan judul kurang berbasis data.

Kebutuhan:

- Fitur pencarian judul yang menampilkan persentase kemiripan.
- Kategori status kemiripan untuk membantu interpretasi hasil.
- Rekomendasi data judul yang paling relevan dengan kata kunci pencarian.

### 3. Format Data Tidak Seragam

Permasalahan:

- Penulisan nama mahasiswa, NIM, pembimbing, tanggal, dan judul dapat berbeda-beda.
- Data pembimbing bisa tidak lengkap, terutama NIP atau pembimbing kedua.
- Kontak mahasiswa dan link dokumen tidak selalu tersedia.

Dampak:

- Rekap administrasi menjadi tidak konsisten.
- Pencarian berdasarkan pembimbing atau mahasiswa dapat menghasilkan data yang tidak lengkap.
- Proses pembaruan data membutuhkan pemeriksaan manual tambahan.

Kebutuhan:

- Form input admin dengan field yang jelas.
- Validasi minimal untuk data wajib seperti NIM, nama mahasiswa, dan judul.
- Penyimpanan field pembimbing, kontak, dan link dokumen secara terstruktur.

### 4. Pengelolaan Data Bergantung pada Admin

Permasalahan:

- Data tugas akhir perlu ditambah, diperbarui, dan dihapus oleh pihak yang berwenang.
- Tanpa halaman admin, perubahan data harus dilakukan langsung pada database atau file sumber.
- Pengelolaan manual meningkatkan risiko salah edit.

Dampak:

- Proses pembaruan data tidak efisien.
- Data lama atau salah dapat tetap muncul di halaman pencarian.
- Akuntabilitas pengelolaan data menjadi rendah.

Kebutuhan:

- Halaman admin untuk operasi tambah, edit, hapus, dan cari data.
- Pembatasan akses agar hanya pengguna dengan role admin yang dapat mengelola data.
- Pesan status setelah data berhasil disimpan, diperbarui, atau dihapus.

### 5. Rekapitulasi dan Pelacakan Data Belum Optimal

Permasalahan:

- Data tugas akhir dibutuhkan untuk laporan program studi, tetapi proses rekap masih dapat bergantung pada pencarian manual.
- Belum tersedia ringkasan jumlah data berdasarkan tahun, pembimbing, atau status dokumen.
- Belum tersedia fitur ekspor data dari halaman admin.

Dampak:

- Penyusunan laporan administrasi memerlukan pekerjaan tambahan.
- Monitoring beban pembimbing dan tren topik penelitian belum maksimal.
- Evaluasi kebijakan akademik belum sepenuhnya berbasis data.

Kebutuhan:

- Dasar data yang rapi sebagai fondasi rekap.
- Fitur lanjutan seperti filter tahun, filter pembimbing, dan ekspor data.
- Standar kelengkapan metadata untuk seluruh data tugas akhir.

## Kebutuhan Fungsional

| Kode | Kebutuhan | Prioritas |
| --- | --- | --- |
| KF-01 | Sistem menyediakan halaman publik untuk mencari judul tugas akhir. | Tinggi |
| KF-02 | Sistem menerima input kata kunci atau calon judul dari pengguna. | Tinggi |
| KF-03 | Sistem menghitung persentase kemiripan antara input pengguna dan judul yang tersimpan. | Tinggi |
| KF-04 | Sistem menampilkan status `UNIK`, `WASPADA`, atau `DUPLIKASI` berdasarkan skor kemiripan tertinggi. | Tinggi |
| KF-05 | Sistem menampilkan daftar rekomendasi judul yang relevan. | Tinggi |
| KF-06 | Admin dapat menambah data judul tugas akhir. | Tinggi |
| KF-07 | Admin dapat mengubah data judul tugas akhir. | Tinggi |
| KF-08 | Admin dapat menghapus data judul tugas akhir. | Tinggi |
| KF-09 | Admin dapat mencari data berdasarkan judul, NIM, mahasiswa, dan pembimbing. | Tinggi |
| KF-10 | Sistem menyimpan link dokumen tugas akhir jika tersedia. | Sedang |
| KF-11 | Sistem mencegah data ganda berdasarkan kombinasi NIM dan judul. | Sedang |
| KF-12 | Sistem menyediakan paginasi pada daftar data admin. | Sedang |

## Kebutuhan Nonfungsional

| Kode | Kebutuhan | Penjelasan |
| --- | --- | --- |
| KNF-01 | Kemudahan penggunaan | Antarmuka harus sederhana agar admin dapat mengelola data tanpa pelatihan teknis khusus. |
| KNF-02 | Keamanan akses | Operasi pengelolaan data hanya boleh dilakukan oleh admin yang sudah login. |
| KNF-03 | Konsistensi data | Field wajib harus divalidasi sebelum data disimpan. |
| KNF-04 | Keterbacaan informasi | Hasil pencarian harus menampilkan judul, nama mahasiswa, NIM, pembimbing, tanggal, dan skor kemiripan. |
| KNF-05 | Responsivitas | Halaman dapat digunakan pada layar desktop dan perangkat mobile. |
| KNF-06 | Kinerja pencarian | Pencarian harus cukup cepat untuk dataset tugas akhir program studi. |
| KNF-07 | Pemeliharaan | Struktur data dan controller harus mudah dikembangkan untuk fitur filter, ekspor, dan impor. |

## Aturan Bisnis

- Setiap data tugas akhir wajib memiliki NIM, nama mahasiswa, dan judul.
- Kombinasi NIM dan judul harus unik agar data yang sama tidak tersimpan berulang.
- Data pembimbing, kontak, tanggal, dan link dokumen bersifat opsional tetapi direkomendasikan untuk dilengkapi.
- Status kemiripan ditentukan dari skor tertinggi hasil perbandingan input pengguna dengan data judul yang tersimpan.
- Interpretasi status kemiripan:
  - `UNIK`: skor kurang dari 30 persen.
  - `WASPADA`: skor mulai dari 30 persen sampai kurang dari 70 persen.
  - `DUPLIKASI`: skor 70 persen atau lebih.
- Status kemiripan adalah indikator awal, bukan keputusan akademik final.

## Data yang Dibutuhkan

| Data | Sumber | Pengguna |
| --- | --- | --- |
| Judul tugas akhir | Admin atau data awal dari spreadsheet | Mahasiswa, dosen, admin |
| NIM | Admin atau data akademik | Admin |
| Nama mahasiswa | Admin atau data akademik | Mahasiswa, dosen, admin |
| Pembimbing 1 dan NIP | Admin atau data penetapan pembimbing | Dosen, admin |
| Pembimbing 2 dan NIP | Admin atau data penetapan pembimbing | Dosen, admin |
| Tanggal pengajuan | Admin atau arsip pengajuan | Admin |
| Email dan nomor WhatsApp | Mahasiswa atau admin | Admin |
| Link dokumen | Repository atau penyimpanan dokumen | Mahasiswa, dosen, admin |

## Risiko dan Mitigasi

| Risiko | Dampak | Mitigasi |
| --- | --- | --- |
| Data lama tidak lengkap | Hasil pencarian kurang informatif | Lengkapi metadata bertahap melalui halaman admin. |
| Judul ditulis dengan variasi berbeda | Skor kemiripan tidak selalu akurat | Gunakan hasil kemiripan sebagai indikator awal dan tetap lakukan validasi akademik. |
| Admin salah menghapus data | Data penting hilang | Pertahankan konfirmasi hapus dan tambahkan backup atau audit log pada pengembangan berikutnya. |
| Link dokumen rusak | Pengguna tidak dapat membuka dokumen | Lakukan pemeriksaan berkala pada `document_url`. |
| Dataset semakin besar | Pencarian menjadi lambat | Tambahkan indexing, pagination publik, atau metode pencarian yang lebih efisien. |

## Indikator Keberhasilan

DELTA-MAT dianggap berhasil mendukung administrasi tugas akhir jika:

- Admin dapat mengelola data judul tanpa membuka database secara langsung.
- Pengguna dapat menemukan riwayat judul tugas akhir melalui kata kunci.
- Sistem dapat menampilkan skor kemiripan dan status indikatif.
- Data tugas akhir tersimpan dalam format yang lebih terstruktur.
- Proses pencarian judul dan validasi awal menjadi lebih cepat dibandingkan pencarian manual.

## Rekomendasi Pengembangan Lanjutan

- Menambahkan fitur impor file spreadsheet secara langsung dari halaman admin.
- Menambahkan fitur ekspor data ke Excel atau CSV.
- Menambahkan filter berdasarkan tahun, pembimbing, program studi, dan status dokumen.
- Menambahkan audit log perubahan data.
- Menambahkan role khusus koordinator tugas akhir.
- Menambahkan normalisasi nama pembimbing agar rekap beban pembimbing lebih akurat.
- Menambahkan metode kemiripan yang lebih kuat, misalnya pembobotan kata kunci atau stemming bahasa Indonesia.
