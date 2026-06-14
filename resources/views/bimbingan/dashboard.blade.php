@extends('bimbingan.layout')

@section('content')
    <section class="panel">
        <h3>Bimbingan Aktif</h3>
        <table class="table">
            <thead>
            <tr>
                <th>#</th>
                <th>Bimbingan</th>
                <th>NIM</th>
                <th>Nama</th>
                <th>Status Seminar</th>
            </tr>
            </thead>
            <tbody>
            @forelse ($activeGuidances as $index => $guidance)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>
                        <strong>{{ $guidance->title }}</strong><br>
                        <span class="muted">{{ $guidance->last_note }}</span>
                    </td>
                    <td>{{ $guidance->nim }}</td>
                    <td>{{ $guidance->student_name }}</td>
                    <td><span class="badge warning">{{ $guidance->seminar_status }}</span></td>
                </tr>
            @empty
                <tr><td colspan="5">Data tidak ditemukan.</td></tr>
            @endforelse
            </tbody>
        </table>
    </section>
@endsection
