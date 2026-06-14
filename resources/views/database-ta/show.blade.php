<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $record->title }} | DELTA-MAT</title>
    <style>
        :root {
            --blue: #1f91e8;
            --ink: #252b36;
            --muted: #68707d;
            --line: #dfe7ef;
            --page: #f5f7fc;
            --panel: #ffffff;
        }
        * { box-sizing: border-box; }
        body {
            margin: 0;
            background: var(--page);
            color: var(--ink);
            font-family: Inter, ui-sans-serif, system-ui, -apple-system, "Segoe UI", Arial, sans-serif;
        }
        .topbar {
            min-height: 78px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 18px;
            padding: 16px min(6vw, 72px);
            background: white;
            border-bottom: 1px solid var(--line);
        }
        .brand { display: flex; align-items: center; gap: 14px; font-weight: 900; letter-spacing: .08em; font-size: 24px; }
        .brand-logo { width: 48px; height: 48px; object-fit: contain; }
        .topbar nav { display: flex; align-items: center; gap: 16px; flex-wrap: wrap; }
        a { color: var(--blue); font-weight: 800; text-decoration: none; }
        .page { width: min(1180px, calc(100vw - 32px)); margin: 28px auto 64px; }
        .back { display: inline-flex; margin-bottom: 18px; }
        .hero, .panel {
            background: var(--panel);
            border: 1px solid var(--line);
            border-radius: 8px;
            box-shadow: 0 8px 18px rgba(37, 43, 54, .05);
        }
        .hero { padding: 28px; margin-bottom: 18px; }
        h1 { margin: 0 0 14px; font-size: clamp(28px, 4vw, 42px); line-height: 1.18; text-transform: uppercase; }
        .meta { margin: 0; color: var(--muted); line-height: 1.7; }
        .grid { display: grid; grid-template-columns: 360px 1fr; gap: 18px; align-items: start; }
        .panel { padding: 24px; }
        h2 { margin: 0 0 18px; font-size: 22px; }
        .detail-row { padding: 13px 0; border-bottom: 1px solid var(--line); }
        .detail-row:last-child { border-bottom: 0; }
        .label { display: block; margin-bottom: 5px; color: var(--muted); font-size: 13px; font-weight: 900; text-transform: uppercase; }
        .value { line-height: 1.55; font-weight: 700; }
        .document-actions { display: flex; align-items: center; gap: 10px; flex-wrap: wrap; margin-bottom: 16px; }
        .button {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-height: 38px;
            border-radius: 5px;
            padding: 9px 13px;
            background: var(--blue);
            color: white;
            font-weight: 900;
        }
        .button.secondary { background: #eef2f7; color: var(--ink); }
        .pdf-frame {
            width: 100%;
            height: min(72vh, 820px);
            min-height: 560px;
            border: 1px solid var(--line);
            border-radius: 8px;
            background: #f8fafc;
        }
        .empty {
            min-height: 260px;
            display: grid;
            place-items: center;
            border: 1px dashed var(--line);
            border-radius: 8px;
            color: var(--muted);
            text-align: center;
            padding: 24px;
        }
        @media (max-width: 920px) {
            .grid { grid-template-columns: 1fr; }
            .pdf-frame { height: 70vh; min-height: 420px; }
        }
    </style>
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
