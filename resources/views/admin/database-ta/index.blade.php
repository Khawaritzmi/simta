<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin DELTA-MAT | Sistem Informasi Manajemen Tugas Akhir</title>
    @vite(['resources/css/admin-database-ta.css', 'resources/js/app.js'])
</head>
<body>
<header class="topbar">
    <div class="brand"><x-unm-logo class="brand-logo" /><span>ADMIN DELTA-MAT</span></div>
    <div class="top-actions">
        <a href="{{ route('database-ta') }}">Lihat Public</a>
        <a href="{{ route('admin.dashboard') }}">Dashboard Admin</a>
        <a href="{{ route('admin.seminars') }}">Jadwal Seminar</a>
        <a href="{{ route('admin.kolektif-update') }}">Update Kolektif</a>
        <a href="{{ route('admin.settings') }}">Settings</a>
        <a href="{{ route('password.edit') }}">Ubah Password</a>
        <form method="post" action="{{ route('logout') }}">
            @csrf
            <button class="secondary" type="submit">Logout</button>
        </form>
    </div>
</header>

<main class="page">
    <h1>Kelola DELTA-MAT</h1>

    @if (session('status'))
        <div class="flash">{{ session('status') }}</div>
    @endif
    @if ($errors->any())
        <div class="errors">{{ $errors->first() }}</div>
    @endif

    <div class="grid">
        <section class="panel">
            <h2>{{ $editing ? 'Edit Data Judul' : 'Tambah Data Judul' }}</h2>
            <form method="post" action="{{ $editing ? route('admin.database-ta.update', $editing->id) : route('admin.database-ta.store') }}">
                @csrf
                @if ($editing)
                    @method('PUT')
                @endif
                <div class="two">
                    <div class="field">
                        <label for="submission_date">Tanggal Pengajuan</label>
                        <input id="submission_date" name="submission_date" value="{{ old('submission_date', $editing->submission_date ?? '') }}" placeholder="22 April 2026">
                    </div>
                    <div class="field">
                        <label for="nim">NIM</label>
                        <input id="nim" name="nim" value="{{ old('nim', $editing->nim ?? '') }}" required>
                    </div>
                </div>
                <div class="field">
                    <label for="student_name">Nama Mahasiswa</label>
                    <input id="student_name" name="student_name" value="{{ old('student_name', $editing->student_name ?? '') }}" required>
                </div>
                <div class="field">
                    <label for="title">Judul Tugas Akhir</label>
                    <textarea id="title" name="title" required>{{ old('title', $editing->title ?? '') }}</textarea>
                </div>
                <div class="two">
                    <div class="field">
                        <label for="email">Email</label>
                        <input id="email" name="email" type="email" value="{{ old('email', $editing->email ?? '') }}">
                    </div>
                    <div class="field">
                        <label for="phone">No. WA</label>
                        <input id="phone" name="phone" value="{{ old('phone', $editing->phone ?? '') }}">
                    </div>
                </div>
                <div class="field">
                    <label for="supervisor_1">Pembimbing 1</label>
                    <input id="supervisor_1" name="supervisor_1" value="{{ old('supervisor_1', $editing->supervisor_1 ?? '') }}">
                </div>
                <div class="field">
                    <label for="supervisor_1_nip">NIP Pembimbing 1</label>
                    <input id="supervisor_1_nip" name="supervisor_1_nip" value="{{ old('supervisor_1_nip', $editing->supervisor_1_nip ?? '') }}">
                </div>
                <div class="field">
                    <label for="supervisor_2">Pembimbing 2</label>
                    <input id="supervisor_2" name="supervisor_2" value="{{ old('supervisor_2', $editing->supervisor_2 ?? '') }}">
                </div>
                <div class="field">
                    <label for="supervisor_2_nip">NIP Pembimbing 2</label>
                    <input id="supervisor_2_nip" name="supervisor_2_nip" value="{{ old('supervisor_2_nip', $editing->supervisor_2_nip ?? '') }}">
                </div>
                <div class="field">
                    <label for="document_url">Link Dokumen</label>
                    <input id="document_url" name="document_url" value="{{ old('document_url', $editing->document_url ?? '') }}">
                </div>
                <button type="submit">{{ $editing ? 'Update Data' : 'Simpan Data' }}</button>
                @if ($editing)
                    <a class="button secondary" href="{{ route('admin.database-ta') }}">Batal Edit</a>
                @endif
            </form>
        </section>

        <section class="panel">
            <h2>Data dari database_judul.xlsx</h2>
            <form class="search" method="get" action="{{ route('admin.database-ta') }}">
                <input name="q" value="{{ $query }}" placeholder="Cari judul, NIM, mahasiswa, pembimbing">
                <button type="submit">Cari</button>
            </form>
            <table class="table">
                <thead>
                <tr>
                    <th>NIM</th>
                    <th>Mahasiswa</th>
                    <th>Judul</th>
                    <th>Pembimbing</th>
                    <th>Aksi</th>
                </tr>
                </thead>
                <tbody>
                @forelse ($records as $record)
                    <tr>
                        <td>{{ $record->nim }}</td>
                        <td>{{ $record->student_name }}<br><span class="muted">{{ $record->submission_date }}</span></td>
                        <td class="title-cell">{{ $record->title }}</td>
                        <td>{{ $record->supervisor_1 ?: '-' }}<br><span class="muted">{{ $record->supervisor_2 }}</span></td>
                        <td>
                            <div class="row-actions">
                                <a class="button small secondary" href="{{ route('admin.database-ta.edit', $record->id) }}">Edit</a>
                                <form method="post" action="{{ route('admin.database-ta.destroy', $record->id) }}" onsubmit="return confirm('Hapus data judul ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="small danger" type="submit">Hapus</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5">Data tidak ditemukan.</td></tr>
                @endforelse
                </tbody>
            </table>
            <div class="pagination">
                <span>Menampilkan {{ $records->firstItem() ?? 0 }}-{{ $records->lastItem() ?? 0 }} dari {{ $records->total() }} data</span>
                <div class="pager-buttons">
                    @if ($records->onFirstPage())
                        <span class="pager-disabled">Sebelumnya</span>
                    @else
                        <a class="pager-link" href="{{ $records->previousPageUrl() }}">Sebelumnya</a>
                    @endif

                    <span>Halaman {{ $records->currentPage() }} / {{ $records->lastPage() }}</span>

                    @if ($records->hasMorePages())
                        <a class="pager-link" href="{{ $records->nextPageUrl() }}">Berikutnya</a>
                    @else
                        <span class="pager-disabled">Berikutnya</span>
                    @endif
                </div>
            </div>
        </section>
    </div>
</main>
</body>
</html>
