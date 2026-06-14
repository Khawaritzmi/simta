@extends('mahasiswa.layout')

@section('content')
    <section class="panel">
        <h3>Bimbingan PA</h3>
        @if ($paAssignment)
            <div class="grid-2">
                <div>
                    <table class="table">
                        <tr><th>Dosen PA</th><td>{{ $paAssignment->lecturer_name }}</td></tr>
                        <tr><th>Email</th><td>{{ $paAssignment->lecturer_email ?? '-' }}</td></tr>
                        <tr><th>No. Telp</th><td>{{ $paAssignment->lecturer_phone ?? '-' }}</td></tr>
                        <tr><th>Tahun Akademik</th><td>{{ $paAssignment->academic_year }}</td></tr>
                        <tr><th>IPK Terakhir</th><td>{{ $latestRecord?->ipk ?? '-' }}</td></tr>
                        <tr><th>SKS Total</th><td>{{ $latestRecord?->sks_total ?? '-' }}</td></tr>
                    </table>
                </div>
                <form method="post" action="{{ route('pa.mahasiswa.consultations.store') }}">
                    @csrf
                    <div class="field">
                        <label for="topic">Topik Konsultasi</label>
                        <input id="topic" name="topic" value="{{ old('topic') }}" placeholder="Contoh: Rencana KRS semester depan" required>
                    </div>
                    <div class="field">
                        <label for="requested_at">Waktu yang Diusulkan</label>
                        <input id="requested_at" name="requested_at" type="datetime-local" value="{{ old('requested_at') }}">
                    </div>
                    <div class="field">
                        <label for="student_note">Catatan Mahasiswa</label>
                        <textarea id="student_note" name="student_note" required>{{ old('student_note') }}</textarea>
                    </div>
                    <button class="button" type="submit">Kirim Konsultasi PA</button>
                </form>
            </div>
        @else
            <p class="muted">Dosen PA belum ditetapkan oleh admin.</p>
        @endif
    </section>

    <div class="grid-2">
        <section class="panel">
            <h3>Riwayat Konsultasi PA</h3>
            <table class="table">
                <thead><tr><th>Topik</th><th>Status</th><th>Catatan Dosen</th></tr></thead>
                <tbody>
                @forelse ($paConsultations as $item)
                    <tr>
                        <td><strong>{{ $item->topic }}</strong><br><span class="muted">{{ $item->created_at }}</span></td>
                        <td><span class="badge">{{ $item->status }}</span></td>
                        <td>{{ $item->lecturer_note ?? 'Menunggu catatan dosen.' }} @if($item->recommendation)<br><strong>Rekomendasi:</strong> {{ $item->recommendation }} @endif</td>
                    </tr>
                @empty
                    <tr><td colspan="3">Belum ada konsultasi PA.</td></tr>
                @endforelse
                </tbody>
            </table>
        </section>

        <section class="panel">
            <h3>Monitoring IPK dan SKS</h3>
            <table class="table">
                <thead><tr><th>Semester</th><th>IPK</th><th>SKS Semester</th><th>SKS Total</th></tr></thead>
                <tbody>
                @forelse ($paRecords as $record)
                    <tr>
                        <td>{{ $record->semester }}</td>
                        <td>{{ $record->ipk }}</td>
                        <td>{{ $record->sks_semester }}</td>
                        <td>{{ $record->sks_total }}</td>
                    </tr>
                @empty
                    <tr><td colspan="4">Belum ada data akademik.</td></tr>
                @endforelse
                </tbody>
            </table>
        </section>
    </div>
@endsection
