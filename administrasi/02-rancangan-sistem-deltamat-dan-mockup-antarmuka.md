# Dokumen Rancangan Sistem DELTA-MAT dan Mockup Antarmuka

## Identitas Dokumen

| Komponen | Keterangan |
| --- | --- |
| Nama fitur | DELTA-MAT |
| Kepanjangan | Database Tugas Akhir Matematika |
| Modul aplikasi | DELTA-MAT |
| Sistem induk | SIMTA - Sistem Informasi Manajemen Tugas Akhir |
| Fokus dokumen | Rancangan sistem dan mockup antarmuka |
| Tanggal penyusunan | 11 Juni 2026 |

## Ringkasan Rancangan

DELTA-MAT dirancang sebagai modul pencarian dan pengelolaan data tugas akhir pada SIMTA atau Sistem Informasi Manajemen Tugas Akhir. Modul ini memiliki dua sisi penggunaan:

- Sisi publik untuk pencarian judul tugas akhir dan pemeriksaan indikasi kemiripan.
- Sisi admin untuk pengelolaan data judul tugas akhir.

Rancangan ini mengikuti implementasi proyek saat ini, yaitu:

- Halaman publik berada pada rute `/database-ta`.
- Halaman admin berada pada rute `/admin/database-ta`.
- Data utama disimpan pada tabel `thesis_title_databases`.
- Status kemiripan menggunakan kategori `UNIK`, `WASPADA`, dan `DUPLIKASI`.

## Tujuan Rancangan

Rancangan sistem DELTA-MAT disusun untuk:

- Menjelaskan arsitektur fitur DELTA-MAT.
- Menentukan alur kerja pengguna dan admin.
- Menjelaskan struktur data yang digunakan.
- Menyediakan gambaran antarmuka dalam bentuk mockup Markdown.
- Menjadi acuan pengembangan dan evaluasi fitur berikutnya.

## Aktor Sistem

| Aktor | Hak akses | Aktivitas utama |
| --- | --- | --- |
| Pengguna publik | Tanpa login | Membuka DELTA-MAT, memasukkan kata kunci judul, melihat skor kemiripan dan rekomendasi. |
| Admin | Login sebagai admin | Menambah, mencari, mengubah, dan menghapus data tugas akhir. |
| Mahasiswa | Pengguna informasi | Mencari referensi judul dan melihat indikasi kemiripan calon judul. |
| Dosen | Pengguna informasi | Menelusuri riwayat topik dan data pembimbingan tugas akhir. |

## Arsitektur Modul

```text
Pengguna Publik
      |
      v
/database-ta
      |
      v
DatabaseTaController@index
      |
      v
thesis_title_databases
      |
      v
resources/views/database-ta/index.blade.php
```

```text
Admin Login
      |
      v
/admin/database-ta
      |
      v
AdminDatabaseTaController
      |
      +--> index: daftar dan pencarian data
      +--> store: tambah data
      +--> edit: buka mode edit
      +--> update: simpan perubahan
      +--> destroy: hapus data
      |
      v
thesis_title_databases
      |
      v
resources/views/admin/database-ta/index.blade.php
```

## Rancangan Data

### Tabel Utama

Nama tabel: `thesis_title_databases`

| Field | Tipe konseptual | Wajib | Keterangan |
| --- | --- | --- | --- |
| `id` | Integer | Ya | Primary key. |
| `submission_date` | String | Tidak | Tanggal pengajuan atau pencatatan. |
| `phone` | String | Tidak | Nomor WhatsApp mahasiswa. |
| `email` | String | Tidak | Email mahasiswa. |
| `nim` | String | Ya | Nomor induk mahasiswa. |
| `student_name` | String | Ya | Nama mahasiswa. |
| `title` | Text | Ya | Judul tugas akhir. |
| `supervisor_1` | String | Tidak | Nama pembimbing 1. |
| `supervisor_1_nip` | String | Tidak | NIP pembimbing 1. |
| `supervisor_2` | String | Tidak | Nama pembimbing 2. |
| `supervisor_2_nip` | String | Tidak | NIP pembimbing 2. |
| `document_url` | String | Tidak | Link dokumen tugas akhir. |
| `created_at` | Timestamp | Ya | Waktu data dibuat. |
| `updated_at` | Timestamp | Ya | Waktu data diperbarui. |

### Aturan Unik Data

