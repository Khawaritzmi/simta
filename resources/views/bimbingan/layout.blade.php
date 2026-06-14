<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title }} | Sistem Informasi Manajemen Tugas Akhir</title>
    @vite(['resources/css/dosen-layout.css', 'resources/js/app.js'])
</head>
<body>
<div class="app">
    <aside class="sidebar">
        <div class="brand">
            <x-unm-logo class="brand-logo" />
            <span class="hamburger">=</span>
        </div>
        <section class="identity">
            <div>
                @if ($profilePhotoUrl)
                    <img class="avatar photo-avatar" src="{{ $profilePhotoUrl }}" alt="Foto profil {{ $lecturer->name }}">
                @else
                    <div class="avatar">{{ mb_substr($lecturer->name, 0, 1) }}</div>
                @endif
                <h2>{{ $lecturer->name }}</h2>
                <p>Dosen</p>
            </div>
        </section>
        <nav class="nav">
            <a class="{{ $activeMenu === 'dashboard' ? 'active' : '' }}" href="{{ route('dashboard') }}"><span class="icon">H</span><span>Home</span></a>
            <a class="{{ $activeMenu === 'profile' ? 'active' : '' }}" href="{{ route('profile') }}"><span class="icon">P</span><span>Profil</span></a>
            <div class="nav-section">
                <span class="nav-title">Tugas Akhir</span>
                <a class="{{ $activeMenu === 'ta.requests' ? 'active' : '' }}" href="{{ route('guidance-requests.index') }}"><span class="icon">T1</span><span>List Pengajuan TA</span></a>
                <a class="{{ $activeMenu === 'ta.mine' ? 'active' : '' }}" href="{{ route('guidance.mine') }}"><span class="icon">T2</span><span>Bimbingan TA Saya</span></a>
            </div>
            <a class="{{ $activeMenu === 'pa' ? 'active' : '' }}" href="{{ route('pa.dashboard') }}"><span class="icon">PA</span><span>Bimbingan PA</span></a>
            <a class="{{ $activeMenu === 'seminars' ? 'active' : '' }}" href="{{ route('seminars') }}"><span class="icon">S</span><span>Seminar / Ujian</span></a>
            <a class="{{ $activeMenu === 'repository' ? 'active' : '' }}" href="{{ route('repository') }}"><span class="icon">R</span><span>Repository</span></a>
            <a class="{{ $activeMenu === 'qa' ? 'active' : '' }}" href="{{ route('qa') }}"><span class="icon">Q</span><span>Q & A</span></a>
            <a class="{{ $activeMenu === 'manuals' ? 'active' : '' }}" href="{{ route('manuals') }}"><span class="icon">M</span><span>Manual Aplikasi</span></a>
        </nav>
    </aside>
    <main class="main">
        <header class="topbar">
            <strong>SISTEM INFORMASI MANAJEMEN TUGAS AKHIR</strong>
            <div class="top-actions">
                <span class="user-chip">Dosen: {{ $lecturer->name }}</span>
                <a class="account-link" href="{{ route('dosen.export-report') }}" download>Export CSV</a>
                <a class="account-link" href="{{ route('password.edit') }}">Ubah Password</a>
                <form class="logout-form" method="post" action="{{ route('logout') }}">
                    @csrf
                    <button class="logout-button" type="submit">Logout</button>
                </form>
            </div>
        </header>
        <section class="content">
            <h1>{{ $title }}</h1>
            @if (session('status'))
                <div class="flash">{{ session('status') }}</div>
            @endif
            @if ($errors->any())
                <div class="errors">{{ $errors->first() }}</div>
            @endif
            @yield('content')
        </section>
        <footer class="footer">
            <span>Copyright 2026 Sistem Informasi Manajemen Tugas Akhir.</span>
            <span>Univeristas Negeri Makassar</span>
        </footer>
    </main>
</div>
@stack('scripts')
</body>
</html>
