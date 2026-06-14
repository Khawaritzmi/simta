<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Bimbingan PA Dosen</title>
    <style>
        :root { --blue:#1f91e8; --green:#16855b; --orange:#c56a14; --ink:#252b36; --muted:#68707d; --line:#dfe7ef; --page:#f5f7fc; --panel:#fff; }
        * { box-sizing:border-box; }
        body { margin:0; background:var(--page); color:var(--ink); font-family:Inter, ui-sans-serif, system-ui, -apple-system, "Segoe UI", Arial, sans-serif; }
        .topbar { height:74px; padding:0 min(5vw,64px); display:flex; align-items:center; justify-content:space-between; background:#fff; border-bottom:1px solid var(--line); }
        .brand { display:flex; align-items:center; gap:12px; font-size:22px; font-weight:900; letter-spacing:.06em; }
        .brand-logo { width:44px; height:44px; object-fit:contain; }
        .logout { border:0; background:#edf2f7; color:var(--ink); min-height:38px; padding:0 14px; border-radius:5px; font-weight:800; cursor:pointer; }
        main { padding:28px min(5vw,64px) 56px; }
        h1 { margin:0 0 8px; font-size:34px; }
        p { color:var(--muted); line-height:1.6; }
        .cards { display:grid; grid-template-columns:repeat(4, minmax(0,1fr)); gap:12px; margin:20px 0; }
        .card, .panel { background:#fff; border:1px solid var(--line); border-radius:8px; padding:18px; }
        .card span { color:var(--muted); font-size:13px; font-weight:800; text-transform:uppercase; }
        .card strong { display:block; margin-top:6px; font-size:28px; }
        .grid { display:grid; grid-template-columns:.95fr 1.05fr; gap:18px; align-items:start; }
        h2 { margin:0 0 14px; font-size:20px; }
        table { width:100%; border-collapse:collapse; }
        th, td { padding:11px 10px; border-bottom:1px solid var(--line); text-align:left; vertical-align:top; }
        th { font-size:13px; color:var(--muted); text-transform:uppercase; }
        .status { display:inline-flex; padding:4px 8px; border-radius:999px; background:#eef6ff; color:var(--blue); font-size:12px; font-weight:900; }
        .consultation { border-top:1px solid var(--line); padding:16px 0; }
        .consultation:first-of-type { border-top:0; padding-top:0; }
        label { display:block; margin:10px 0 6px; color:#394150; font-weight:800; }
        select, input, textarea { width:100%; border:1px solid var(--line); border-radius:6px; padding:10px 11px; font:inherit; background:#fff; }
        textarea { min-height:82px; resize:vertical; }
        .button { border:0; border-radius:5px; background:var(--blue); color:#fff; min-height:38px; padding:0 13px; font-weight:800; cursor:pointer; }
        .success { margin-bottom:18px; background:#eaf8f1; color:#146c48; border:1px solid #bfe8d3; border-radius:8px; padding:12px 14px; font-weight:800; }
        @media (max-width:980px) { .cards, .grid { grid-template-columns:1fr; } }
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
    <h1>Bimbingan PA Dosen</h1>
    <p>{{ $lecturer->name }}. Kelola daftar mahasiswa bimbingan PA, catatan konsultasi, monitoring IPK/SKS, dan laporan konsultasi akademik.</p>

    @if (session('status'))
        <div class="success">{{ session('status') }}</div>
    @endif

    <section class="cards">
        <article class="card"><span>Mahasiswa PA</span><strong>{{ $report['students'] }}</strong></article>
        <article class="card"><span>Total Konsultasi</span><strong>{{ $report['consultations'] }}</strong></article>
        <article class="card"><span>Perlu Diproses</span><strong>{{ $report['pending'] }}</strong></article>
        <article class="card"><span>Selesai</span><strong>{{ $report['done'] }}</strong></article>
    </section>

    <div class="grid">
        <section class="panel">
            <h2>Daftar Mahasiswa Bimbingan</h2>
            <table>
                <thead><tr><th>Mahasiswa</th><th>Program</th><th>IPK/SKS</th><th>Status</th></tr></thead>
                <tbody>
                @forelse ($students as $student)
                    <tr>
                        <td><strong>{{ $student->name }}</strong><br><small>{{ $student->nim }} - {{ $student->email ?? '-' }}</small></td>
                        <td>{{ $student->program }}</td>
                        <td>{{ $student->ipk ?? '-' }} / {{ $student->sks_total ?? '-' }} SKS</td>
                        <td><span class="status">{{ $student->academic_status ?? 'Belum ada data' }}</span></td>
                    </tr>
                @empty
                    <tr><td colspan="4">Belum ada mahasiswa PA.</td></tr>
                @endforelse
                </tbody>
            </table>
        </section>

        <section class="panel">
            <h2>Pengajuan dan Catatan Hasil Bimbingan</h2>
            @forelse ($consultations as $item)
                <article class="consultation">
                    <p><strong>{{ $item->student_name }}</strong> - {{ $item->nim }}<br><strong>{{ $item->topic }}</strong><br>{{ $item->student_note }}</p>
                    <p><span class="status">{{ $item->status }}</span> @if($item->scheduled_at) Jadwal: {{ $item->scheduled_at }} @endif</p>
                    <form method="post" action="{{ route('pa.dosen.consultations.update', $item->id) }}">
                        @csrf
                        <label>Status</label>
                        <select name="status">
                            @foreach (['diajukan', 'dijadwalkan', 'selesai', 'dibatalkan'] as $status)
                                <option value="{{ $status }}" @selected($item->status === $status)>{{ ucfirst($status) }}</option>
                            @endforeach
                        </select>
                        <label>Jadwal konsultasi</label>
                        <input name="scheduled_at" type="datetime-local" value="{{ $item->scheduled_at ? date('Y-m-d\TH:i', strtotime($item->scheduled_at)) : '' }}">
                        <label>Catatan hasil bimbingan</label>
                        <textarea name="lecturer_note">{{ $item->lecturer_note }}</textarea>
                        <label>Rekomendasi PA</label>
                        <textarea name="recommendation">{{ $item->recommendation }}</textarea>
                        <p><button class="button" type="submit">Simpan Catatan</button></p>
                    </form>
                </article>
            @empty
                <p>Belum ada pengajuan konsultasi PA.</p>
            @endforelse
        </section>
    </div>
</main>
</body>
</html>
