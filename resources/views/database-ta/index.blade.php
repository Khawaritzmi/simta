<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>DELTA-MAT | Sistem Informasi Manajemen Tugas Akhir</title>
    <style>
        :root {
            --blue: #1f91e8;
            --green: #17a86b;
            --yellow: #f4c430;
            --red: #ef4b4b;
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
            height: 78px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 min(6vw, 72px);
            background: white;
            border-bottom: 1px solid var(--line);
        }
        .brand { display: flex; align-items: center; gap: 14px; font-weight: 900; letter-spacing: .08em; font-size: 24px; }
        .brand-logo { width: 48px; height: 48px; object-fit: contain; }
        .topbar nav { display: flex; align-items: center; gap: 16px; }
        .topbar a { color: var(--blue); font-weight: 800; text-decoration: none; }
        .page { width: min(980px, calc(100vw - 32px)); margin: 28px auto 64px; }
        .title { text-align: center; margin: 12px 0 24px; }
        .title h1 { margin: 0; font-size: clamp(34px, 5vw, 52px); letter-spacing: .02em; }
        .title p { margin: 10px auto 0; color: var(--muted); max-width: 660px; line-height: 1.65; }
        .search {
            display: grid;
            grid-template-columns: 1fr 58px;
            gap: 0;
            margin-bottom: 24px;
            box-shadow: 0 10px 24px rgba(37, 43, 54, .08);
        }
        .search input {
            width: 100%;
            border: 1px solid var(--line);
            border-right: 0;
            border-radius: 8px 0 0 8px;
            padding: 17px 18px;
            font: inherit;
            background: white;
            color: var(--ink);
            text-transform: uppercase;
        }
        .search button {
            border: 0;
            border-radius: 0 8px 8px 0;
            background: var(--blue);
            color: white;
            font-size: 18px;
            font-weight: 900;
            cursor: pointer;
        }
        .summary-card, .article-card {
            background: white;
            border: 1px solid var(--line);
            border-radius: 8px;
            box-shadow: 0 8px 18px rgba(37, 43, 54, .06);
        }
        .summary-card { padding: 24px; margin-bottom: 28px; }
        .summary-grid { display: grid; grid-template-columns: 150px 1fr; gap: 22px; align-items: center; }
        .score { font-size: 44px; line-height: 1; font-weight: 900; letter-spacing: 0; }
        .metric-title { margin: 0 0 8px; font-weight: 900; }
        .bar { position: relative; height: 18px; display: grid; grid-template-columns: 30% 35% 35%; overflow: hidden; border-radius: 999px; }
        .bar span:nth-child(1) { background: var(--green); }
        .bar span:nth-child(2) { background: var(--yellow); }
        .bar span:nth-child(3) { background: var(--red); }
        .pointer { position: absolute; top: -2px; width: 0; height: 0; border-left: 8px solid transparent; border-right: 8px solid transparent; border-top: 12px solid #0f172a; transform: translateX(-8px); }
        .legend { display: grid; grid-template-columns: repeat(3, 1fr); margin-top: 8px; font-size: 12px; font-weight: 800; }
        .legend span:nth-child(2) { text-align: center; }
        .legend span:nth-child(3) { text-align: right; }
        .status-line { margin: 14px 0 0; color: var(--muted); }
        .status-line strong { color: #c08400; }
        .section-title { margin: 0 0 16px; font-size: 22px; }
        .list { display: grid; gap: 14px; }
        .article-card { padding: 20px; }
        .article-card h3 { margin: 0 0 8px; font-size: 18px; line-height: 1.45; text-transform: uppercase; }
        .article-card h3 a { color: var(--ink); text-decoration: none; }
        .article-card h3 a:hover { color: var(--blue); }
        .meta { margin: 0 0 10px; color: var(--muted); }
        .chips { display: flex; gap: 8px; flex-wrap: wrap; }
        .chip { display: inline-flex; align-items: center; min-height: 28px; border-radius: 4px; padding: 5px 9px; background: #eef2f7; font-size: 12px; font-weight: 800; }
        a.chip { color: var(--blue); text-decoration: none; }
        .chip.high { background: #dcfce7; color: #157347; }
        .chip.medium { background: #fff6db; color: #9a6700; }
        .chip.low { background: #f1f5f9; color: #64748b; }
        .empty { background: white; border: 1px solid var(--line); border-radius: 8px; padding: 22px; color: var(--muted); }
        @media (max-width: 700px) {
            .summary-grid { grid-template-columns: 1fr; }
            .score { font-size: 38px; }
            .topbar { padding: 0 16px; }
        }
    </style>
</head>
<body>
<header class="topbar">
    <div class="brand"><x-unm-logo class="brand-logo" /><span>SIMTA</span></div>
    <nav>
        <a href="{{ route('landing') }}">Beranda</a>
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
    <section class="title">
        <h1>DELTA-MAT</h1>
        <p>Telusuri judul tugas akhir dan lihat indikasi kemiripan terhadap data repository yang sudah tersimpan.</p>
    </section>

    <form class="search" method="get" action="{{ route('database-ta') }}">
        <input name="q" value="{{ $query }}" placeholder="Masukkan judul tugas akhir">
        <button type="submit">Cari</button>
    </form>

    <section class="summary-card">
        <p class="metric-title">Analisis Kemiripan Judul</p>
        <div class="summary-grid">
            <div class="score">{{ number_format($highest, 2) }}%</div>
            <div>
                <div class="bar">
                    <span></span>
                    <span></span>
                    <span></span>
                    <i class="pointer" style="left: {{ min(max($highest, 0), 100) }}%"></i>
                </div>
                <div class="legend">
                    <span>UNIK</span>
                    <span>WASPADA</span>
                    <span>DUPLIKASI</span>
                </div>
            </div>
        </div>
        <p class="status-line">Tingkat Kemiripan: <strong>{{ $status }}</strong> @if($query !== '')({{ number_format($highest, 2) }}%)@endif</p>
    </section>

    <h2 class="section-title">Artikel yang direkomendasikan</h2>

    <section class="list">
        @forelse ($recommendations as $item)
            @php
                $chipClass = $item->similarity >= 70 ? 'high' : ($item->similarity >= 30 ? 'medium' : 'low');
            @endphp
            <article class="article-card">
                <h3><a href="{{ route('database-ta.show', $item->id) }}">{{ $item->title }}</a></h3>
                <p class="meta">{{ $item->student_name }} - {{ $item->nim }} | Pembimbing: {{ $item->supervisor_1 ?: '-' }}</p>
                <div class="chips">
                    <span class="chip {{ $chipClass }}">{{ $item->match_label }}</span>
                    <span class="chip">{{ number_format($item->similarity, 2) }}%</span>
                    <span class="chip">{{ $item->submission_date ?: 'Tanpa tanggal' }}</span>
                    @if ($item->supervisor_2)
                        <span class="chip">Pembimbing 2: {{ $item->supervisor_2 }}</span>
                    @endif
                    @if ($item->document_url)
                        <a class="chip" href="{{ route('database-ta.show', $item->id) }}">Dokumen</a>
                    @endif
                    <a class="chip" href="{{ route('database-ta.show', $item->id) }}">Detail</a>
                </div>
            </article>
        @empty
            <div class="empty">Belum ada data tugas akhir di database.</div>
        @endforelse
    </section>
</main>
</body>
</html>
