<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin DELTA-MAT | Sistem Informasi Manajemen Tugas Akhir</title>
    <style>
        :root { --blue:#1f91e8; --orange:#c56a14; --ink:#252b36; --muted:#68707d; --line:#dfe7ef; --page:#f5f7fc; --panel:#fff; --success:#198754; --danger:#dc3545; }
        * { box-sizing: border-box; }
        body { margin: 0; background: var(--page); color: var(--ink); font-family: Inter, ui-sans-serif, system-ui, -apple-system, "Segoe UI", Arial, sans-serif; }
        .topbar { height: 78px; display: flex; align-items: center; justify-content: space-between; padding: 0 min(6vw, 72px); background: white; border-bottom: 1px solid var(--line); }
        .brand { display: flex; align-items: center; gap: 14px; font-weight: 900; letter-spacing: .08em; font-size: 24px; }
        .brand-logo { width: 48px; height: 48px; object-fit: contain; }
        .top-actions { display: flex; align-items: center; gap: 12px; }
        a { color: var(--blue); font-weight: 800; text-decoration: none; }
        button, .button { border: 0; border-radius: 5px; padding: 12px 16px; background: var(--blue); color: white; font: inherit; font-weight: 800; cursor: pointer; text-decoration: none; display: inline-flex; align-items: center; justify-content: center; }
        button.secondary, .button.secondary { background: #edf2f7; color: var(--ink); }
        button.danger, .button.danger { background: var(--danger); color: white; }
        button.small, .button.small { min-height: 32px; padding: 7px 10px; font-size: 13px; }
        .page { width: min(1180px, calc(100vw - 32px)); margin: 30px auto 64px; }
        h1 { margin: 0 0 22px; font-size: 36px; }
        h2 { margin: 0 0 18px; font-size: 22px; }
        .grid { display: grid; grid-template-columns: 390px 1fr; gap: 18px; align-items: start; }
        .panel { background: white; border: 1px solid var(--line); border-radius: 8px; padding: 24px; box-shadow: 0 8px 18px rgba(37, 43, 54, .05); }
        label { display: block; font-weight: 800; margin-bottom: 8px; }
        input, textarea { width: 100%; border: 1px solid var(--line); border-radius: 5px; padding: 12px 13px; font: inherit; color: var(--ink); }
        textarea { min-height: 110px; resize: vertical; }
        .field { margin-bottom: 14px; }
        .two { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; }
        .flash { background: #e6f4ea; color: #146c43; border: 1px solid #badbcc; padding: 13px 14px; margin-bottom: 18px; border-radius: 5px; }
        .errors { background: #fdecec; color: #842029; border: 1px solid #f5c2c7; padding: 13px 14px; margin-bottom: 18px; border-radius: 5px; }
        .search { display: grid; grid-template-columns: 1fr auto; gap: 10px; margin-bottom: 16px; }
        .table { width: 100%; border-collapse: collapse; }
        .table th, .table td { border-bottom: 1px solid var(--line); padding: 13px 10px; text-align: left; vertical-align: top; }
        .table th { font-size: 13px; text-transform: uppercase; color: var(--muted); }
        .title-cell { max-width: 420px; font-weight: 800; }
        .muted { color: var(--muted); }
        .row-actions { display: flex; align-items: center; gap: 8px; flex-wrap: wrap; }
        .row-actions form { margin: 0; }
        .pagination { margin-top: 18px; display: flex; align-items: center; justify-content: space-between; gap: 12px; color: var(--muted); font-size: 13px; }
        .pager-buttons { display: flex; align-items: center; gap: 8px; }
        .pager-link, .pager-disabled { display: inline-flex; align-items: center; justify-content: center; min-height: 32px; padding: 7px 12px; border-radius: 5px; font-size: 13px; font-weight: 800; }
        .pager-link { background: var(--blue); color: white; }
        .pager-disabled { background: #edf2f7; color: #9aa3af; }
        @media (max-width: 980px) { .grid, .two { grid-template-columns: 1fr; } .panel { overflow-x: auto; } }
    </style>
</head>
<body>
<header class="topbar">
    <div class="brand"><x-unm-logo class="brand-logo" /><span>ADMIN DELTA-MAT</span></div>
    <div class="top-actions">
        <a href="{{ route('database-ta') }}">Lihat Public</a>
        <a href="{{ route('admin.dashboard') }}">Dashboard Admin</a>
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
