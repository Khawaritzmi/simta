@extends('mahasiswa.layout')

@section('content')
    <section class="panel">
        <h3>Monitoring Tugas Akhir Saya</h3>
        <table class="table">
            <thead><tr><th>Judul</th><th>Dosen Pembimbing</th><th>Progress</th><th>Status Seminar</th><th>Catatan Terakhir</th></tr></thead>
            <tbody>
            @forelse ($guidances as $guidance)
                <tr>
                    <td>{{ $guidance->title }}</td>
                    <td>{{ $guidance->lecturer_name }}<br><span class="muted">{{ $guidance->lecturer_email ?? '-' }}</span></td>
                    <td>
                        <div class="actions">
                            <progress class="progress" max="100" value="{{ $guidance->progress }}">{{ $guidance->progress }}%</progress>
                            <strong>{{ $guidance->ta_progress['fraction'] }}</strong>
                        </div>
                        <span class="muted">{{ $guidance->ta_progress['percent'] }}% dari target bimbingan</span>
                    </td>
                    <td><span class="badge">{{ $guidance->seminar_status }}</span></td>
                    <td>{{ $guidance->last_note ?: '-' }}</td>
                </tr>
            @empty
                <tr><td colspan="5">Belum ada bimbingan TA aktif.</td></tr>
            @endforelse
            </tbody>
        </table>
    </section>

    @foreach ($guidances as $guidance)
        @if (in_array($guidance->status, ['approved', 'active'], true))
            <section class="panel">
                <h3>Pengajuan Jadwal Seminar / Ujian - {{ $guidance->title }}</h3>
                <p class="muted">Pengajuan jadwal akan berjalan setelah disetujui admin, dua dosen pembimbing, dan dua dosen penguji. Jika ditolak, alasan akan tampil pada tabel status.</p>
                <form method="post" action="{{ route('mahasiswa.seminar-requests.store') }}">
                    @csrf
                    <input type="hidden" name="thesis_guidance_id" value="{{ $guidance->id }}">
                    <div class="form-grid">
                        <div>
                            <label for="seminar-type-{{ $guidance->id }}">Jenis</label>
                            <select id="seminar-type-{{ $guidance->id }}" name="type" required>
                                @foreach ($seminarTypes as $type)
                                    <option value="{{ $type }}" @selected(old('type') === $type)>{{ $type }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label for="proposed-at-{{ $guidance->id }}">Usulan Tanggal dan Jam</label>
                            <input id="proposed-at-{{ $guidance->id }}" name="proposed_at" type="datetime-local" value="{{ old('proposed_at') }}" required>
                        </div>
                        <div>
                            <label for="room-{{ $guidance->id }}">Usulan Ruang</label>
                            <input id="room-{{ $guidance->id }}" name="room" value="{{ old('room') }}" placeholder="Ruang Seminar Matematika">
                        </div>
                        <div>
                            <label for="student-note-{{ $guidance->id }}">Catatan Mahasiswa</label>
                            <textarea id="student-note-{{ $guidance->id }}" name="student_note" placeholder="Opsional">{{ old('student_note') }}</textarea>
                        </div>
                    </div>
                    <p><button class="button" type="submit">Kirim Pengajuan Jadwal</button></p>
                </form>
            </section>

            <section class="panel">
                <h3>Upload Dokumen TA - {{ $guidance->title }}</h3>
                <p class="muted">Upload tersedia setelah TA disetujui. Format wajib PDF, maksimal 2MB per file. Upload baru akan mengganti file lama pada kategori yang sama.</p>
                <table class="table">
                    <thead><tr><th>Kategori</th><th>File Saat Ini</th><th>Upload / Ganti</th></tr></thead>
                    <tbody>
                    @foreach ($uploadCategories as $category => $label)
                        @php($existing = $uploads->get($guidance->id)?->get($category))
                        <tr>
                            <td>{{ $label }}</td>
                            <td>
                                @if ($existing)
                                    <a href="{{ $existing->url }}" target="_blank">{{ $existing->original_name }}</a>
                                @else
                                    <span class="muted">Belum ada file.</span>
                                @endif
                            </td>
                            <td>
                                <form method="post" action="{{ route('mahasiswa.thesis-uploads.store') }}" enctype="multipart/form-data">
                                    @csrf
                                    <input type="hidden" name="thesis_guidance_id" value="{{ $guidance->id }}">
                                    <input type="hidden" name="category" value="{{ $category }}">
                                    <div class="actions">
                                        <input name="file" type="file" accept="application/pdf" required>
                                        <button class="button small" type="submit">{{ $existing ? 'Ganti' : 'Upload' }}</button>
                                    </div>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </section>
        @endif
    @endforeach

    <section class="panel">
        <h3>Status Pengajuan Jadwal Seminar / Ujian</h3>
        <table class="table">
            <thead>
            <tr>
                <th>Jenis</th>
                <th>Judul</th>
                <th>Usulan Jadwal</th>
                <th>Status</th>
                <th>Validasi</th>
                <th>Alasan</th>
            </tr>
            </thead>
            <tbody>
            @forelse ($seminarRequests as $request)
                <tr>
                    <td>{{ $request->type }}</td>
                    <td>{{ $request->title }}</td>
                    <td>{{ \Carbon\Carbon::parse($request->proposed_at)->format('d M Y H:i') }}<br><span class="muted">{{ $request->room ?: '-' }}</span></td>
                    <td><span class="badge {{ $request->status === 'approved' ? 'success' : ($request->status === 'rejected' ? 'danger' : 'warning') }}">{{ $request->status }}</span></td>
                    <td>
                        Admin: <span class="badge {{ $request->admin_status === 'approved' ? 'success' : ($request->admin_status === 'rejected' ? 'danger' : 'warning') }}">{{ $request->admin_status }}</span><br>
                        Pembimbing 1: <span class="badge {{ $request->supervisor_1_status === 'approved' ? 'success' : ($request->supervisor_1_status === 'rejected' ? 'danger' : 'warning') }}">{{ $request->supervisor_1_status }}</span><br>
                        Pembimbing 2: <span class="badge {{ $request->supervisor_2_status === 'approved' ? 'success' : ($request->supervisor_2_status === 'rejected' ? 'danger' : 'warning') }}">{{ $request->supervisor_2_status }}</span><br>
                        Penguji 1: <span class="badge {{ $request->examiner_1_status === 'approved' ? 'success' : ($request->examiner_1_status === 'rejected' ? 'danger' : 'warning') }}">{{ $request->examiner_1_status }}</span><br>
                        Penguji 2: <span class="badge {{ $request->examiner_2_status === 'approved' ? 'success' : ($request->examiner_2_status === 'rejected' ? 'danger' : 'warning') }}">{{ $request->examiner_2_status }}</span>
                    </td>
                    <td>
                        @foreach ([
                            'Admin' => $request->admin_note,
                            'Pembimbing 1' => $request->supervisor_1_note,
                            'Pembimbing 2' => $request->supervisor_2_note,
                            'Penguji 1' => $request->examiner_1_note,
                            'Penguji 2' => $request->examiner_2_note,
                        ] as $label => $note)
                            @if ($note)
                                <strong>{{ $label }}:</strong> {{ $note }}<br>
                            @endif
                        @endforeach
                        @if (! $request->admin_note && ! $request->supervisor_1_note && ! $request->supervisor_2_note && ! $request->examiner_1_note && ! $request->examiner_2_note)
                            <span class="muted">-</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr><td colspan="6">Belum ada pengajuan jadwal seminar atau ujian.</td></tr>
            @endforelse
            </tbody>
        </table>
    </section>

    <section class="panel">
        <h3>Jadwal Seminar / Ujian</h3>
        <table class="table">
            <thead><tr><th>Jenis</th><th>Judul</th><th>Jadwal</th><th>Ruang</th><th>Status</th></tr></thead>
            <tbody>
            @forelse ($seminars as $seminar)
                <tr>
                    <td>{{ $seminar->type }}</td>
                    <td>{{ $seminar->title }}</td>
                    <td>{{ $seminar->scheduled_at }}</td>
                    <td>{{ $seminar->room ?? '-' }}</td>
                    <td><span class="badge">{{ $seminar->status }}</span></td>
                </tr>
            @empty
                <tr><td colspan="5">Belum ada jadwal seminar atau ujian.</td></tr>
            @endforelse
            </tbody>
        </table>
    </section>
@endsection
