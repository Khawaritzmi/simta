<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title }} | Mahasiswa</title>
    @vite(['resources/css/mahasiswa-layout.css', 'resources/js/app.js'])
</head>
<body>
<div class="app">
    <aside class="sidebar">
        <div class="brand"><x-unm-logo class="brand-logo" /><span>MAHASISWA</span></div>
        <section class="identity">
            <div>
                @if ($profilePhotoUrl)
                    <img class="avatar photo-avatar" src="{{ $profilePhotoUrl }}" alt="Foto profil {{ $student->name }}">
                @else
                    <div class="avatar">{{ mb_substr($student->name, 0, 1) }}</div>
                @endif
                <h2>{{ $student->name }}</h2>
                <p>{{ $student->nim }}</p>
            </div>
        </section>
        <nav class="nav">
            <a class="{{ $activeMenu === 'dashboard' ? 'active' : '' }}" href="{{ route('mahasiswa.dashboard') }}"><span>H</span><span>Home</span></a>
            <a class="{{ $activeMenu === 'profile' ? 'active' : '' }}" href="{{ route('mahasiswa.profile') }}"><span>P</span><span>Profil</span></a>
            <div class="nav-section">
                <span class="nav-title">Tugas Akhir</span>
                <a class="{{ $activeMenu === 'ta.request' ? 'active' : '' }}" href="{{ route('mahasiswa.guidance-requests.index') }}"><span>T1</span><span>Pengajuan TA</span></a>
                <a class="{{ $activeMenu === 'ta.mine' ? 'active' : '' }}" href="{{ route('mahasiswa.guidance.mine') }}"><span>T2</span><span>Tugas Akhir Saya</span></a>
            </div>
            <a class="{{ $activeMenu === 'pa' ? 'active' : '' }}" href="{{ route('mahasiswa.pa.dashboard') }}"><span>PA</span><span>Bimbingan PA</span></a>
            <a class="{{ $activeMenu === 'repository' ? 'active' : '' }}" href="{{ route('mahasiswa.repository') }}"><span>R</span><span>Repository</span></a>
            <a class="{{ $activeMenu === 'qa' ? 'active' : '' }}" href="{{ route('mahasiswa.qa') }}"><span>Q</span><span>Q & A</span></a>
        </nav>
    </aside>
    <main class="main">
        <header class="topbar">
            <strong>SIMTA MAHASISWA</strong>
            <div class="top-actions">
                <span class="user-chip">Mahasiswa: {{ $student->name }}</span>
                <a class="account-link" href="{{ route('password.edit') }}">Ubah Password</a>
                <form method="post" action="{{ route('logout') }}">
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
</body>
</html>
