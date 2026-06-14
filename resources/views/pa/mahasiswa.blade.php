<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Bimbingan PA Mahasiswa</title>
    <style>
        :root { --blue:#1f91e8; --green:#16855b; --orange:#c56a14; --ink:#252b36; --muted:#68707d; --line:#dfe7ef; --page:#f5f7fc; --panel:#fff; }
        * { box-sizing:border-box; }
        body { margin:0; background:var(--page); color:var(--ink); font-family:Inter, ui-sans-serif, system-ui, -apple-system, "Segoe UI", Arial, sans-serif; }
        .topbar { height:74px; padding:0 min(5vw,64px); display:flex; align-items:center; justify-content:space-between; background:#fff; border-bottom:1px solid var(--line); }
        .brand { display:flex; align-items:center; gap:12px; font-size:22px; font-weight:900; letter-spacing:.06em; }
        .brand-logo { width:44px; height:44px; object-fit:contain; }
        .logout { border:0; background:#edf2f7; color:var(--ink); min-height:38px; padding:0 14px; border-radius:5px; font-weight:800; cursor:pointer; }
        main { padding:28px min(5vw,64px) 56px; }
        .header { display:flex; justify-content:space-between; gap:18px; align-items:flex-start; margin-bottom:20px; }
        h1 { margin:0 0 8px; font-size:34px; line-height:1.1; }
        p { color:var(--muted); line-height:1.6; }
        .grid { display:grid; grid-template-columns:1.1fr .9fr; gap:18px; }
        .cards { display:grid; grid-template-columns:repeat(3, minmax(0,1fr)); gap:12px; margin-bottom:18px; }
        .card, .panel { background:var(--panel); border:1px solid var(--line); border-radius:8px; padding:18px; }
        .card span { color:var(--muted); font-size:13px; font-weight:800; text-transform:uppercase; }
        .card strong { display:block; margin-top:6px; font-size:26px; }
        .panel { margin-bottom:18px; }
        h2 { margin:0 0 14px; font-size:20px; }
        label { display:block; margin:12px 0 6px; color:#394150; font-weight:800; }
        input, textarea { width:100%; border:1px solid var(--line); border-radius:6px; padding:11px 12px; font:inherit; background:#fff; }
        textarea { min-height:120px; resize:vertical; }
        .button { border:0; border-radius:5px; background:var(--blue); color:#fff; min-height:40px; padding:0 14px; font-weight:800; cursor:pointer; }
        table { width:100%; border-collapse:collapse; }
        th, td { padding:11px 10px; border-bottom:1px solid var(--line); text-align:left; vertical-align:top; }
        th { font-size:13px; color:var(--muted); text-transform:uppercase; }
        .status { display:inline-flex; padding:4px 8px; border-radius:999px; background:#eef6ff; color:var(--blue); font-size:12px; font-weight:900; }
        .success { margin-bottom:18px; background:#eaf8f1; color:#146c48; border:1px solid #bfe8d3; border-radius:8px; padding:12px 14px; font-weight:800; }
        .error { color:#b42318; font-size:13px; margin-top:5px; }
        @media (max-width:900px) { .grid, .cards { grid-template-columns:1fr; } .header { display:block; } }
    </style>
</head>
<body>
<header class="topbar">
    <div class="brand"><x-unm-logo class="brand-logo" /><span>SIMTA PA</span></div>
    <form method="post" action="{{ route('logout') }}">
        @csrf
        <button class="logout" type="submit">Logout</button>
    </form>
</header>

<main>
    <div class="header">
        <div>
            <h1>Bimbingan PA Mahasiswa</h1>
            <p>{{ $student->name }} - {{ $student->nim }}. Gunakan halaman ini untuk mengajukan konsultasi PA dan melihat riwayat bimbingan akademik.</p>
        </div>
    </div>

    @if (session('status'))
        <div class="success">{{ session('status') }}</div>
    @endif

    <section class="cards">
        <article class="card"><span>Dosen PA</span><strong>{{ $assignment->lecturer_name }}</strong></article>
        <article class="card"><span>IPK Terakhir</span><strong>{{ $latestRecord?->ipk ?? '-' }}</strong></article>
        <article class="card"><span>SKS Total</span><strong>{{ $latestRecord?->sks_total ?? '-' }}</strong></article>
    </section>

    <div class="grid">
        <section>
            <article class="panel">
                <h2>Pengajuan Konsultasi PA</h2>
                <form method="post" action="{{ route('pa.mahasiswa.consultations.store') }}">
                    @csrf
                    <label for="topic">Topik konsultasi</label>
                    <input id="topic" name="topic" value="{{ old('topic') }}" placeholder="Contoh: Rencana KRS semester depan">
                    @error('topic') <div class="error">{{ $message }}</div> @enderror

                    <label for="requested_at">Waktu yang diusulkan</label>
                    <input id="requested_at" name="requested_at" type="datetime-local" value="{{ old('requested_at') }}">
                    @error('requested_at') <div class="error">{{ $message }}</div> @enderror

                    <label for="student_note">Catatan mahasiswa</label>
                    <textarea id="student_note" name="student_note" placeholder="Tuliskan masalah akademik atau kebutuhan konsultasi">{{ old('student_note') }}</textarea>
                    @error('student_note') <div class="error">{{ $message }}</div> @enderror

                    <p><button class="button" type="submit">Kirim Pengajuan</button></p>
                </form>
            </article>

            <article class="panel">
                <h2>Riwayat Bimbingan PA</h2>
                <table>
                    <thead><tr><th>Topik</th><th>Status</th><th>Catatan Dosen</th></tr></thead>
                    <tbody>
                    @forelse ($consultations as $item)
                        <tr>
                            <td><strong>{{ $item->topic }}</strong><br><small>{{ $item->created_at }}</small></td>
                            <td><span class="status">{{ $item->status }}</span></td>
                            <td>{{ $item->lecturer_note ?? 'Menunggu catatan dosen.' }} @if($item->recommendation)<br><strong>Rekomendasi:</strong> {{ $item->recommendation }} @endif</td>
                        </tr>
                    @empty
                        <tr><td colspan="3">Belum ada riwayat konsultasi.</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </article>
        </section>

        <aside>
            <article class="panel">
                <h2>Data Dosen PA</h2>
                <p><strong>{{ $assignment->lecturer_name }}</strong><br>{{ $assignment->lecturer_email ?? '-' }}<br>{{ $assignment->lecturer_phone ?? '-' }}</p>
                <p>Tahun akademik: <strong>{{ $assignment->academic_year }}</strong></p>
            </article>

            <article class="panel">
                <h2>Monitoring IPK dan SKS</h2>
                <table>
                    <thead><tr><th>Semester</th><th>IPK</th><th>SKS</th></tr></thead>
                    <tbody>
                    @forelse ($records as $record)
                        <tr><td>{{ $record->semester }}</td><td>{{ $record->ipk }}</td><td>{{ $record->sks_total }}</td></tr>
                    @empty
                        <tr><td colspan="3">Belum ada data akademik.</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </article>
        </aside>
    </div>
</main>
</body>
</html>
