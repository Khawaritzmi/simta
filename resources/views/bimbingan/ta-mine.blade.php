@extends('bimbingan.layout')

@section('content')
    <section class="panel">
        <h3>Bimbingan TA Saya</h3>
        <p class="actions">
            <a class="button secondary" href="{{ route('dosen.export-report') }}" download>Export Progress TA/PA CSV</a>
        </p>
        <form class="filters" method="get" action="{{ route('guidance.mine') }}">
            <strong>Filter</strong>
            <input name="nim" placeholder="NIM" value="{{ $filters['nim'] ?? '' }}">
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
                <th>Status Seminar</th>
                <th>Status</th>
                <th>Catatan Bimbingan</th>
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
                            <progress class="progress" max="100" value="{{ $guidance->ta_progress['percent'] }}">{{ $guidance->ta_progress['fraction'] }}</progress>
                            <strong>{{ $guidance->ta_progress['fraction'] }}</strong>
                        </div>
                        <span class="muted">{{ $guidance->ta_progress['percent'] }}% dari target bimbingan</span>
                    </td>
                    <td><span class="badge">{{ $guidance->seminar_status }}</span></td>
                    <td><span class="badge {{ $guidance->status === 'active' ? 'success' : '' }}">{{ ucfirst($guidance->status) }}</span></td>
                    <td>
                        <form method="post" action="{{ route('guidance-log.store') }}">
                            @csrf
                            <input type="hidden" name="student_id" value="{{ $guidance->student_id }}">
                            <input type="hidden" name="type" value="TA">
                            <input name="completed_at" type="date" value="{{ now()->toDateString() }}">
                            <input name="notes" placeholder="Catatan bimbingan">
                            <button class="button small" type="submit">Tambah Bimbingan</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="7">0 record ditemukan.</td></tr>
            @endforelse
            </tbody>
        </table>
    </section>
@endsection
