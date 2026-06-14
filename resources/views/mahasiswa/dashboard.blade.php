@extends('mahasiswa.layout')

@section('content')
    <section class="panel">
        <h3>Status Bimbingan Saya</h3>
        <table class="table">
            <thead>
            <tr>
                <th>Judul</th>
                <th>Dosen Pembimbing</th>
                <th>Progress</th>
                <th>Status Seminar</th>
            </tr>
            </thead>
            <tbody>
            @forelse ($guidances as $guidance)
                <tr>
                    <td><strong>{{ $guidance->title }}</strong><br><span class="muted">{{ $guidance->last_note }}</span></td>
                    <td>{{ $guidance->lecturer_name }}<br><span class="muted">{{ $guidance->lecturer_email }}</span></td>
                    <td><div class="actions"><div class="progress"><span style="width:{{ $guidance->progress }}%"></span></div><strong>{{ $guidance->progress }}%</strong></div></td>
                    <td><span class="badge warning">{{ $guidance->seminar_status }}</span></td>
                </tr>
            @empty
                <tr><td colspan="4">Belum ada data bimbingan tugas akhir.</td></tr>
            @endforelse
            </tbody>
        </table>
    </section>

    <section class="panel">
        <h3>Jadwal Seminar</h3>
        <table class="table">
            <thead><tr><th>Jenis</th><th>Jadwal</th><th>Ruang</th><th>Status</th></tr></thead>
            <tbody>
            @forelse ($seminars as $seminar)
                <tr>
                    <td>{{ $seminar->type }}<br><span class="muted">{{ $seminar->title }}</span></td>
                    <td>{{ \Carbon\Carbon::parse($seminar->scheduled_at)->format('d M Y H:i') }}</td>
                    <td>{{ $seminar->room }}</td>
                    <td><span class="badge">{{ $seminar->status }}</span></td>
                </tr>
            @empty
                <tr><td colspan="4">Belum ada jadwal seminar.</td></tr>
            @endforelse
            </tbody>
        </table>
    </section>
@endsection
