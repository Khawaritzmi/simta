<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Bimbingan PA Mahasiswa</title>
    @vite(['resources/css/pa-mahasiswa.css', 'resources/js/app.js'])
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
