<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin Bimbingan PA | Sistem Informasi Manajemen Tugas Akhir</title>
    <style>
        :root { --blue:#1f91e8; --green:#16855b; --orange:#c56a14; --danger:#dc3545; --ink:#252b36; --muted:#68707d; --line:#dfe7ef; --page:#f5f7fc; --panel:#fff; }
        * { box-sizing:border-box; }
        body { margin:0; background:var(--page); color:var(--ink); font-family:Inter, ui-sans-serif, system-ui, -apple-system, "Segoe UI", Arial, sans-serif; }
        .topbar { height:78px; display:flex; align-items:center; justify-content:space-between; padding:0 min(5vw,64px); background:#fff; border-bottom:1px solid var(--line); }
        .brand { display:flex; align-items:center; gap:14px; font-size:24px; font-weight:900; letter-spacing:.06em; }
        .brand-logo { width:48px; height:48px; object-fit:contain; }
        .top-actions { display:flex; align-items:center; gap:12px; flex-wrap:wrap; }
        .page { width:min(1240px, calc(100vw - 32px)); margin:30px auto 64px; }
        h1 { margin:0 0 8px; font-size:36px; }
        h2 { margin:0 0 16px; font-size:22px; }
        p { color:var(--muted); line-height:1.6; }
        a { color:var(--blue); font-weight:800; text-decoration:none; }
        button, .button { border:0; border-radius:5px; padding:11px 14px; background:var(--blue); color:#fff; font:inherit; font-weight:800; cursor:pointer; text-decoration:none; display:inline-flex; align-items:center; justify-content:center; }
        button.secondary, .button.secondary { background:#edf2f7; color:var(--ink); }
        button.danger, .button.danger { background:var(--danger); color:#fff; }
        button.small, .button.small { min-height:31px; padding:7px 10px; font-size:13px; }
        .grid { display:grid; grid-template-columns:380px 1fr; gap:18px; align-items:start; margin-top:22px; }
        .panel { background:#fff; border:1px solid var(--line); border-radius:8px; padding:22px; box-shadow:0 8px 18px rgba(37,43,54,.05); margin-bottom:18px; }
        label { display:block; margin:0 0 7px; font-weight:800; }
        input, select, textarea { width:100%; border:1px solid var(--line); border-radius:5px; padding:10px 11px; font:inherit; color:var(--ink); background:#fff; }
        textarea { min-height:90px; resize:vertical; }
        .field { margin-bottom:13px; }
        .two { display:grid; grid-template-columns:1fr 1fr; gap:10px; }
        .flash { background:#e6f4ea; color:#146c43; border:1px solid #badbcc; padding:13px 14px; margin:18px 0; border-radius:5px; font-weight:800; }
        .errors { background:#fdecec; color:#842029; border:1px solid #f5c2c7; padding:13px 14px; margin:18px 0; border-radius:5px; }
        .table { width:100%; border-collapse:collapse; }
        .table th, .table td { border-bottom:1px solid var(--line); padding:12px 9px; text-align:left; vertical-align:top; }
        .table th { font-size:12px; text-transform:uppercase; color:var(--muted); }
        .row-actions { display:flex; gap:7px; flex-wrap:wrap; }
        .row-actions form { margin:0; }
        .badge { display:inline-flex; padding:4px 8px; border-radius:999px; background:#eef6ff; color:var(--blue); font-size:12px; font-weight:900; }
        .muted { color:var(--muted); }
        @media (max-width:1020px) { .grid, .two { grid-template-columns:1fr; } .panel { overflow-x:auto; } .topbar { height:auto; padding-top:16px; padding-bottom:16px; align-items:flex-start; } }
    </style>
</head>
<body>
<header class="topbar">
    <div class="brand"><x-unm-logo class="brand-logo" /><span>ADMIN PA</span></div>
    <div class="top-actions">
        <a href="{{ route('admin.dashboard') }}">Dashboard Admin</a>
        <a href="{{ route('landing') }}">Beranda</a>
        <form method="post" action="{{ route('logout') }}">
            @csrf
            <button class="secondary" type="submit">Logout</button>
        </form>
    </div>
</header>

<main class="page">
    <h1>CRUD Bimbingan PA</h1>
    <p>Admin dapat mengelola penetapan dosen PA, data IPK/SKS mahasiswa, dan riwayat konsultasi PA.</p>

    @if (session('status'))
        <div class="flash">{{ session('status') }}</div>
    @endif
    @if ($errors->any())
        <div class="errors">{{ $errors->first() }}</div>
    @endif

    <div class="grid">
        <aside>
            <section class="panel">
                <h2>{{ $editingAssignment ? 'Edit Dosen PA' : 'Tambah Dosen PA' }}</h2>
                <form method="post" action="{{ $editingAssignment ? route('admin.bimbingan-pa.assignments.update', $editingAssignment->id) : route('admin.bimbingan-pa.assignments.store') }}">
                    @csrf
                    @if ($editingAssignment) @method('PUT') @endif
                    <div class="field">
                        <label>Dosen PA</label>
                        <select name="lecturer_id" required>
                            @foreach ($lecturers as $lecturer)
                                <option value="{{ $lecturer->id }}" @selected((int) old('lecturer_id', $editingAssignment->lecturer_id ?? '') === $lecturer->id)>{{ $lecturer->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="field">
                        <label>Mahasiswa</label>
                        <select name="student_id" required>
                            @foreach ($students as $student)
                                <option value="{{ $student->id }}" @selected((int) old('student_id', $editingAssignment->student_id ?? '') === $student->id)>{{ $student->name }} - {{ $student->nim }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="two">
                        <div class="field">
                            <label>Tahun Akademik</label>
                            <input name="academic_year" value="{{ old('academic_year', $editingAssignment->academic_year ?? '2025/2026') }}" required>
                        </div>
                        <div class="field">
                            <label>Status</label>
                            <select name="status">
                                @foreach (['aktif', 'nonaktif'] as $status)
                                    <option value="{{ $status }}" @selected(old('status', $editingAssignment->status ?? 'aktif') === $status)>{{ ucfirst($status) }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <button type="submit">{{ $editingAssignment ? 'Update Dosen PA' : 'Simpan Dosen PA' }}</button>
                    @if ($editingAssignment)<a class="button secondary" href="{{ route('admin.bimbingan-pa') }}">Batal</a>@endif
                </form>
            </section>

            <section class="panel">
                <h2>{{ $editingRecord ? 'Edit IPK/SKS' : 'Tambah IPK/SKS' }}</h2>
                <form method="post" action="{{ $editingRecord ? route('admin.bimbingan-pa.records.update', $editingRecord->id) : route('admin.bimbingan-pa.records.store') }}">
                    @csrf
                    @if ($editingRecord) @method('PUT') @endif
                    <div class="field">
                        <label>Mahasiswa</label>
                        <select name="student_id" required>
                            @foreach ($students as $student)
                                <option value="{{ $student->id }}" @selected((int) old('student_id', $editingRecord->student_id ?? '') === $student->id)>{{ $student->name }} - {{ $student->nim }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="two">
                        <div class="field"><label>Semester</label><input name="semester" type="number" min="1" max="14" value="{{ old('semester', $editingRecord->semester ?? '') }}" required></div>
                        <div class="field"><label>IPK</label><input name="ipk" type="number" min="0" max="4" step="0.01" value="{{ old('ipk', $editingRecord->ipk ?? '') }}" required></div>
                    </div>
                    <div class="two">
                        <div class="field"><label>SKS Semester</label><input name="sks_semester" type="number" min="0" max="30" value="{{ old('sks_semester', $editingRecord->sks_semester ?? '') }}" required></div>
                        <div class="field"><label>SKS Total</label><input name="sks_total" type="number" min="0" max="180" value="{{ old('sks_total', $editingRecord->sks_total ?? '') }}" required></div>
                    </div>
                    <div class="field"><label>Status Akademik</label><input name="academic_status" value="{{ old('academic_status', $editingRecord->academic_status ?? 'Aktif') }}" required></div>
                    <button type="submit">{{ $editingRecord ? 'Update IPK/SKS' : 'Simpan IPK/SKS' }}</button>
                    @if ($editingRecord)<a class="button secondary" href="{{ route('admin.bimbingan-pa') }}">Batal</a>@endif
                </form>
            </section>
        </aside>

        <section>
            <article class="panel">
                <h2>Data Dosen PA Mahasiswa</h2>
                <table class="table">
                    <thead><tr><th>Mahasiswa</th><th>Dosen PA</th><th>Tahun</th><th>Status</th><th>Aksi</th></tr></thead>
                    <tbody>
                    @forelse ($assignments as $assignment)
                        <tr>
                            <td><strong>{{ $assignment->student_name }}</strong><br><span class="muted">{{ $assignment->nim }}</span></td>
                            <td>{{ $assignment->lecturer_name }}</td>
                            <td>{{ $assignment->academic_year }}</td>
                            <td><span class="badge">{{ $assignment->status }}</span></td>
                            <td>
                                <div class="row-actions">
                                    <a class="button small secondary" href="{{ route('admin.bimbingan-pa', ['edit_assignment' => $assignment->id]) }}">Edit</a>
                                    <form method="post" action="{{ route('admin.bimbingan-pa.assignments.destroy', $assignment->id) }}" onsubmit="return confirm('Hapus penetapan dosen PA ini?')">
                                        @csrf @method('DELETE')
                                        <button class="small danger" type="submit">Hapus</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5">Belum ada data dosen PA.</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </article>

            <article class="panel">
                <h2>Monitoring IPK dan SKS</h2>
                <table class="table">
                    <thead><tr><th>Mahasiswa</th><th>Semester</th><th>IPK</th><th>SKS</th><th>Status</th><th>Aksi</th></tr></thead>
                    <tbody>
                    @forelse ($records as $record)
                        <tr>
                            <td><strong>{{ $record->student_name }}</strong><br><span class="muted">{{ $record->nim }}</span></td>
                            <td>{{ $record->semester }}</td>
                            <td>{{ $record->ipk }}</td>
                            <td>{{ $record->sks_semester }} / {{ $record->sks_total }}</td>
                            <td><span class="badge">{{ $record->academic_status }}</span></td>
                            <td>
                                <div class="row-actions">
                                    <a class="button small secondary" href="{{ route('admin.bimbingan-pa', ['edit_record' => $record->id]) }}">Edit</a>
                                    <form method="post" action="{{ route('admin.bimbingan-pa.records.destroy', $record->id) }}" onsubmit="return confirm('Hapus data IPK/SKS ini?')">
                                        @csrf @method('DELETE')
                                        <button class="small danger" type="submit">Hapus</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6">Belum ada data IPK/SKS.</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </article>

            <article class="panel">
                <h2>{{ $editingConsultation ? 'Edit Konsultasi PA' : 'Tambah Konsultasi PA' }}</h2>
                <form method="post" action="{{ $editingConsultation ? route('admin.bimbingan-pa.consultations.update', $editingConsultation->id) : route('admin.bimbingan-pa.consultations.store') }}">
                    @csrf
                    @if ($editingConsultation) @method('PUT') @endif
                    <div class="two">
                        <div class="field">
                            <label>Mahasiswa dan Dosen PA</label>
                            <select name="pa_assignment_id" required>
                                @foreach ($assignments as $assignment)
                                    <option value="{{ $assignment->id }}" @selected((int) old('pa_assignment_id', $editingConsultation->pa_assignment_id ?? '') === $assignment->id)>{{ $assignment->student_name }} - {{ $assignment->lecturer_name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="field">
                            <label>Status</label>
                            <select name="status">
                                @foreach (['diajukan', 'dijadwalkan', 'selesai', 'dibatalkan'] as $status)
                                    <option value="{{ $status }}" @selected(old('status', $editingConsultation->status ?? 'diajukan') === $status)>{{ ucfirst($status) }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="field"><label>Topik</label><input name="topic" value="{{ old('topic', $editingConsultation->topic ?? '') }}" required></div>
                    <div class="two">
                        <div class="field"><label>Waktu Diajukan</label><input name="requested_at" type="datetime-local" value="{{ old('requested_at', $editingConsultation?->requested_at ? date('Y-m-d\TH:i', strtotime($editingConsultation->requested_at)) : '') }}"></div>
                        <div class="field"><label>Jadwal Konsultasi</label><input name="scheduled_at" type="datetime-local" value="{{ old('scheduled_at', $editingConsultation?->scheduled_at ? date('Y-m-d\TH:i', strtotime($editingConsultation->scheduled_at)) : '') }}"></div>
                    </div>
                    <div class="field"><label>Catatan Mahasiswa</label><textarea name="student_note" required>{{ old('student_note', $editingConsultation->student_note ?? '') }}</textarea></div>
                    <div class="field"><label>Catatan Dosen</label><textarea name="lecturer_note">{{ old('lecturer_note', $editingConsultation->lecturer_note ?? '') }}</textarea></div>
                    <div class="field"><label>Rekomendasi</label><textarea name="recommendation">{{ old('recommendation', $editingConsultation->recommendation ?? '') }}</textarea></div>
                    <button type="submit">{{ $editingConsultation ? 'Update Konsultasi' : 'Simpan Konsultasi' }}</button>
                    @if ($editingConsultation)<a class="button secondary" href="{{ route('admin.bimbingan-pa') }}">Batal</a>@endif
                </form>
            </article>

            <article class="panel">
                <h2>Riwayat Konsultasi PA</h2>
                <table class="table">
                    <thead><tr><th>Mahasiswa</th><th>Dosen PA</th><th>Topik</th><th>Status</th><th>Aksi</th></tr></thead>
                    <tbody>
                    @forelse ($consultations as $consultation)
                        <tr>
                            <td><strong>{{ $consultation->student_name }}</strong><br><span class="muted">{{ $consultation->nim }}</span></td>
                            <td>{{ $consultation->lecturer_name }}</td>
                            <td><strong>{{ $consultation->topic }}</strong><br><span class="muted">{{ $consultation->scheduled_at ?? $consultation->requested_at ?? '-' }}</span></td>
                            <td><span class="badge">{{ $consultation->status }}</span></td>
                            <td>
                                <div class="row-actions">
                                    <a class="button small secondary" href="{{ route('admin.bimbingan-pa', ['edit_consultation' => $consultation->id]) }}">Edit</a>
                                    <form method="post" action="{{ route('admin.bimbingan-pa.consultations.destroy', $consultation->id) }}" onsubmit="return confirm('Hapus konsultasi PA ini?')">
                                        @csrf @method('DELETE')
                                        <button class="small danger" type="submit">Hapus</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5">Belum ada konsultasi PA.</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </article>
        </section>
    </div>
</main>
</body>
</html>
