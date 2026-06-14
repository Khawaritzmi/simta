<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Bimbingan PA Dosen</title>
    @vite(['resources/css/pa-dosen.css', 'resources/js/app.js'])
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