Sistem menggunakan kombinasi `nim` dan `title` sebagai data unik. Artinya, satu mahasiswa tidak boleh memiliki data judul yang sama tersimpan lebih dari satu kali.

```text
Unique key: nim + title
```

## Rancangan Proses

### 1. Proses Pencarian Judul oleh Pengguna

```text
Pengguna membuka /database-ta
      |
      v
Pengguna mengisi calon judul atau kata kunci
      |
      v
Sistem mengambil seluruh data judul tugas akhir
      |
      v
Sistem menghitung kemiripan input dengan setiap judul
      |
      v
Sistem mengurutkan rekomendasi berdasarkan skor tertinggi
      |
      v
Sistem menampilkan skor tertinggi, status, dan daftar rekomendasi
```

### 2. Proses Tambah Data oleh Admin

```text
Admin login
      |
      v
Admin membuka /admin/database-ta
      |
      v
Admin mengisi form data judul
      |
      v
Sistem melakukan validasi field
      |
      v
Sistem menyimpan data ke thesis_title_databases
      |
      v
Sistem menampilkan pesan berhasil
```

### 3. Proses Edit Data oleh Admin

```text
Admin memilih tombol Edit pada salah satu data
      |
      v
Sistem membuka form dengan data terpilih
      |
      v
Admin memperbarui field yang diperlukan
      |
      v
Sistem melakukan validasi
      |
      v
Sistem menyimpan perubahan
      |
      v
Sistem menampilkan pesan berhasil
```

### 4. Proses Hapus Data oleh Admin

```text
Admin memilih tombol Hapus
      |
      v
Sistem menampilkan konfirmasi hapus
      |
      v
Admin menyetujui konfirmasi
      |
      v
Sistem menghapus data dari thesis_title_databases
      |
      v
Sistem menampilkan pesan berhasil
```

## Rancangan Algoritma Kemiripan

Rancangan saat ini menggunakan pendekatan kemiripan kata sederhana:

1. Input pengguna dan judul yang tersimpan diubah menjadi huruf kecil.
2. Karakter selain huruf, angka, dan spasi dibersihkan.
3. Kalimat dipecah menjadi daftar kata.
4. Kata dengan panjang kurang dari atau sama dengan 2 karakter diabaikan.
5. Kata unik dari input dibandingkan dengan kata unik dari judul database.
6. Skor dihitung dari jumlah kata yang beririsan dibandingkan dengan jumlah gabungan kata unik.

Rumus konseptual:

```text
skor = jumlah_kata_sama / jumlah_seluruh_kata_unik * 100
```

Kategori status:

| Rentang skor | Status | Makna administrasi |
| --- | --- | --- |
| 0 sampai kurang dari 30 persen | `UNIK` | Judul relatif berbeda dari data yang tersimpan. |
| 30 sampai kurang dari 70 persen | `WASPADA` | Judul memiliki kemiripan sedang dan perlu ditinjau. |
| 70 sampai 100 persen | `DUPLIKASI` | Judul sangat mirip dan perlu validasi ketat. |

Catatan: status ini adalah indikator awal administrasi, bukan keputusan akademik final.

## Rancangan Validasi Input Admin

| Field | Validasi |
| --- | --- |
| `nim` | Wajib, maksimal 255 karakter. |
| `student_name` | Wajib, maksimal 255 karakter. |
| `title` | Wajib, maksimal 2000 karakter. |
| `email` | Opsional, harus berformat email, maksimal 255 karakter. |
| `submission_date` | Opsional, maksimal 255 karakter. |
| `phone` | Opsional, maksimal 255 karakter. |
| `supervisor_1` | Opsional, maksimal 255 karakter. |
| `supervisor_1_nip` | Opsional, maksimal 255 karakter. |
| `supervisor_2` | Opsional, maksimal 255 karakter. |
| `supervisor_2_nip` | Opsional, maksimal 255 karakter. |
| `document_url` | Opsional, maksimal 255 karakter. |

## Rancangan Navigasi

```text
Beranda
  |
  +-- DELTA-MAT
  |     |
  |     +-- Pencarian judul dan rekomendasi kemiripan
  |
  +-- Login Admin
        |
        +-- Dashboard Admin
              |
              +-- Kelola DELTA-MAT
                    |
                    +-- Tambah data
                    +-- Cari data
                    +-- Edit data
                    +-- Hapus data
```

## Mockup Antarmuka Publik: DELTA-MAT

### Tujuan Layar

