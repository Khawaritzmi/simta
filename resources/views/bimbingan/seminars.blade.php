@extends('bimbingan.layout')

@section('content')
    <section class="panel">
        <h3>Riwayat Seminar / Ujian</h3>
        <table class="table">
            <thead>
            <tr>
                <th>Mahasiswa</th>
                <th>Jenis</th>
                <th>Jadwal</th>
                <th>Nilai</th>
                <th>Penilaian</th>
            </tr>
            </thead>
            <tbody>
            @foreach ($seminars as $seminar)
                <tr>
                    <td><strong>{{ $seminar->student_name }}</strong><br><span class="muted">{{ $seminar->nim }}</span></td>
                    <td>{{ $seminar->type }}<br><span class="muted">{{ $seminar->title }}</span></td>
                    <td>{{ \Carbon\Carbon::parse($seminar->scheduled_at)->format('d M Y H:i') }}<br><span class="muted">{{ $seminar->room }}</span></td>
                    <td>{{ $seminar->score ?? '-' }}<br><span class="badge">{{ $seminar->status }}</span></td>
                    <td>
                        <form method="post" action="{{ route('seminars.grade', $seminar->id) }}">
                            @csrf
                            <div class="actions">
                                <input style="max-width:90px" type="number" name="score" min="0" max="100" value="{{ $seminar->score }}">
                                <input name="feedback" placeholder="Catatan penilaian" value="{{ $seminar->feedback }}">
                                <button class="button small" type="submit">Simpan</button>
                            </div>
                        </form>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </section>
@endsection
