<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title }} | Sistem Informasi Manajemen Tugas Akhir</title>
    <style>
        :root {
            --blue: #1f91e8;
            --blue-soft: #e9f3ff;
            --ink: #252b36;
            --muted: #8b9098;
            --line: #dfe7ef;
            --page: #f5f7fc;
            --panel: #ffffff;
            --success: #198754;
            --danger: #dc3545;
            --warning: #b7791f;
        }
        * { box-sizing: border-box; }
        body {
            margin: 0;
            background: var(--page);
            color: var(--ink);
            font-family: Inter, ui-sans-serif, system-ui, -apple-system, "Segoe UI", Arial, sans-serif;
            font-size: 16px;
        }
        a { color: inherit; text-decoration: none; }
        .app { min-height: 100vh; display: grid; grid-template-columns: 300px 1fr; }
        .sidebar { background: var(--panel); border-right: 1px solid var(--line); display: flex; flex-direction: column; }
        .brand { height: 88px; display: flex; align-items: center; justify-content: space-between; padding: 0 42px; border-bottom: 1px solid var(--line); }
        .brand strong, .topbar strong { font-size: 28px; letter-spacing: .08em; font-weight: 700; }
        .brand-logo { width: 54px; height: 54px; object-fit: contain; }
        .hamburger { color: var(--blue); font-size: 28px; }
        .identity { min-height: 260px; display: grid; place-items: center; padding: 28px 24px; text-align: center; border-bottom: 1px solid var(--line); }
        .avatar {
            width: 108px; height: 108px; border-radius: 50%; display: grid; place-items: center;
            background: #d8ebff; color: var(--blue); font-size: 34px; font-weight: 800; border: 12px solid #d8ebff;
        }
        .identity h2 { max-width: 240px; margin: 22px 0 8px; font-size: 18px; line-height: 1.35; }
        .identity p { margin: 0; color: var(--muted); }
        .nav { padding: 28px 20px; display: grid; gap: 10px; }
        .nav a { display: grid; grid-template-columns: 34px 1fr; align-items: center; gap: 14px; min-height: 58px; padding: 0 18px; border-radius: 8px; font-weight: 700; }
        .nav a.active { background: var(--blue); color: white; }
        .nav .icon { font-size: 20px; text-align: center; }
        .main { display: flex; flex-direction: column; min-width: 0; }
        .topbar { height: 88px; background: var(--panel); border-bottom: 1px solid var(--line); display: flex; align-items: center; justify-content: space-between; padding: 0 42px; }
        .top-actions { display: flex; align-items: center; gap: 26px; font-size: 20px; }
        .logout { display: inline-flex; align-items: center; gap: 10px; padding: 14px 22px; border-radius: 5px; background: var(--blue-soft); color: var(--blue); font-weight: 800; }
        .logout-form { margin: 0; }
        .logout-button { border: 0; display: inline-flex; align-items: center; gap: 10px; padding: 14px 22px; border-radius: 5px; background: var(--blue-soft); color: var(--blue); font-weight: 800; font: inherit; cursor: pointer; }
        .content { flex: 1; padding: 34px 42px 72px; }
        .footer { background: white; border-top: 1px solid var(--line); display: flex; justify-content: space-between; gap: 18px; padding: 18px 42px; color: #919191; font-weight: 700; }
        h1 { margin: 0 0 34px; font-size: 34px; }
        h3 { margin: 0 0 22px; font-size: 22px; }
        .panel { background: var(--panel); border: 1px solid var(--line); padding: 32px; margin-bottom: 24px; }
        .grid-2 { display: grid; grid-template-columns: minmax(280px, 420px) 1fr; gap: 16px; }
        .table { width: 100%; border-collapse: collapse; }
        .table th, .table td { border-bottom: 1px solid var(--line); text-align: left; padding: 16px 14px; vertical-align: top; }
        .table th { font-size: 15px; letter-spacing: .02em; }
        .filters { display: grid; grid-template-columns: 160px repeat(3, 1fr) auto; align-items: center; gap: 18px; margin-bottom: 22px; }
        input, select, textarea {
            width: 100%; border: 1px solid var(--line); border-radius: 5px; padding: 13px 14px; font: inherit; background: white; color: var(--ink);
        }
        textarea { min-height: 96px; resize: vertical; }
        label { display: block; font-weight: 700; margin-bottom: 8px; }
        .form-grid { display: grid; grid-template-columns: repeat(2, minmax(240px, 1fr)); gap: 18px; }
        .button { border: 0; border-radius: 5px; padding: 12px 18px; background: var(--blue); color: white; font-weight: 800; font: inherit; cursor: pointer; }
        .button.secondary { background: #edf2f7; color: var(--ink); }
        .button.danger { background: var(--danger); }
        .button.success { background: var(--success); }
        .button.small { padding: 8px 12px; font-size: 14px; }
        .actions { display: flex; gap: 10px; align-items: center; flex-wrap: wrap; }
        .badge { display: inline-flex; align-items: center; min-height: 28px; padding: 4px 10px; border-radius: 999px; background: #eef2f7; font-weight: 800; font-size: 13px; }
        .badge.success { background: #e6f4ea; color: var(--success); }
        .badge.warning { background: #fff6db; color: var(--warning); }
        .notice { background: #8e8e8e; color: white; padding: 18px; border-radius: 3px; margin-bottom: 22px; }
        .flash { background: #e6f4ea; color: #146c43; border: 1px solid #badbcc; padding: 14px 16px; margin-bottom: 22px; border-radius: 5px; }
        .errors { background: #fdecec; color: #842029; border: 1px solid #f5c2c7; padding: 14px 16px; margin-bottom: 22px; border-radius: 5px; }
        .profile-photo { min-height: 360px; display: grid; place-items: center; border-bottom: 1px solid var(--line); }
        .profile-photo .avatar { width: 220px; height: 220px; font-size: 58px; border-width: 18px; }
        .photo-action { padding: 22px; display: flex; justify-content: flex-end; }
        .detail-row { display: grid; grid-template-columns: 220px 1fr; gap: 20px; padding: 15px 0; border-bottom: 1px solid var(--line); }
        .muted { color: var(--muted); }
        .progress { width: 150px; height: 10px; background: #edf2f7; border-radius: 999px; overflow: hidden; }
        .progress span { display: block; height: 100%; background: var(--blue); }
        @media (max-width: 900px) {
            .app { grid-template-columns: 1fr; }
            .sidebar { position: static; }
            .brand, .identity { min-height: auto; }
            .nav { grid-template-columns: repeat(2, minmax(0, 1fr)); }
            .topbar { display: none; }
            .content { padding: 24px 18px 48px; }
            .footer, .grid-2, .filters, .form-grid, .detail-row { display: block; }
            .filters > * { margin-bottom: 12px; }
            .panel { padding: 22px; overflow-x: auto; }
        }
    </style>
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
                <div class="avatar">{{ mb_substr($lecturer->name, 0, 1) }}</div>
                <h2>{{ $lecturer->name }}</h2>
                <p>Dosen</p>
            </div>
        </section>
        <nav class="nav">
            <a class="{{ $activeMenu === 'dashboard' ? 'active' : '' }}" href="{{ route('dashboard') }}"><span class="icon">H</span><span>Home</span></a>
            <a class="{{ $activeMenu === 'profile' ? 'active' : '' }}" href="{{ route('profile') }}"><span class="icon">P</span><span>Profil Dosen</span></a>
            <a class="{{ $activeMenu === 'guidance' ? 'active' : '' }}" href="{{ route('guidance') }}"><span class="icon">B</span><span>Bimbingan TA</span></a>
            <a class="{{ $activeMenu === 'approvals' ? 'active' : '' }}" href="{{ route('approvals') }}"><span class="icon">A</span><span>Persetujuan</span></a>
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
                <span>F</span>
                <span>D</span>
                <span>K</span>
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
</body>
</html>
