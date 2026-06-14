<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sistem Informasi Manajemen Tugas Akhir</title>
    <style>
        :root {
            --blue: #1f91e8;
            --blue-dark: #176fb2;
            --green: #16855b;
            --orange: #c56a14;
            --ink: #252b36;
            --muted: #68707d;
            --line: #dfe7ef;
            --page: #f5f7fc;
            --panel: #ffffff;
            --soft-blue: #eef6ff;
            --soft-green: #ecf8f2;
            --soft-orange: #fff4e8;
        }
        * { box-sizing: border-box; }
        html { scroll-behavior: smooth; }
        body {
            margin: 0;
            background: var(--page);
            color: var(--ink);
            font-family: Inter, ui-sans-serif, system-ui, -apple-system, "Segoe UI", Arial, sans-serif;
        }
        a { color: inherit; }
        .topbar {
            position: sticky;
            top: 0;
            z-index: 10;
            min-height: 74px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 24px;
            padding: 14px 72px;
            background: rgba(255, 255, 255, .96);
            border-bottom: 1px solid var(--line);
        }
        .brand {
            display: flex;
            align-items: center;
            gap: 12px;
            font-weight: 900;
            font-size: 22px;
            letter-spacing: 0;
            text-decoration: none;
        }
        .brand-logo { width: 44px; height: 44px; object-fit: contain; }
        .nav {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 24px;
            color: var(--muted);
            font-weight: 800;
            font-size: 14px;
        }
        .nav a { text-decoration: none; }
        .nav a:hover { color: var(--blue); }
        .top-actions { display: flex; align-items: center; gap: 10px; }
        .button {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-height: 42px;
            padding: 11px 16px;
            border: 0;
            border-radius: 6px;
            background: var(--blue);
            color: #ffffff;
            font-weight: 900;
            font-size: 14px;
            text-decoration: none;
            white-space: nowrap;
        }
        .button:hover { background: var(--blue-dark); }
        .button.secondary { background: #edf2f7; color: var(--ink); }
        .button.secondary:hover { background: #e2e8f0; }
        .button.green { background: var(--green); }
        .button.green:hover { background: #106b48; }
        .button.orange { background: var(--orange); }
        .button.orange:hover { background: #a65710; }
        .hero-banner {
            min-height: 560px;
            display: flex;
            align-items: center;
            padding: 86px 72px 118px;
            color: #ffffff;
            background-image:
                linear-gradient(90deg, rgba(16, 31, 49, .88) 0%, rgba(20, 53, 84, .76) 43%, rgba(20, 53, 84, .24) 100%),
                url('{{ url('/assets/fmipa.png') }}');
            background-size: cover;
            background-position: center center;
            border-bottom: 1px solid rgba(255, 255, 255, .18);
        }
        .hero-content { max-width: 680px; }
        .eyebrow {
            display: inline-flex;
            align-items: center;
            min-height: 32px;
            padding: 7px 12px;
            border-radius: 999px;
            background: rgba(255, 255, 255, .14);
            border: 1px solid rgba(255, 255, 255, .28);
            font-weight: 900;
            font-size: 13px;
        }
        h1 {
            margin: 20px 0 18px;
            max-width: 620px;
            font-size: 64px;
            line-height: 1.02;
            letter-spacing: 0;
        }
        .lead {
            margin: 0;
            max-width: 620px;
            color: rgba(255, 255, 255, .86);
            line-height: 1.75;
            font-size: 18px;
        }
        .hero-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
            margin-top: 28px;
        }
        .hero-actions .button.secondary {
            background: rgba(255, 255, 255, .92);
            color: var(--ink);
        }
        .hero-metrics {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 14px;
            max-width: 780px;
            margin-top: 34px;
        }
        .metric {
            min-height: 94px;
            padding: 18px;
            border-radius: 8px;
            background: rgba(255, 255, 255, .12);
            border: 1px solid rgba(255, 255, 255, .24);
        }
        .metric strong { display: block; font-size: 30px; line-height: 1; }
        .metric span { display: block; margin-top: 9px; color: rgba(255, 255, 255, .8); font-size: 13px; font-weight: 800; }
        .section {
            padding: 70px 72px;
        }
        .section.compact {
            padding-top: 0;
        }
        .container {
            width: min(1160px, 100%);
            margin: 0 auto;
        }
        .quick-actions {
            position: relative;
            z-index: 2;
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 16px;
            margin-top: -54px;
        }
        .quick-card,
        .feature-card,
        .access-card {
            background: var(--panel);
            border: 1px solid var(--line);
            border-radius: 8px;
            box-shadow: 0 18px 45px rgba(31, 49, 73, .08);
        }
        .quick-card {
            min-height: 148px;
            padding: 22px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            text-decoration: none;
        }
        .quick-card h2 {
            margin: 0;
            font-size: 19px;
            line-height: 1.25;
        }
        .quick-card p {
            margin: 12px 0 0;
            color: var(--muted);
            line-height: 1.55;
            font-size: 14px;
        }
        .quick-link {
            margin-top: 18px;
            color: var(--blue);
            font-size: 13px;
            font-weight: 900;
        }
        .section-header {
            display: flex;
            justify-content: space-between;
            gap: 28px;
            margin-bottom: 28px;
        }
        .section-header h2 {
            margin: 0;
            max-width: 620px;
            font-size: 34px;
            line-height: 1.18;
        }
        .section-header p {
            margin: 0;
            max-width: 430px;
            color: var(--muted);
            line-height: 1.7;
        }
        .feature-grid {
            display: grid;
            grid-template-columns: 1.1fr 1fr 1fr;
            gap: 18px;
        }
        .feature-card {
            min-height: 250px;
            padding: 26px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }
        .feature-card.primary {
            background: var(--blue);
            color: #ffffff;
            border-color: var(--blue);
        }
        .feature-card.primary p,
        .feature-card.primary li { color: rgba(255, 255, 255, .84); }
        .feature-label {
            display: inline-flex;
            width: max-content;
            padding: 7px 10px;
            border-radius: 999px;
            background: var(--soft-blue);
            color: var(--blue-dark);
            font-size: 12px;
            font-weight: 900;
        }
        .feature-card.primary .feature-label {
            background: rgba(255, 255, 255, .18);
            color: #ffffff;
        }
        .feature-card h3 {
            margin: 18px 0 10px;
            font-size: 24px;
            line-height: 1.2;
        }
        .feature-card p,
        .feature-card li {
            color: var(--muted);
            line-height: 1.65;
        }
        .feature-card ul {
            display: grid;
            gap: 8px;
            margin: 16px 0 0;
            padding-left: 18px;
        }
        .access-band {
            background: #ffffff;
            border-top: 1px solid var(--line);
            border-bottom: 1px solid var(--line);
        }
        .access-grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 18px;
        }
        .access-card {
            padding: 24px;
            box-shadow: none;
        }
        .access-card h3 {
            margin: 0 0 10px;
            font-size: 22px;
        }
        .access-card p {
            margin: 0 0 20px;
            color: var(--muted);
            line-height: 1.65;
        }
        .access-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
        }
        .delta-panel {
            display: grid;
            grid-template-columns: .85fr 1.15fr;
            gap: 24px;
            align-items: stretch;
        }
        .delta-image {
            min-height: 320px;
            border-radius: 8px;
            border: 1px solid var(--line);
            background:
                linear-gradient(180deg, rgba(245, 247, 252, .15), rgba(245, 247, 252, .84)),
                url('{{ url('/assets/jurmat.png') }}');
            background-size: contain;
            background-position: center;
            background-repeat: no-repeat;
            background-color: #ffffff;
        }
        .delta-content {
            padding: 34px;
            border-radius: 8px;
            background: var(--ink);
            color: #ffffff;
        }
        .delta-content h2 {
            margin: 0 0 14px;
            font-size: 34px;
            line-height: 1.16;
        }
        .delta-content p {
            margin: 0;
            color: rgba(255, 255, 255, .78);
            line-height: 1.75;
        }
        .delta-steps {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 12px;
            margin: 24px 0;
        }
        .delta-step {
            min-height: 96px;
            padding: 16px;
            border-radius: 8px;
            background: rgba(255, 255, 255, .08);
            border: 1px solid rgba(255, 255, 255, .14);
        }
        .delta-step strong { display: block; margin-bottom: 8px; font-size: 18px; }
        .delta-step span { color: rgba(255, 255, 255, .72); font-size: 13px; line-height: 1.5; }
        footer {
            padding: 30px 72px;
            color: var(--muted);
            border-top: 1px solid var(--line);
            background: #ffffff;
            font-size: 14px;
        }
        footer .container {
            display: flex;
            justify-content: space-between;
            gap: 16px;
        }
        @media (max-width: 1080px) {
            .topbar { padding: 14px 28px; }
            .nav { display: none; }
            .hero-banner,
            .section,
            footer { padding-left: 28px; padding-right: 28px; }
            .quick-actions,
            .feature-grid,
            .access-grid,
            .delta-panel { grid-template-columns: 1fr 1fr; }
            .section-header { display: block; }
            .section-header p { margin-top: 12px; }
        }
        @media (max-width: 720px) {
            .topbar { align-items: flex-start; flex-direction: column; }
            .top-actions { width: 100%; flex-wrap: wrap; }
            .top-actions .button { flex: 1 1 auto; }
            .hero-banner {
                min-height: 620px;
                align-items: flex-end;
                padding-top: 70px;
                padding-bottom: 92px;
                background-position: center top;
            }
            h1 { font-size: 42px; }
            .lead { font-size: 16px; }
            .hero-metrics,
            .quick-actions,
            .feature-grid,
            .access-grid, 
            .delta-panel,
            .delta-steps { grid-template-columns: 1fr; }
            .section { padding-top: 54px; padding-bottom: 54px; }
            .section-header h2,
            .delta-content h2 { font-size: 28px; }
            footer .container { flex-direction: column; }
        }
    </style>
</head>
<body>
<header class="topbar">
    <a class="brand" href="{{ route('landing') }}" aria-label="SIMTA">
        <x-unm-logo class="brand-logo" />
        <span>SIMTA</span>
    </a>
    <nav class="nav" aria-label="Navigasi utama">
        <a href="#layanan">Layanan</a>
        <a href="#akses">Akses</a>
        <a href="#deltamat">DELTA-MAT</a>
    </nav>
    <div class="top-actions">
        <a class="button secondary" href="{{ route('mahasiswa.login') }}">Login Mahasiswa</a>
        <a class="button" href="{{ route('dosen.login') }}">Login Dosen</a>
    </div>
</header>

<main>
    <section class="hero-banner">
        <div class="hero-content">
            <span class="eyebrow">Sistem Informasi Manajemen Tugas Akhir</span>
            <h1>SIMTA untuk layanan tugas akhir Jurusan Matematika</h1>
            <p class="lead">Satu pintu untuk bimbingan tugas akhir, bimbingan akademik, pengelolaan repository, dan pencarian referensi judul melalui DELTA-MAT.</p>
            <div class="hero-actions">
                <a class="button orange" href="{{ route('database-ta') }}">Buka DELTA-MAT</a>
                <a class="button secondary" href="#akses">Pilih Akses Pengguna</a>
            </div>
            <div class="hero-metrics" aria-label="Ringkasan layanan SIMTA">
                <div class="metric">
                    <strong>3</strong>
                    <span>Modul utama layanan</span>
                </div>
                <div class="metric">
                    <strong>TA</strong>
                    <span>Alur bimbingan digital</span>
                </div>
                <div class="metric">
                    <strong>PDF</strong>
                    <span>Dokumen repository DELTA-MAT</span>
                </div>
            </div>
        </div>
    </section>

    <section class="section compact" aria-label="Akses cepat">
        <div class="container">
            <div class="quick-actions">
                <a class="quick-card" href="{{ route('dosen.login') }}">
                    <div>
                        <h2>Bimbingan TA</h2>
                        <p>Monitoring bimbingan, persetujuan, seminar, repository, dan tanya jawab.</p>
                    </div>
                    <span class="quick-link">Masuk sebagai dosen</span>
                </a>
                <a class="quick-card" href="{{ route('mahasiswa.login') }}">
                    <div>
                        <h2>Portal Mahasiswa</h2>
                        <p>Akses profil, bimbingan, repository, dan komunikasi akademik.</p>
                    </div>
                    <span class="quick-link">Masuk sebagai mahasiswa</span>
                </a>
                <a class="quick-card" href="{{ route('dosen.login', ['next' => 'pa.dosen.dashboard']) }}">
                    <div>
                        <h2>Bimbingan PA</h2>
                        <p>Konsultasi akademik, catatan semester, dan pengelolaan mahasiswa PA.</p>
                    </div>
                    <span class="quick-link">Buka modul PA</span>
                </a>
                <a class="quick-card" href="{{ route('database-ta') }}">
                    <div>
                        <h2>DELTA-MAT</h2>
                        <p>Telusuri judul, cek kemiripan, dan buka dokumen tugas akhir.</p>
                    </div>
                    <span class="quick-link">Cari referensi TA</span>
                </a>
            </div>
        </div>
    </section>

    <section class="section" id="layanan">
        <div class="container">
            <div class="section-header">
                <h2>Layanan utama yang langsung mengikuti kebutuhan tugas akhir</h2>
                <p>Layout dibuat seperti portal layanan: informasi utama terlihat di atas, akses cepat berada dekat banner, dan setiap modul punya jalur masuk yang jelas.</p>
            </div>
            <div class="feature-grid">
                <article class="feature-card primary">
                    <div>
                        <span class="feature-label">Bimbingan Tugas Akhir</span>
                        <h3>Koordinasi bimbingan sampai repository</h3>
                        <p>Dosen dan mahasiswa dapat mengelola proses bimbingan, persetujuan, seminar, unggah repository, dan tanya jawab dalam alur yang sama.</p>
                    </div>
                    <a class="button secondary" href="{{ route('dosen.login') }}">Login Bimbingan TA</a>
                </article>
                <article class="feature-card">
                    <div>
                        <span class="feature-label">Bimbingan PA</span>
                        <h3>Pendampingan akademik terarsip</h3>
                        <ul>
                            <li>Catatan konsultasi akademik mahasiswa.</li>
                            <li>Riwayat validasi dan perkembangan studi.</li>
                            <li>Akses dosen PA dan mahasiswa.</li>
                        </ul>
                    </div>
                    <a class="button green" href="{{ route('mahasiswa.login', ['next' => 'pa.mahasiswa.dashboard']) }}">Masuk PA</a>
                </article>
                <article class="feature-card">
                    <div>
                        <span class="feature-label">DELTA-MAT</span>
                        <h3>Database judul dan dokumen TA</h3>
                        <ul>
                            <li>Pencarian judul tugas akhir tersimpan.</li>
                            <li>Indikasi kemiripan dan rekomendasi artikel.</li>
                            <li>Halaman detail dengan tautan dokumen PDF.</li>
                        </ul>
                    </div>
                    <a class="button orange" href="{{ route('database-ta') }}">Buka DELTA-MAT</a>
                </article>
            </div>
        </div>
    </section>

    <section class="section access-band" id="akses">
        <div class="container">
            <div class="section-header">
                <h2>Akses sesuai peran pengguna</h2>
                <p>Setiap pengguna diarahkan ke fungsi yang relevan agar halaman awal tetap ringkas dan mudah dipindai.</p>
            </div>
            <div class="access-grid">
                <article class="access-card">
                    <h3>Dosen</h3>
                    <p>Masuk ke dashboard bimbingan TA atau dashboard bimbingan PA sesuai kebutuhan layanan.</p>
                    <div class="access-actions">
                        <a class="button" href="{{ route('dosen.login') }}">Login Dosen</a>
                        <a class="button secondary" href="{{ route('dosen.register') }}">Regis Dosen</a>
                    </div>
                </article>
                <article class="access-card">
                    <h3>Mahasiswa</h3>
                    <p>Akses bimbingan, repository, tanya jawab, dan konsultasi akademik melalui portal mahasiswa.</p>
                    <div class="access-actions">
                        <a class="button green" href="{{ route('mahasiswa.login') }}">Login Mahasiswa</a>
                        <a class="button secondary" href="{{ route('mahasiswa.register') }}">Regis Mahasiswa</a>
                    </div>
                </article>
                <article class="access-card">
                    <h3>Admin</h3>
                    <p>Kelola data DELTA-MAT dan data administrasi bimbingan PA dari halaman admin.</p>
                    <div class="access-actions">
                        <a class="button orange" href="{{ route('admin.login') }}">Login Admin</a>
                    </div>
                </article>
            </div>
        </div>
    </section>

    <section class="section" id="deltamat">
        <div class="container">
            <div class="delta-panel">
                <div class="delta-image" aria-hidden="true"></div>
                <div class="delta-content">
                    <h2>DELTA-MAT sebagai pusat database tugas akhir</h2>
                    <p>Mahasiswa dapat menelusuri topik yang sudah tersimpan, membaca metadata pembimbing, membuka halaman detail, dan mengakses dokumen PDF jika tautan dokumen tersedia.</p>
                    <div class="delta-steps">
                        <div class="delta-step">
                            <strong>1</strong>
                            <span>Masukkan judul atau kata kunci penelitian.</span>
                        </div>
                        <div class="delta-step">
                            <strong>2</strong>
                            <span>Lihat skor kemiripan dan daftar rekomendasi.</span>
                        </div>
                        <div class="delta-step">
                            <strong>3</strong>
                            <span>Buka detail data dan dokumen repository.</span>
                        </div>
                    </div>
                    <a class="button orange" href="{{ route('database-ta') }}">Mulai Pencarian</a>
                </div>
            </div>
        </div>
    </section>
</main>

<footer>
    <div class="container">
        <span>SIMTA - Sistem Informasi Manajemen Tugas Akhir</span>
        <span>Jurusan Matematika FMIPA UNM Makassar</span>
    </div>
</footer>
</body>
</html>
