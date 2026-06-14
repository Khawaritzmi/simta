<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Update Database Kolektif | SIMTA</title>
    @vite(['resources/css/admin-seminars.css', 'resources/js/app.js'])
</head>
<body>
<header class="topbar">
    <div class="brand"><x-unm-logo class="brand-logo" /><span>ADMIN SIMTA</span></div>
    <div class="top-actions">
        <a href="{{ route('admin.dashboard') }}">Dashboard Admin</a>
        <a href="{{ route('admin.bimbingan-pa') }}">CRUD PA</a>
        <a href="{{ route('admin.seminars') }}">Jadwal Seminar</a>
        <a href="{{ route('admin.database-ta') }}">DELTA-MAT</a>
        <a href="{{ route('admin.settings') }}">Settings</a>
    </div>
</header>

<main class="page">
    <h1>Update Database Kolektif</h1>
    <p class="lead">Admin dapat menambah atau memperbarui data DELTA-MAT, mahasiswa, dan dosen SIMTA dari CSV. Gunakan file dengan baris pertama sebagai header kolom.</p>

    @if (session('status'))
        <div class="flash">{{ session('status') }}</div>
    @endif
    @if ($errors->any())
        <div class="errors">{{ $errors->first() }}</div>
    @endif
    @if (! $historyTableReady)
        <div class="errors">Tabel <code>update_histories</code> belum tersedia. Jalankan <code>php artisan migrate</code> agar fitur Update Database Kolektif dapat dipakai dan history perubahan tersimpan.</div>
    @endif

    <div class="grid">
        <section class="panel">
            <h2>Upload Data Massal</h2>
            <form method="post" action="{{ route('admin.kolektif-update.store') }}" enctype="multipart/form-data">
                @csrf

                <div class="field">
                    <label for="target">Lingkup data</label>
                    <select id="target" name="target" required>
                        @foreach ($targets as $key => $target)
                            <option value="{{ $key }}" @selected(old('target') === $key)>{{ $target['label'] }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="field">
                    <label>Mode eksekusi</label>
                    <div class="choice-stack">
                        <label class="choice-card">
                            <input type="radio" name="mode" value="update_existing" @checked(old('mode', 'update_existing') === 'update_existing')>
                            <span>
                                <strong>Update data yang lama</strong>
                                <small>Data lama diperbarui jika key unik ditemukan. Data baru tetap ditambahkan.</small>
                            </span>
                        </label>
                        <label class="choice-card">
                            <input type="radio" name="mode" value="insert_only" @checked(old('mode') === 'insert_only')>
                            <span>
                                <strong>Jangan ganggu data yang lama</strong>
                                <small>Hanya menambah data yang belum ada. Data lama dilewati.</small>
                            </span>
                        </label>
                    </div>
                </div>

                <div class="field">
                    <label for="data_file">File CSV</label>
                    <input id="data_file" type="file" name="data_file" accept=".csv,.txt,.xlsx,text/csv">
                    <p class="hint">Mendukung CSV dan XLSX. File .xls lama perlu disimpan ulang sebagai .xlsx atau CSV.</p>
                </div>

                <div class="field">
                    <label for="data_text">Atau tempel data CSV</label>
                    <textarea id="data_text" name="data_text" rows="8" placeholder="nim,name,program,email&#10;H011201001,Aulia Rahmadani,Matematika,aulia@example.test">{{ old('data_text') }}</textarea>
                </div>

                <div class="form-actions">
                    <button type="submit" @disabled(! $historyTableReady)>Jalankan Update</button>
                </div>
            </form>
        </section>

        <aside class="panel">
            <h2>Format Kolom</h2>
            <div class="help-list">
                <h3>DELTA-MAT</h3>
                <p><code>nim, student_name, title, submission_date, phone, email, supervisor_1, supervisor_1_nip, supervisor_2, supervisor_2_nip, document_url</code></p>
                <h3>Mahasiswa</h3>
                <p><code>nim, name, program, email</code></p>
                <h3>Dosen</h3>
                <p><code>nip, nidn, name, employment_status, expertise, email, phone, address</code></p>
            </div>
        </aside>
    </div>

    <section class="panel history-panel">
        <h2>Riwayat Update</h2>
        <table class="table">
            <thead>
            <tr>
                <th>Waktu</th>
                <th>User</th>
                <th>Target</th>
                <th>Mode</th>
                <th>Ringkasan perubahan</th>
            </tr>
            </thead>
            <tbody>
            @forelse ($histories as $history)
                @php($changes = $history->changes ?? [])
                <tr>
                    <td>{{ optional($history->created_at)->format('d M Y H:i') }}</td>
                    <td>{{ $history->user?->name ?? 'User dihapus' }}</td>
                    <td><code>{{ $history->target_table }}</code></td>
                    <td><span class="badge {{ $history->mode === 'insert' ? 'success' : '' }}">{{ $history->mode }}</span></td>
                    <td>
                        <strong>Baris {{ $changes['row'] ?? '-' }}</strong>
                        <span class="muted">({{ ($changes['run_mode'] ?? '') === 'insert_only' ? 'Jangan ganggu data lama' : 'Update data lama' }})</span><br>
                        <span class="muted">Key: {{ json_encode($changes['key'] ?? [], JSON_UNESCAPED_UNICODE) }}</span><br>
                        @if (($history->mode ?? '') === 'update')
                            @foreach (($changes['fields'] ?? []) as $field => $change)
                                <div><code>{{ $field }}</code>: "{{ $change['from'] ?? '' }}" menjadi "{{ $change['to'] ?? '' }}"</div>
                            @endforeach
                        @else
                            <span>Data baru ditambahkan.</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr><td colspan="5">Belum ada riwayat update kolektif.</td></tr>
            @endforelse
            </tbody>
        </table>

        <div class="pagination">
            <span>Menampilkan {{ $histories->count() }} dari {{ $histories->total() }} riwayat</span>
            <div class="pager-buttons">
                @if ($histories->onFirstPage())
                    <span class="pager-disabled">Sebelumnya</span>
                @else
                    <a class="pager-link" href="{{ $histories->previousPageUrl() }}">Sebelumnya</a>
                @endif
                @if ($histories->hasMorePages())
                    <a class="pager-link" href="{{ $histories->nextPageUrl() }}">Berikutnya</a>
                @else
                    <span class="pager-disabled">Berikutnya</span>
                @endif
            </div>
        </div>
    </section>
</main>
</body>
</html>
