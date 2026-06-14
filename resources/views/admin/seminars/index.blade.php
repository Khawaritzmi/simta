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
    <p class="lead">Admin menjadwalkan seminar proposal, seminar hasil, atau ujian tugas akhir. Jadwal yang tersimpan akan terlihat pada halaman mahasiswa dan dosen pembimbing.</p>

    @if (session('status'))
        <div class="flash">{{ session('status') }}</div>
    @endif
    @if ($errors->any())
        <div class="errors">{{ $errors->first() }}</div>
    @endif

    <section class="panel">
        <h2>Pengajuan Jadwal Seminar / Ujian</h2>
        <table class="table">
            <thead>
            <tr>
                <th>Mahasiswa</th>
                <th>Jenis</th>
                <th>Usulan Jadwal</th>
                <th>Status</th>
                <th>Alasan</th>
                <th>Aksi Admin</th>
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
                        <span class="badge {{ $request->admin_status === 'approved' ? 'success' : ($request->admin_status === 'rejected' ? 'danger' : '') }}">Admin: {{ $request->admin_status }}</span>
                    </td>
                    <td>{{ $request->admin_note ?: '-' }}</td>
                    <td>
                        @if ($request->admin_status === 'pending' && $request->status === 'pending')
                            <div class="row-actions">
                                <form method="post" action="{{ route('admin.seminar-requests.decide', $request->id) }}">
                                    @csrf
                                    <input type="hidden" name="status" value="approved">
                                    <button class="small" type="submit">Setujui</button>
                                </form>
                                <form class="reject-form" method="post" action="{{ route('admin.seminar-requests.decide', $request->id) }}">
                                    @csrf
                                    <input type="hidden" name="status" value="rejected">
                                    <textarea name="note" required placeholder="Alasan penolakan"></textarea>
                                    <button class="small danger" type="submit">Tolak</button>
                                </form>
                            </div>
                        @else
                            <span class="muted">Keputusan admin sudah tercatat.</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr><td colspan="6">Belum ada pengajuan jadwal seminar/ujian.</td></tr>
            @endforelse
            </tbody>
        </table>
    </section>

    <div class="grid">
        <section class="panel">
            <h2>{{ $editing ? 'Edit Jadwal' : 'Tambah Jadwal' }}</h2>
            <form method="post" action="{{ $editing ? route('admin.seminars.update', $editing->id) : route('admin.seminars.store') }}">
                @csrf
                @if ($editing)
                    @method('PUT')
                @endif

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
                    <button type="submit">{{ $editing ? 'Simpan Perubahan' : 'Tambah Jadwal' }}</button>
                    @if ($editing)
                        <a class="button secondary" href="{{ route('admin.seminars') }}">Batal Edit</a>
                    @endif
                </div>
            </form>
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
