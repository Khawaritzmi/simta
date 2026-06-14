@extends('bimbingan.layout')

@section('content')
    <section class="panel">
        <h3>Ringkasan Bimbingan PA</h3>
        <p class="actions">
            <a class="button secondary" href="{{ route('dosen.export-report') }}" download>Export Progress TA/PA CSV</a>
        </p>
        <div class="form-grid">
            <div>
                <div class="detail-row"><strong>Mahasiswa PA</strong><span>{{ $paReport['students'] }}</span></div>
                <div class="detail-row"><strong>Total Konsultasi</strong><span>{{ $paReport['consultations'] }}</span></div>
            </div>
            <div>
                <div class="detail-row"><strong>Perlu Diproses</strong><span>{{ $paReport['pending'] }}</span></div>
                <div class="detail-row"><strong>Selesai</strong><span>{{ $paReport['done'] }}</span></div>
            </div>
        </div>
    </section>

    <div class="grid-2 pa-grid">
        <section class="panel">
            <h3>Daftar Mahasiswa PA</h3>
            <table class="table">
                <thead><tr><th>Mahasiswa</th><th>Program</th><th>IPK/SKS</th><th>Status</th></tr></thead>
                <tbody>
                @forelse ($paStudents as $student)
                    <tr>
                        <td><strong>{{ $student->name }}</strong><br><span class="muted">{{ $student->nim }}</span></td>
                        <td>{{ $student->program }}</td>
                        <td>{{ $student->ipk ?? '-' }} / {{ $student->sks_total ?? '-' }} SKS</td>
                        <td><span class="badge table-badge">{{ $student->academic_status ?? 'Belum ada data' }}</span></td>
                    </tr>
                @empty
                    <tr><td colspan="4">Belum ada mahasiswa PA.</td></tr>
                @endforelse
                </tbody>
            </table>
        </section>

        <section class="panel">
            <h3>Pengajuan dan Catatan PA</h3>
            @forelse ($paConsultations as $item)
                <article class="consultation-item">
                    <p><strong>{{ $item->student_name }}</strong> - {{ $item->nim }}<br><strong>{{ $item->topic }}</strong><br>{{ $item->student_note }}</p>
                    <p><span class="badge">{{ $item->status }}</span> @if($item->scheduled_at) Jadwal: {{ $item->scheduled_at }} @endif</p>
                    <form method="post" action="{{ route('pa.dosen.consultations.update', $item->id) }}">
                        @csrf
                        <div class="form-grid">
                            <div>
                                <label>Status</label>
                                <select name="status">
                                    @foreach (['diajukan', 'dijadwalkan', 'selesai', 'dibatalkan'] as $status)
                                        <option value="{{ $status }}" @selected($item->status === $status)>{{ ucfirst($status) }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label>Jadwal konsultasi</label>
                                <input name="scheduled_at" type="datetime-local" value="{{ $item->scheduled_at ? date('Y-m-d\TH:i', strtotime($item->scheduled_at)) : '' }}">
                            </div>
                        </div>
                        <label>Catatan hasil bimbingan</label>
                        <textarea name="lecturer_note">{{ $item->lecturer_note }}</textarea>
                        <label>Rekomendasi PA</label>
                        <textarea name="recommendation">{{ $item->recommendation }}</textarea>
                        <p><button class="button small" type="submit">Simpan Catatan</button></p>
                    </form>
                </article>
            @empty
                <p class="muted">Belum ada pengajuan konsultasi PA.</p>
            @endforelse
        </section>
    </div>
@endsection