Layar ini digunakan oleh pengguna untuk memasukkan calon judul atau kata kunci tugas akhir, lalu melihat status kemiripan dan daftar judul yang relevan.

### Struktur Layar

```text
+--------------------------------------------------------------------------------+
| SIMTA                                                  Beranda | Login Admin     |
+--------------------------------------------------------------------------------+

                              DELTA-MAT
       Telusuri judul tugas akhir dan lihat indikasi kemiripan terhadap
                    data repository yang sudah tersimpan.

+---------------------------------------------------------------------+----------+
| Masukkan judul tugas akhir                                          |  Cari    |
+---------------------------------------------------------------------+----------+

+--------------------------------------------------------------------------------+
| Analisis Kemiripan Judul                                             |
|                                                                                |
|  42.86%     [ UNIK -------- WASPADA -------- DUPLIKASI ]                       |
|                                                                                |
|  Tingkat Kemiripan: WASPADA (42.86%)                                           |
+--------------------------------------------------------------------------------+

Artikel yang direkomendasikan

+--------------------------------------------------------------------------------+
| DESKRIPSI KEMAMPUAN PEMECAHAN MASALAH MATEMATIKA ...                          |
| A. Nurul Hikmah - 123456789 | Pembimbing: Dr. Nama Pembimbing                 |
| [Kecocokan Sedang] [42.86%] [22 April 2026] [Pembimbing 2: ...] [Dokumen]      |
+--------------------------------------------------------------------------------+

+--------------------------------------------------------------------------------+
| ANALISIS KESALAHAN SISWA DALAM MENYELESAIKAN SOAL ...                         |
| M. Ilham - 987654321 | Pembimbing: Dr. Nama Pembimbing                       |
| [Kecocokan Rendah] [18.75%] [Tanpa tanggal]                                    |
+--------------------------------------------------------------------------------+
```

### Komponen Antarmuka

| Komponen | Fungsi |
| --- | --- |
| Topbar | Menampilkan identitas sistem dan tautan navigasi. |
| Judul halaman | Memberi konteks bahwa pengguna sedang berada di DELTA-MAT. |
| Form pencarian | Menerima input judul atau kata kunci tugas akhir. |
| Kartu analisis | Menampilkan skor tertinggi dan status kemiripan. |
| Bar status | Memberi visualisasi rentang `UNIK`, `WASPADA`, dan `DUPLIKASI`. |
| Daftar rekomendasi | Menampilkan judul yang paling relevan dengan pencarian. |
| Chip informasi | Menampilkan label kemiripan, skor, tanggal, pembimbing, dan link dokumen. |

## Mockup Antarmuka Admin: Kelola DELTA-MAT

### Tujuan Layar

Layar ini digunakan admin untuk mengelola data tugas akhir. Admin dapat menambah data baru, mencari data yang sudah tersimpan, mengedit data, dan menghapus data.

### Struktur Layar

```text
+--------------------------------------------------------------------------------+
| ADMIN DELTA-MAT                           Lihat Public | Dashboard Admin | Logout |
+--------------------------------------------------------------------------------+

Kelola DELTA-MAT

+--------------------------------------+-----------------------------------------+
| Tambah Data Judul                    | Data dari database_judul.xlsx           |
|                                      |                                         |
| Tanggal Pengajuan   [22 April 2026]  | +-----------------------------------+---+ |
| NIM                 [123456789]      | | Cari judul, NIM, mahasiswa ...    |Cari |
| Nama Mahasiswa      [Nama Lengkap]   | +-----------------------------------+---+ |
| Judul Tugas Akhir   [textarea]       |                                         |
| Email               [email]          | +------+-------------+-----------+------+ |
| No. WA              [nomor]          | | NIM  | Mahasiswa   | Judul     | Aksi | |
| Pembimbing 1        [nama]           | +------+-------------+-----------+------+ |
| NIP Pembimbing 1    [nip]            | | ...  | ...         | ...       |Edit  | |
| Pembimbing 2        [nama]           | |      |             |           |Hapus | |
| NIP Pembimbing 2    [nip]            | +------+-------------+-----------+------+ |
| Link Dokumen        [url]            |                                         |
|                                      | Menampilkan 1-15 dari 120 data           |
| [Simpan Data]                        | [Sebelumnya] Halaman 1 / 8 [Berikutnya] |
+--------------------------------------+-----------------------------------------+
```

