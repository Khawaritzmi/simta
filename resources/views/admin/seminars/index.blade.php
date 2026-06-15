<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin Jadwal Seminar | Sistem Informasi Manajemen Tugas Akhir</title>
    @vite(['resources/css/admin-seminars.css', 'resources/js/app.js'])
</head>
<body>
<header class="topbar">
    <div class="brand"><x-unm-logo class="brand-logo" /><span>ADMIN SEMINAR</span></div>
    <div class="top-actions">
        <a href="{{ route('admin.dashboard') }}">Dashboard Admin</a>
        <a href="{{ route('admin.bimbingan-pa') }}">CRUD PA</a>
        <a href="{{ route('admin.database-ta') }}">DELTA-MAT</a>
        <a href="{{ route('admin.kolektif-update') }}">Update Kolektif</a>
        <a href="{{ route('admin.settings') }}">Settings</a>
        <a href="{{ route('password.edit') }}">Ubah Password</a>
        <form method="post" action="{{ route('logout') }}">
            @csrf
            <button class="secondary" type="submit">Logout</button>
        </form>
    </div>
</header>

<main class="page">
    <h1>Penjadwalan Seminar / Ujian TA</h1>
    <p class="lead">Admin membuat usulan jadwal seminar/ujian. Jadwal resmi baru masuk ke daftar setelah disetujui dua dosen pembimbing dan dua dosen penguji.</p>

    @if (session('status'))
        <div class="flash">{{ session('status') }}</div>
    @endif
    @if ($errors->any())
        <div class="errors">{{ $errors->first() }}</div>
    @endif

    <section class="panel">
        <h2>Jadwal Menunggu Persetujuan Dosen</h2>
        <table class="table">
            <thead>
            <tr>
                <th>Mahasiswa</th>
                <th>Jenis</th>
                <th>Jadwal dari Admin</th>
                <th>Status</th>
                <th>Validasi Dosen</th>
                <th>Catatan / Alasan</th>
            </tr>
            </thead>
            <tbody>
            @forelse ($seminarRequests as $request)
                <tr>
                    <td><strong>{{ $request->student_name }}</strong><br><span class="muted">{{ $request->nim }}</span><br><span class="muted">{{ $request->title }}</span></td>
                    <td>{{ $request->type }}<br><span class="muted">{{ $request->student_note ?: '-' }}</span></td>
                    <td>{{ \Carbon\Carbon::parse($request->proposed_at)->format('d M Y H:i') }}<br><span class="muted">{{ $request->room ?: '-' }}</span></td>
                    <td>
                        <span class="badge {{ $request->status === 'approved' ? 'success' : ($request->status === 'rejected' ? 'danger' : '') }}">Final: {{ $request->status }}</span><br>
                        <span class="badge success">Admin: dibuat</span>
                    </td>
                    <td>
                        Pembimbing 1: <span class="badge {{ $request->supervisor_1_status === 'approved' ? 'success' : ($request->supervisor_1_status === 'rejected' ? 'danger' : '') }}">{{ $request->supervisor_1_status }}</span><br>
                        Pembimbing 2: <span class="badge {{ $request->supervisor_2_status === 'approved' ? 'success' : ($request->supervisor_2_status === 'rejected' ? 'danger' : '') }}">{{ $request->supervisor_2_status }}</span><br>
                        Penguji 1: <span class="badge {{ $request->examiner_1_status === 'approved' ? 'success' : ($request->examiner_1_status === 'rejected' ? 'danger' : '') }}">{{ $request->examiner_1_status }}</span><br>
                        Penguji 2: <span class="badge {{ $request->examiner_2_status === 'approved' ? 'success' : ($request->examiner_2_status === 'rejected' ? 'danger' : '') }}">{{ $request->examiner_2_status }}</span>
                    </td>
                    <td>
                        @foreach ([
                            'Pembimbing 1' => $request->supervisor_1_note,
                            'Pembimbing 2' => $request->supervisor_2_note,
                            'Penguji 1' => $request->examiner_1_note,
                            'Penguji 2' => $request->examiner_2_note,
                        ] as $label => $note)
                            @if ($note)
                                <strong>{{ $label }}:</strong> {{ $note }}<br>
                            @endif
                        @endforeach
                        @if (! $request->supervisor_1_note && ! $request->supervisor_2_note && ! $request->examiner_1_note && ! $request->examiner_2_note)
                            <span class="muted">-</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr><td colspan="6">Belum ada jadwal yang menunggu persetujuan dosen.</td></tr>
            @endforelse
            </tbody>
        </table>
    </section>

    <div class="grid">
        <section class="panel">
            @if ($editing)
                <h2>Edit Jadwal</h2>
                <form method="post" action="{{ route('admin.seminars.update', $editing->id) }}">
                    @csrf
                    @method('PUT')

                    <div class="field">
                        <label for="thesis_guidance_id">Mahasiswa / Bimbingan TA</label>
                        <select id="thesis_guidance_id" name="thesis_guidance_id" required>
                            <option value="">Pilih bimbingan TA</option>
                            @foreach ($guidances as $guidance)
                                <option value="{{ $guidance->id }}" @selected((int) old('thesis_guidance_id', $editing->thesis_guidance_id ?? '') === $guidance->id)>
                                    {{ $guidance->student_name }} - {{ $guidance->nim }} | {{ $guidance->title }} | Pembimbing: {{ $guidance->lecturer_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="two">
                        <div class="field">
                            <label for="type">Jenis Seminar</label>
                            <select id="type" name="type" required>
                                @foreach ($types as $type)
                                    <option value="{{ $type }}" @selected(old('type', $editing->type ?? 'Seminar Proposal') === $type)>{{ $type }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="field">
                            <label for="status">Status</label>
                            <select id="status" name="status" required>
                                @foreach ($statuses as $status)
                                    <option value="{{ $status }}" @selected(old('status', $editing->status ?? 'scheduled') === $status)>{{ ucfirst($status) }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="two">
                        <div class="field">
                            <label for="scheduled_at">Tanggal dan Jam</label>
                            <input id="scheduled_at" name="scheduled_at" type="datetime-local" value="{{ old('scheduled_at', $editing?->scheduled_at ? date('Y-m-d\TH:i', strtotime($editing->scheduled_at)) : '') }}" required>
                        </div>
                        <div class="field">
                            <label for="room">Ruangan</label>
                            <input id="room" name="room" value="{{ old('room', $editing->room ?? '') }}" placeholder="Ruang Seminar Matematika">
                        </div>
                    </div>

                    <div class="two">
                        <div class="field">
                            <label for="score">Nilai</label>
                            <input id="score" name="score" type="number" min="0" max="100" value="{{ old('score', $editing->score ?? '') }}" placeholder="Opsional">
                        </div>
                        <div class="field">
                            <label for="feedback">Catatan</label>
                            <textarea id="feedback" name="feedback" placeholder="Opsional">{{ old('feedback', $editing->feedback ?? '') }}</textarea>
                        </div>
                    </div>

                    <div class="form-actions">
                        <button type="submit">Simpan Perubahan</button>
                        <a class="button secondary" href="{{ route('admin.seminars') }}">Batal Edit</a>
                    </div>
                </form>
            @else
                <h2>Buat Jadwal untuk Approval Dosen</h2>
                <p class="muted">Form ini membuat usulan jadwal. Sistem akan meminta persetujuan pembimbing dan penguji sebelum jadwal resmi diterbitkan.</p>
                <form method="post" action="{{ route('admin.seminars.store') }}">
                    @csrf

                    <div class="field">
                        <label for="thesis_guidance_id">Mahasiswa / Bimbingan TA</label>
                        <select id="thesis_guidance_id" name="thesis_guidance_id" required>
                            <option value="">Pilih bimbingan TA</option>
                            @foreach ($guidances as $guidance)
                                <option value="{{ $guidance->id }}" @selected((int) old('thesis_guidance_id') === $guidance->id)>
                                    {{ $guidance->student_name }} - {{ $guidance->nim }} | {{ $guidance->title }} | Pembimbing: {{ $guidance->lecturer_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="two">
                        <div class="field">
                            <label for="type">Jenis Seminar</label>
                            <select id="type" name="type" required>
                                @foreach ($types as $type)
                                    <option value="{{ $type }}" @selected(old('type', 'Seminar Proposal') === $type)>{{ $type }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="field">
                            <label for="proposed_at">Tanggal dan Jam</label>
                            <input id="proposed_at" name="proposed_at" type="datetime-local" value="{{ old('proposed_at') }}" required>
                        </div>
                    </div>

                    <div class="field">
                        <label for="room">Ruangan</label>
                        <input id="room" name="room" value="{{ old('room') }}" placeholder="Ruang Seminar Matematika">
                    </div>

                    <div class="field">
                        <label for="note">Catatan untuk dosen</label>
                        <textarea id="note" name="note" placeholder="Opsional">{{ old('note') }}</textarea>
                    </div>

                    <div class="form-actions">
                        <button type="submit">Kirim ke Dosen</button>
                    </div>
                </form>
            @endif
        </section>

        <section class="panel">
            <h2>Daftar Jadwal</h2>
            <form class="search" method="get" action="{{ route('admin.seminars') }}">
                <input name="q" value="{{ $query }}" placeholder="Cari NIM, nama, judul, jenis, atau ruangan">
                <button type="submit">Cari</button>
            </form>

            <table class="table">
                <thead>
                <tr>
                    <th>Mahasiswa</th>
                    <th>Jenis</th>
                    <th>Jadwal</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
                </thead>
                <tbody>
                @forelse ($seminars as $seminar)
                    <tr>
                        <td>
                            <strong>{{ $seminar->student_name }}</strong><br>
                            <span class="muted">{{ $seminar->nim }}</span><br>
                            <span class="muted">{{ $seminar->title }}</span>
                        </td>
                        <td>{{ $seminar->type }}<br><span class="muted">Pembimbing: {{ $seminar->lecturer_name }}</span></td>
                        <td>{{ \Carbon\Carbon::parse($seminar->scheduled_at)->format('d M Y H:i') }}<br><span class="muted">{{ $seminar->room ?: '-' }}</span></td>
                        <td>
                            <span class="badge {{ $seminar->status === 'graded' ? 'success' : ($seminar->status === 'cancelled' ? 'danger' : '') }}">{{ $seminar->status }}</span>
                            @if (! is_null($seminar->score))
                                <br><span class="muted">Nilai: {{ $seminar->score }}</span>
                            @endif
                        </td>
                        <td>
                            <div class="row-actions">
                                <a class="button small secondary" href="{{ route('admin.seminars.edit', $seminar->id) }}">Edit</a>
                                <form method="post" action="{{ route('admin.seminars.destroy', $seminar->id) }}">
                                    @csrf
                                    @method('DELETE')
                                    <button class="small danger" type="submit">Hapus</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5">Belum ada jadwal seminar.</td></tr>
                @endforelse
                </tbody>
            </table>

            <div class="pagination">
                <span>Menampilkan {{ $seminars->count() }} dari {{ $seminars->total() }} jadwal</span>
                <div class="pager-buttons">
                    @if ($seminars->onFirstPage())
                        <span class="pager-disabled">Sebelumnya</span>
                    @else
                        <a class="pager-link" href="{{ $seminars->previousPageUrl() }}">Sebelumnya</a>
                    @endif
                    @if ($seminars->hasMorePages())
                        <a class="pager-link" href="{{ $seminars->nextPageUrl() }}">Berikutnya</a>
                    @else
                        <span class="pager-disabled">Berikutnya</span>
                    @endif
                </div>
            </div>
        </section>
    </div>
</main>
</body>
</html>
