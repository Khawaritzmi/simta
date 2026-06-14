<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sistem Informasi Manajemen Tugas Akhir</title>
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
        <a href="#layanan">Layanan</a>
        <a href="#akses">Akses</a>
        <a href="#deltamat">DELTA-MAT</a>
        <a href="{{ route('about') }}">About</a>
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
    <section class="hero-banner">
        <div class="hero-content">
            <span class="eyebrow">Sistem Informasi Manajemen Tugas Akhir</span>
            <h1>SIMTA untuk layanan tugas akhir Jurusan Matematika</h1>
            <p class="lead">Satu pintu untuk bimbingan tugas akhir, bimbingan akademik, pengelolaan repository, dan pencarian referensi judul melalui DELTA-MAT.</p>
            <div class="hero-actions">
                <a class="button orange" href="{{ route('database-ta') }}">Buka DELTA-MAT</a>
                <a class="button secondary" href="#akses">Pilih Akses Pengguna</a>
            </div>
            <!-- <div class="hero-metrics" aria-label="Ringkasan layanan SIMTA">
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
            </div> -->
        </div>
    </section>

    <section class="section compact" aria-label="Keunggulan sistem">
        <div class="container">
            <div class="quick-actions">
                <article class="quick-card">
                    <div>
                        <h2>Satu Akun, Banyak Akses</h2>
                        <p>Pengguna cukup masuk satu kali, lalu diarahkan ke halaman sesuai perannya sebagai mahasiswa, dosen, atau admin.</p>
                    </div>
                    <span class="quick-link">Alur akses lebih ringkas</span>
                </article>
                <article class="quick-card">
                    <div>
                        <h2>TA dan PA Terpusat</h2>
                        <p>Bimbingan tugas akhir, bimbingan akademik, pengajuan, repository, dan komunikasi akademik tersedia dalam satu sistem.</p>
                    </div>
                    <span class="quick-link">Tidak perlu berpindah aplikasi</span>
                </article>
                <article class="quick-card">
                    <div>
                        <h2>Status Mudah Dipantau</h2>
                        <p>Mahasiswa, dosen, dan admin dapat melihat perkembangan pengajuan TA, persetujuan, progress bimbingan, dan jadwal seminar.</p>
                    </div>
                    <span class="quick-link">Monitoring lebih transparan</span>
                </article>
                <article class="quick-card">
                    <div>
                        <h2>Referensi Judul Lebih Cepat</h2>
                        <p>DELTA-MAT membantu menelusuri judul tugas akhir, melihat kemiripan topik, dan membuka data repository yang sudah tersedia.</p>
                    </div>
                    <span class="quick-link">Mengurangi risiko duplikasi topik</span>
                </article>
            </div>
        </div>
    </section>

    <section class="section" id="layanan">
        <div class="container">
            <div class="section-header">
                <h2>Layanan utama yang langsung mengikuti kebutuhan tugas akhir</h2>
            </div>
            <div class="feature-grid">
                <article class="feature-card primary">
                    <img class="feature-emblem" src="{{ route('project.asset', ['file' => 'TA_logo.png']) }}" alt="Emblem Bimbingan Tugas Akhir">
                    <div>
                        <span class="feature-label">Bimbingan Tugas Akhir</span>
                        <h3>Koordinasi bimbingan sampai repository</h3>
                        <p>Dosen dan mahasiswa dapat mengelola proses bimbingan, persetujuan, seminar, unggah repository, dan tanya jawab dalam alur yang sama.</p>
                    </div>
                </article>
                <article class="feature-card">
                    <img class="feature-emblem" src="{{ route('project.asset', ['file' => 'PA_logo.png']) }}" alt="Emblem Bimbingan PA">
                    <div>
                        <span class="feature-label">Bimbingan PA</span>
                        <h3>Pendampingan akademik terarsip</h3>
                        <ul>
                            <li>Catatan konsultasi akademik mahasiswa.</li>
                            <li>Riwayat validasi dan perkembangan studi.</li>
                            <li>Akses dosen PA dan mahasiswa.</li>
                        </ul>
                    </div>
                </article>
                <article class="feature-card">
                    <img class="feature-emblem" src="{{ route('project.asset', ['file' => 'DELTAMAT_logo.png']) }}" alt="Emblem DELTA-MAT">
                    <div>
                        <span class="feature-label">DELTA-MAT</span>
                        <h3>Database judul dan dokumen TA</h3>
                        <ul>
                            <li>Pencarian judul tugas akhir tersimpan.</li>
                            <li>Indikasi kemiripan dan rekomendasi artikel.</li>
                            <li>Halaman detail dengan tautan dokumen PDF.</li>
                        </ul>
                    </div>
                </article>
            </div>
        </div>
    </section>

    <section class="section access-band" id="akses">
        <div class="container">
            <div class="section-header">
                <h2>Akses sesuai peran pengguna</h2>
            </div>
            <div class="access-grid">
                <article class="access-card">
                    <h3>Dosen</h3>
                    <p>Masuk ke profil dosen untuk mengakses Bimbingan TA dan Bimbingan PA.</p>
                    <div class="access-actions">
                        @auth
                            <a class="button" href="{{ route('portal') }}">Buka Portal Aktif</a>
                        @else
                            <a class="button" href="{{ route('dosen.login') }}">Login Dosen</a>
                            <a class="button secondary" href="{{ route('dosen.register') }}">Regis Dosen</a>
                        @endauth
                    </div>
                </article>
                <article class="access-card">
                    <h3>Mahasiswa</h3>
                    <p>Akses bimbingan, repository, tanya jawab, dan konsultasi akademik melalui portal mahasiswa.</p>
                    <div class="access-actions">
                        @auth
                            <a class="button green" href="{{ route('portal') }}">Buka Portal Aktif</a>
                        @else
                            <a class="button green" href="{{ route('mahasiswa.login') }}">Login Mahasiswa</a>
                            <a class="button secondary" href="{{ route('mahasiswa.register') }}">Regis Mahasiswa</a>
                        @endauth
                    </div>
                </article>
                <article class="access-card">
                    <h3>Admin</h3>
                    <p>Kelola data DELTA-MAT dan data administrasi bimbingan PA dari halaman admin.</p>
                    <div class="access-actions">
                        @auth
                            <a class="button orange" href="{{ route('portal') }}">Buka Portal Aktif</a>
                        @else
                            <a class="button orange" href="{{ route('admin.login') }}">Login Admin</a>
                        @endauth
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