### Komponen Antarmuka

| Komponen | Fungsi |
| --- | --- |
| Topbar admin | Menampilkan identitas halaman dan akses cepat ke halaman publik, dashboard admin, dan logout. |
| Form tambah/edit | Digunakan untuk memasukkan atau memperbarui data judul tugas akhir. |
| Pesan status | Memberi umpan balik setelah proses simpan, update, atau hapus. |
| Pesan error | Menampilkan validasi input yang gagal. |
| Form pencarian admin | Mencari data berdasarkan judul, NIM, nama mahasiswa, atau pembimbing. |
| Tabel data | Menampilkan daftar data tugas akhir. |
| Tombol Edit | Membuka data terpilih ke mode edit. |
| Tombol Hapus | Menghapus data setelah konfirmasi. |
| Paginasi | Membatasi jumlah data per halaman agar tampilan tetap mudah dibaca. |

## Rancangan Respons Sistem

| Kondisi | Respons sistem |
| --- | --- |
| Pengguna membuka `/database-ta` tanpa kata kunci | Sistem menampilkan daftar data dengan skor 0 persen. |
| Pengguna mencari judul | Sistem menghitung kemiripan dan mengurutkan data berdasarkan skor tertinggi. |
| Tidak ada data tugas akhir | Sistem menampilkan pesan data belum tersedia. |
| Admin berhasil menyimpan data | Sistem menampilkan pesan `Data DELTA-MAT berhasil disimpan.` |
| Admin berhasil memperbarui data | Sistem menampilkan pesan `Data DELTA-MAT berhasil diperbarui.` |
| Admin berhasil menghapus data | Sistem menampilkan pesan `Data DELTA-MAT berhasil dihapus.` |
| Admin mengisi email tidak valid | Sistem menampilkan pesan validasi. |
| Pengguna non-admin membuka halaman admin | Sistem menolak akses dengan status 403. |

## Rancangan Keamanan

- Halaman publik `/database-ta` dapat diakses tanpa login.
- Halaman admin `/admin/database-ta` hanya dapat digunakan oleh pengguna yang sudah login.
- Controller admin memastikan role pengguna adalah `admin`.
- Form admin menggunakan proteksi CSRF bawaan Laravel.
- Operasi hapus menggunakan method `DELETE` dan konfirmasi di sisi antarmuka.
- Data wajib divalidasi sebelum disimpan ke database.

## Rancangan Pengembangan Lanjutan

| Fitur lanjutan | Manfaat |
| --- | --- |
| Impor Excel | Mempercepat migrasi data dari `database_judul.xlsx`. |
| Ekspor Excel atau CSV | Memudahkan pelaporan administrasi. |
| Filter tahun dan pembimbing | Memudahkan rekap per periode dan monitoring beban pembimbing. |
| Audit log | Melacak siapa yang menambah, mengubah, atau menghapus data. |
| Upload dokumen | Mengurangi ketergantungan pada link eksternal. |
| Normalisasi pembimbing | Membuat rekap pembimbing lebih akurat. |
| Pencarian semantik | Meningkatkan akurasi deteksi kemiripan judul. |
| Dashboard statistik | Menampilkan jumlah judul, tren topik, dan sebaran pembimbing. |

## Kriteria Penerimaan

Fitur DELTA-MAT dinyatakan sesuai rancangan jika:

- Pengguna dapat membuka halaman DELTA-MAT.
- Pengguna dapat mencari judul tugas akhir.
- Sistem menampilkan skor kemiripan tertinggi.
- Sistem menampilkan status `UNIK`, `WASPADA`, atau `DUPLIKASI`.
- Sistem menampilkan daftar rekomendasi judul.
- Admin dapat membuka halaman Kelola DELTA-MAT setelah login.
- Admin dapat menambah data judul.
- Admin dapat mengubah data judul.
- Admin dapat menghapus data judul.
- Admin dapat mencari data berdasarkan judul, NIM, mahasiswa, atau pembimbing.
- Data tersimpan pada tabel `thesis_title_databases`.

## Catatan Implementasi

Rancangan ini disusun berdasarkan struktur implementasi yang sudah tersedia di proyek. Beberapa bagian seperti impor Excel langsung, ekspor data, audit log, dan statistik belum menjadi fitur inti saat dokumen ini dibuat, tetapi direkomendasikan sebagai pengembangan lanjutan untuk memperkuat fungsi administrasi tugas akhir.
