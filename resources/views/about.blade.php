<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>About | Sistem Informasi Manajemen Tugas Akhir</title>
    @vite(['resources/css/landing.css', 'resources/js/app.js'])
</head>
<body>
@php
    $currentUser = auth()->user();
    $currentRoleLabel = match ($currentUser?->role) {
        'admin' => 'Admin',
        'mahasiswa' => 'Mahasiswa',
        'dosen' => 'Dosen',
        default => null,
    };
    $portalUrl = $currentUser ? route('portal') : null;
@endphp
<header class="topbar">
    <a class="brand" href="{{ route('landing') }}" aria-label="SIMTA">
        <x-unm-logo class="brand-logo" />
        <span>SIMTA</span>
    </a>
    <nav class="nav" aria-label="Navigasi utama">
        <a href="{{ route('landing') }}#layanan">Layanan</a>
        <a href="{{ route('landing') }}#akses">Akses</a>
        <a href="{{ route('landing') }}#deltamat">DELTA-MAT</a>
        <a class="active" href="{{ route('about') }}">About</a>
    </nav>
    <div class="top-actions">
        @auth
            <span class="session-pill">{{ $currentRoleLabel }}: {{ $currentUser->name }}</span>
            <a class="button" href="{{ $portalUrl }}">Buka Portal</a>
            <a class="button secondary" href="{{ route('password.edit') }}">Ubah Password</a>
            <form method="post" action="{{ route('logout') }}">
                @csrf
                <button class="button secondary" type="submit">Logout</button>
            </form>
        @else
            <a class="button secondary" href="{{ route('mahasiswa.login') }}">Login Mahasiswa</a>
            <a class="button" href="{{ route('dosen.login') }}">Login Dosen</a>
        @endauth
    </div>
</header>

<main>
    <section class="about-hero">
        <div class="container about-grid">
            <aside class="about-profile-card">
                <div class="about-avatar">K</div>
                <span class="feature-label">Pembuat Web App</span>
                <h1>Khawaritzmi Abdallah Ahmad, S.Si., M.Eng</h1>
                <p>Dosen Universitas Negeri Makassar dengan bidang keahlian Machine Learning dan pengembangan sistem informasi akademik.</p>
            </aside>

            <section class="about-content-card">
                <span class="eyebrow">Profil Pengembang</span>
                <h2>Pengembang SIMTA dan DELTA-MAT</h2>
                <p>
                    Web app ini dikembangkan untuk membantu Jurusan Matematika FMIPA UNM dalam mengelola proses tugas akhir, bimbingan akademik, repository, dan pencarian referensi judul tugas akhir melalui satu sistem yang lebih terarah.
                </p>
                <div class="about-facts">
                    <div>
                        <strong>Institusi</strong>
                        <span>Universitas Negeri Makassar</span>
                    </div>
                    <div>
                        <strong>Fokus Sistem</strong>
                        <span>Bimbingan TA, Bimbingan PA, dan DELTA-MAT</span>
                    </div>
                    <div>
                        <strong>Keahlian</strong>
                        <span>Machine Learning dan aplikasi web akademik</span>
                    </div>
                    <div>
                        <strong>Kontak</strong>
                        <span>khawaritzmi.abdallah@unm.ac.id</span>
                    </div>
                </div>
            </section>
        </div>
    </section>

    <section class="section about-section">
        <div class="container">
            <div class="section-header">
                <h2>Tujuan pengembangan aplikasi</h2>
                <p>SIMTA dibuat agar proses akademik yang sebelumnya tersebar dapat dipantau melalui alur yang lebih jelas.</p>
            </div>
            <div class="feature-grid">
                <article class="feature-card">
                    <img class="feature-emblem" src="{{ route('project.asset', ['file' => 'TA_logo.png']) }}" alt="Emblem Bimbingan Tugas Akhir">
                    <div>
                        <span class="feature-label">Bimbingan TA</span>
                        <h3>Alur tugas akhir lebih terpantau</h3>
                        <p>Mahasiswa dapat mengajukan pembimbing dan dosen dapat memantau daftar bimbingan serta status proses tugas akhir.</p>
                    </div>
                </article>
                <article class="feature-card">
                    <img class="feature-emblem" src="{{ route('project.asset', ['file' => 'PA_logo.png']) }}" alt="Emblem Bimbingan PA">
                    <div>
                        <span class="feature-label">Bimbingan PA</span>
                        <h3>Konsultasi akademik terdokumentasi</h3>
                        <p>Catatan akademik mahasiswa dapat dikelola agar riwayat konsultasi dan perkembangan studi lebih mudah ditelusuri.</p>
                    </div>
                </article>
                <article class="feature-card">
                    <img class="feature-emblem" src="{{ route('project.asset', ['file' => 'DELTAMAT_logo.png']) }}" alt="Emblem DELTA-MAT">
                    <div>
                        <span class="feature-label">DELTA-MAT</span>
                        <h3>Referensi judul lebih cepat ditemukan</h3>
                        <p>Data judul tugas akhir dapat dicari dan dibandingkan untuk membantu mengurangi duplikasi topik penelitian.</p>
                    </div>
                </article>
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
