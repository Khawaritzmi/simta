<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $record->title }} | DELTA-MAT</title>
    @vite(['resources/css/database-ta-show.css', 'resources/js/app.js'])
</head>
<body>
<header class="topbar">
    <div class="brand"><x-unm-logo class="brand-logo" /><span>SIMTA</span></div>
    <nav>
        <a href="{{ route('landing') }}">Beranda</a>
        <a href="{{ route('database-ta') }}">DELTA-MAT</a>
        @auth
            @if (auth()->user()->role === 'admin')
                <a href="{{ route('admin.database-ta') }}">Kelola DELTA-MAT</a>
            @endif
        @else
            <a href="{{ route('admin.login') }}">Login Admin</a>
        @endauth
    </nav>
</header>

<main class="page">
    <a class="back" href="{{ route('database-ta') }}">Kembali ke DELTA-MAT</a>

    <section class="hero">
        <h1>{{ $record->title }}</h1>
        <p class="meta">{{ $record->student_name }} - {{ $record->nim }} @if($record->submission_date) | {{ $record->submission_date }} @endif</p>
    </section>

    <div class="grid">
        <aside class="panel">
            <h2>Detail Data</h2>
            <div class="detail-row">
                <span class="label">Mahasiswa</span>
                <span class="value">{{ $record->student_name }}</span>
            </div>
            <div class="detail-row">
                <span class="label">NIM</span>
                <span class="value">{{ $record->nim }}</span>
            </div>
            <div class="detail-row">
                <span class="label">Tanggal Pengajuan</span>
                <span class="value">{{ $record->submission_date ?: '-' }}</span>
            </div>
            <div class="detail-row">
                <span class="label">Email</span>
                <span class="value">{{ $record->email ?: '-' }}</span>
            </div>
            <div class="detail-row">
                <span class="label">No. WA</span>
                <span class="value">{{ $record->phone ?: '-' }}</span>
            </div>
            <div class="detail-row">
                <span class="label">Pembimbing 1</span>
                <span class="value">{{ $record->supervisor_1 ?: '-' }} @if($record->supervisor_1_nip)<br>{{ $record->supervisor_1_nip }} @endif</span>
            </div>
            <div class="detail-row">
                <span class="label">Pembimbing 2</span>
                <span class="value">{{ $record->supervisor_2 ?: '-' }} @if($record->supervisor_2_nip)<br>{{ $record->supervisor_2_nip }} @endif</span>
            </div>
        </aside>

        <section class="panel">
            <h2>Dokumen PDF</h2>
            @if ($record->document_url)
                <div class="document-actions">
                    <a class="button" href="{{ $record->document_url }}" target="_blank" rel="noopener">Buka dokumen asli</a>
                    <a class="button secondary" href="{{ $previewUrl }}" target="_blank" rel="noopener">Buka preview</a>
                </div>
                <iframe class="pdf-frame" src="{{ $previewUrl }}" title="Preview dokumen {{ $record->student_name }}"></iframe>
            @else
                <div class="empty">Dokumen belum tersedia untuk data ini.</div>
            @endif
        </section>
    </div>
</main>
</body>
</html>
