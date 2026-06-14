<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title }} | Mahasiswa</title>
    <style>
        :root { --green:#16855b; --green-soft:#e9f8f1; --blue:#1f91e8; --ink:#252b36; --muted:#8b9098; --line:#dfe7ef; --page:#f5f7fc; --panel:#fff; --warning:#b7791f; }
        * { box-sizing: border-box; }
        body { margin: 0; background: var(--page); color: var(--ink); font-family: Inter, ui-sans-serif, system-ui, -apple-system, "Segoe UI", Arial, sans-serif; font-size: 16px; }
        a { color: inherit; text-decoration: none; }
        .app { min-height: 100vh; display: grid; grid-template-columns: 300px 1fr; }
        .sidebar { background: var(--panel); border-right: 1px solid var(--line); }
        .brand { height: 88px; display: flex; align-items: center; justify-content: space-between; padding: 0 42px; border-bottom: 1px solid var(--line); }
        .brand strong, .topbar strong { font-size: 26px; letter-spacing: .08em; }
        .brand-logo { width: 54px; height: 54px; object-fit: contain; }
        .identity { min-height: 230px; display: grid; place-items: center; padding: 28px 24px; text-align: center; border-bottom: 1px solid var(--line); }
        .avatar { width: 104px; height: 104px; border-radius: 50%; display: grid; place-items: center; background: #dff7ec; color: var(--green); font-size: 34px; font-weight: 900; border: 12px solid #dff7ec; }
        .identity h2 { max-width: 240px; margin: 20px 0 8px; font-size: 18px; line-height: 1.35; }
        .identity p { margin: 0; color: var(--muted); }
        .nav { padding: 28px 20px; display: grid; gap: 10px; }
        .nav a { display: grid; grid-template-columns: 34px 1fr; align-items: center; gap: 14px; min-height: 58px; padding: 0 18px; border-radius: 8px; font-weight: 800; }
        .nav a.active { background: var(--green); color: white; }
        .main { display: flex; flex-direction: column; min-width: 0; }
        .topbar { height: 88px; background: var(--panel); border-bottom: 1px solid var(--line); display: flex; align-items: center; justify-content: space-between; padding: 0 42px; }
        .logout-button { border: 0; border-radius: 5px; padding: 14px 22px; background: var(--green-soft); color: var(--green); font: inherit; font-weight: 900; cursor: pointer; }
        .content { flex: 1; padding: 34px 42px 72px; }
        .footer { background: white; border-top: 1px solid var(--line); display: flex; justify-content: space-between; gap: 18px; padding: 18px 42px; color: #919191; font-weight: 700; }
        h1 { margin: 0 0 34px; font-size: 34px; }
        h3 { margin: 0 0 22px; font-size: 22px; }
        .panel { background: white; border: 1px solid var(--line); padding: 32px; margin-bottom: 24px; }
        .table { width: 100%; border-collapse: collapse; }
        .table th, .table td { border-bottom: 1px solid var(--line); text-align: left; padding: 16px 14px; vertical-align: top; }
        .badge { display: inline-flex; min-height: 28px; align-items: center; padding: 4px 10px; border-radius: 999px; background: #eef2f7; font-weight: 900; font-size: 13px; }
        .badge.warning { background: #fff6db; color: var(--warning); }
        .muted { color: var(--muted); }
        .progress { width: 180px; height: 10px; background: #edf2f7; border-radius: 999px; overflow: hidden; }
        .progress span { display: block; height: 100%; background: var(--green); }
        .actions { display: flex; align-items: center; gap: 10px; flex-wrap: wrap; }
        .flash { background: #e6f4ea; color: #146c43; border: 1px solid #badbcc; padding: 14px 16px; margin-bottom: 22px; border-radius: 5px; }
        @media (max-width: 900px) { .app { grid-template-columns: 1fr; } .topbar { display: none; } .content { padding: 24px 18px 48px; } .panel { overflow-x: auto; } }
    </style>
</head>
<body>
<div class="app">
    <aside class="sidebar">
        <div class="brand"><x-unm-logo class="brand-logo" /><span>MAHASISWA</span></div>
        <section class="identity">
            <div>
                <div class="avatar">{{ mb_substr($student->name, 0, 1) }}</div>
                <h2>{{ $student->name }}</h2>
                <p>{{ $student->nim }}</p>
            </div>
        </section>
        <nav class="nav">
            <a class="{{ $activeMenu === 'dashboard' ? 'active' : '' }}" href="{{ route('mahasiswa.dashboard') }}"><span>H</span><span>Home</span></a>
            <a class="{{ $activeMenu === 'profile' ? 'active' : '' }}" href="{{ route('mahasiswa.profile') }}"><span>P</span><span>Profil Mahasiswa</span></a>
            <a class="{{ $activeMenu === 'guidance' ? 'active' : '' }}" href="{{ route('mahasiswa.guidance') }}"><span>B</span><span>Bimbingan TA</span></a>
            <a class="{{ $activeMenu === 'repository' ? 'active' : '' }}" href="{{ route('mahasiswa.repository') }}"><span>R</span><span>Repository</span></a>
            <a class="{{ $activeMenu === 'qa' ? 'active' : '' }}" href="{{ route('mahasiswa.qa') }}"><span>Q</span><span>Q & A</span></a>
        </nav>
    </aside>
    <main class="main">
        <header class="topbar">
            <strong>SIMTA MAHASISWA</strong>
            <form method="post" action="{{ route('logout') }}">
                @csrf
                <button class="logout-button" type="submit">Logout</button>
            </form>
        </header>
        <section class="content">
            <h1>{{ $title }}</h1>
            @if (session('status'))
                <div class="flash">{{ session('status') }}</div>
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
