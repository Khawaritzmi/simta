@extends('mahasiswa.layout')

@section('content')
    <section class="panel">
        <h3>Riwayat Bimbingan Tugas Akhir</h3>
        <table class="table">
            <thead><tr><th>Judul</th><th>Dosen</th><th>Progress</th><th>Catatan Terakhir</th></tr></thead>
            <tbody>
            @forelse ($guidances as $guidance)
                <tr>
                    <td>{{ $guidance->title }}</td>
                    <td>{{ $guidance->lecturer_name }}</td>
                    <td>{{ $guidance->progress }}%</td>
                    <td>{{ $guidance->last_note ?: '-' }}</td>
                </tr>
            @empty
                <tr><td colspan="4">Belum ada data bimbingan.</td></tr>
            @endforelse
            </tbody>
        </table>
    </section>
@endsection
