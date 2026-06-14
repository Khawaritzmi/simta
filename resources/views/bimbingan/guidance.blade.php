@extends('bimbingan.layout')

@section('content')
    <section class="panel">
        <h3>Data</h3>
        <form class="filters" method="get" action="{{ route('guidance') }}">
            <strong>Filter</strong>
            <input name="nim" placeholder="Nim" value="{{ $filters['nim'] ?? '' }}">
            <input name="nama" placeholder="Nama" value="{{ $filters['nama'] ?? '' }}">
            <input name="judul" placeholder="Judul" value="{{ $filters['judul'] ?? '' }}">
            <button class="button" type="submit">Cari</button>
        </form>
        <table class="table">
            <thead>
            <tr>
                <th>NIM</th>
                <th>Nama</th>
                <th>Judul</th>
                <th>Progress</th>
                <th>Status</th>
            </tr>
            </thead>
            <tbody>
            @forelse ($guidances as $guidance)
                <tr>
                    <td>{{ $guidance->nim }}</td>
                    <td>{{ $guidance->student_name }}</td>
                    <td>{{ $guidance->title }}</td>
                    <td>
                        <div class="actions">
                            <div class="progress"><span style="width:{{ $guidance->progress }}%"></span></div>
                            <strong>{{ $guidance->progress }}%</strong>
                        </div>
                    </td>
                    <td><span class="badge {{ $guidance->status === 'active' ? 'success' : '' }}">{{ ucfirst($guidance->status) }}</span></td>
                </tr>
            @empty
                <tr><td colspan="5">0 record ditemukan.</td></tr>
            @endforelse
            </tbody>
        </table>
    </section>
@endsection
