<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Profil Admin | Sistem Informasi Manajemen Tugas Akhir</title>
    @vite(['resources/css/admin-dashboard.css', 'resources/js/app.js'])
</head>
<body>
<header class="topbar">
    <div class="brand"><x-unm-logo class="brand-logo" /><span>ADMIN SIMTA</span></div>
    <div class="top-actions">
        <span class="user-chip">Admin: {{ $admin->name }}</span>
        <a href="{{ route('landing') }}">Beranda</a>
        <a href="{{ route('admin.bimbingan-pa') }}">CRUD PA</a>
        <a href="{{ route('admin.seminars') }}">Jadwal Seminar</a>
        <a href="{{ route('admin.database-ta') }}">DELTA-MAT</a>
        <a href="{{ route('admin.kolektif-update') }}">Update Kolektif</a>
        <a href="{{ route('admin.settings') }}">Settings</a>
        <a href="{{ route('admin.export-report') }}" download>Export CSV</a>
        <a href="{{ route('password.edit') }}">Ubah Password</a>
        <form method="post" action="{{ route('logout') }}">
            @csrf
            <button class="secondary" type="submit">Logout</button>
        </form>
    </div>
</header>

<main class="page">
    <h1>Profil Admin</h1>
    <p>{{ $admin->name }} - {{ $admin->email }}. Kelola persetujuan Bimbingan TA, akses Bimbingan PA, dan database judul dari satu halaman admin.</p>

    @if (session('status'))
        <div class="flash">{{ session('status') }}</div>
    @endif
    @if ($errors->any())
        <div class="errors">{{ $errors->first() }}</div>
    @endif

    <section class="cards">
        <article class="card"><span>Pengajuan TA</span><strong>{{ $stats['ta_requests'] }}</strong></article>
        <article class="card"><span>TA Pending</span><strong>{{ $stats['ta_pending'] }}</strong></article>
        <article class="card"><span>Mahasiswa PA</span><strong>{{ $stats['pa_assignments'] }}</strong></article>
        <article class="card"><span>Konsultasi PA</span><strong>{{ $stats['pa_consultations'] }}</strong></article>
    </section>

    <div class="grid">
        <aside>
            <section class="panel">
                <h2>Akses Cepat Admin</h2>
                <p>Halaman ini menjadi pusat admin. CRUD rinci tetap tersedia untuk data PA dan database judul.</p>
                <p class="actions">
                    <a class="button secondary" href="{{ route('admin.bimbingan-pa') }}">Kelola Bimbingan PA</a>
                    <a class="button secondary" href="{{ route('admin.seminars') }}">Atur Jadwal Seminar</a>
                    <a class="button secondary" href="{{ route('admin.database-ta') }}">Kelola DELTA-MAT</a>
                    <a class="button secondary" href="{{ route('admin.kolektif-update') }}">Update Database Kolektif</a>
                    <a class="button secondary" href="{{ route('admin.settings') }}">Pengaturan Target</a>
                    <a class="button secondary" href="{{ route('admin.export-report') }}" download>Export Laporan CSV</a>
                </p>
            </section>

            <section class="panel">
                <h2>Data Master</h2>
                <table class="table">
                    <tr><th>Mahasiswa</th><td>{{ $stats['students'] }}</td></tr>
                    <tr><th>Dosen</th><td>{{ $stats['lecturers'] }}</td></tr>
                    <tr><th>Penetapan PA</th><td>{{ $stats['pa_assignments'] }}</td></tr>
                    <tr><th>Konsultasi PA</th><td>{{ $stats['pa_consultations'] }}</td></tr>
                </table>
            </section>

            <section class="panel">
                <h2>Bimbingan PA</h2>
                <table class="table">
                    <thead><tr><th>Mahasiswa</th><th>Dosen PA</th><th>Status</th></tr></thead>
                    <tbody>
                    @forelse ($paAssignments as $assignment)
                        <tr>
                            <td><strong>{{ $assignment->student_name }}</strong><br><span class="muted">{{ $assignment->nim }}</span></td>
                            <td>{{ $assignment->lecturer_name }}</td>
                            <td><span class="badge">{{ $assignment->status }}</span></td>
                        </tr>
                    @empty
                        <tr><td colspan="3">Belum ada penetapan dosen PA.</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </section>
        </aside>

        <section class="panel">
            <h2>Persetujuan Pengajuan Bimbingan TA</h2>
            <table class="table">
                <thead>
                <tr>
                    <th>Mahasiswa</th>
                    <th>Judul</th>
                    <th>Dosen Terkait</th>
                    <th>Status</th>
                    <th>Alasan</th>
                    <th>Aksi Admin</th>
                </tr>
                </thead>
                <tbody>
                @forelse ($guidanceRequests as $item)
                    <tr>
                        <td><strong>{{ $item->student_name }}</strong><br><span class="muted">{{ $item->nim }}</span></td>
                        <td>{{ $item->title }}<br><span class="muted">{{ $item->created_at }}</span></td>
                        <td>
                            <strong>Pembimbing:</strong> {{ $item->supervisor_1_name }} <span class="badge {{ $item->supervisor_1_status === 'approved' ? 'success' : ($item->supervisor_1_status === 'rejected' ? 'danger' : 'warning') }}">{{ $item->supervisor_1_status }}</span>,
                            {{ $item->supervisor_2_name }} <span class="badge {{ $item->supervisor_2_status === 'approved' ? 'success' : ($item->supervisor_2_status === 'rejected' ? 'danger' : 'warning') }}">{{ $item->supervisor_2_status }}</span><br>
                            <strong>Penguji:</strong> {{ $item->examiner_1_name }} <span class="badge {{ $item->examiner_1_status === 'approved' ? 'success' : ($item->examiner_1_status === 'rejected' ? 'danger' : 'warning') }}">{{ $item->examiner_1_status }}</span>,
                            {{ $item->examiner_2_name }} <span class="badge {{ $item->examiner_2_status === 'approved' ? 'success' : ($item->examiner_2_status === 'rejected' ? 'danger' : 'warning') }}">{{ $item->examiner_2_status }}</span>
                        </td>
                        <td>
                            <span class="badge {{ $item->admin_status === 'approved' ? 'success' : ($item->admin_status === 'rejected' ? 'danger' : 'warning') }}">Admin: {{ $item->admin_status }}</span><br>
                            <span class="badge {{ $item->status === 'approved' ? 'success' : ($item->status === 'rejected' ? 'danger' : 'warning') }}">Final: {{ $item->status }}</span>
                        </td>
                        <td>
                            @foreach ([
                                'Admin' => $item->admin_note,
                                'Pembimbing 1' => $item->supervisor_1_note,
                                'Pembimbing 2' => $item->supervisor_2_note,
                                'Penguji 1' => $item->examiner_1_note,
                                'Penguji 2' => $item->examiner_2_note,
                            ] as $label => $note)
                                @if ($note)
                                    <strong>{{ $label }}:</strong> {{ $note }}<br>
                                @endif
                            @endforeach
                            @if (! $item->admin_note && ! $item->supervisor_1_note && ! $item->supervisor_2_note && ! $item->examiner_1_note && ! $item->examiner_2_note)
                                <span class="muted">-</span>
                            @endif
                        </td>
                        <td>
                            @if ($item->admin_status === 'pending' && $item->status === 'pending')
                                <div class="actions">
                                    <form method="post" action="{{ route('admin.guidance-requests.decide', $item->id) }}">
                                        @csrf
                                        <input type="hidden" name="status" value="approved">
                                        <button class="small success" type="submit">Setujui</button>
                                    </form>
                                    <form class="reject-form" method="post" action="{{ route('admin.guidance-requests.decide', $item->id) }}">
                                        @csrf
                                        <input type="hidden" name="status" value="rejected">
                                        <textarea name="note" required placeholder="Alasan penolakan"></textarea>
                                        <button class="small danger" type="submit">Tolak</button>
                                    </form>
                                </div>
                            @else
                                <span class="muted">{{ $item->admin_note ?: 'Keputusan admin sudah tercatat.' }}</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6">Belum ada pengajuan Bimbingan TA.</td></tr>
                @endforelse
                </tbody>
            </table>
        </section>
    </div>
</main>
</body>
</html>
