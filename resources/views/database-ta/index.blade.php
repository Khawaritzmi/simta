<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>DELTA-MAT | Sistem Informasi Manajemen Tugas Akhir</title>
    @vite(['resources/css/database-ta-index.css', 'resources/js/app.js'])
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
                <progress class="similarity-meter" max="100" value="{{ min(max($highest, 0), 100) }}">{{ number_format($highest, 2) }}%</progress>
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
