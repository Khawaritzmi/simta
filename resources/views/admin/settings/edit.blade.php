<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin Settings | Sistem Informasi Manajemen Tugas Akhir</title>
    @vite(['resources/css/admin-seminars.css', 'resources/js/app.js'])
</head>
<body>
<header class="topbar">
    <div class="brand"><x-unm-logo class="brand-logo" /><span>ADMIN SETTINGS</span></div>
    <div class="top-actions">
        <a href="{{ route('admin.dashboard') }}">Dashboard Admin</a>
        <a href="{{ route('admin.kolektif-update') }}">Update Kolektif</a>
        <a href="{{ route('admin.export-report') }}" download>Export CSV</a>
        <form method="post" action="{{ route('logout') }}">
            @csrf
            <button class="secondary" type="submit">Logout</button>
        </form>
    </div>
</header>

<main class="page">
    <h1>Pengaturan Sistem</h1>
    <p class="lead">Admin dapat mengatur target default jumlah bimbingan yang dipakai untuk progress PA dan TA.</p>

    @if (session('status'))
        <div class="flash">{{ session('status') }}</div>
    @endif
    @if ($errors->any())
        <div class="errors">{{ $errors->first() }}</div>
    @endif

    <section class="panel">
        <h2>Target Bimbingan Default</h2>
        <form method="post" action="{{ route('admin.settings.update') }}">
            @csrf
            @method('PUT')
            <div class="field">
                <label for="guidance_target_default">Jumlah Bimbingan Wajib</label>
                <input id="guidance_target_default" name="guidance_target_default" type="number" min="1" max="100" value="{{ old('guidance_target_default', $guidanceTarget) }}" required>
            </div>
            <button type="submit">Simpan Pengaturan</button>
        </form>
    </section>
</main>
</body>
</html>
