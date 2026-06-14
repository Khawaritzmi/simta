<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title }} | Sistem Informasi Manajemen Tugas Akhir</title>
    @vite(['resources/css/account-password.css', 'resources/js/app.js'])
</head>
<body>
<main class="password-shell">
    <section class="brand-panel">
        <div>
            <x-unm-logo class="brand-logo" />
            <strong>SIMTA</strong>
            <h1>Ubah password akun</h1>
            <p>Gunakan password baru yang kuat agar akses akun {{ $roleLabel }} tetap aman.</p>
        </div>
        <a class="back-link" href="{{ route('portal') }}">Kembali ke portal</a>
    </section>

    <section class="password-panel">
        <p class="role-pill">{{ $roleLabel }}: {{ auth()->user()->name }}</p>
        <h2>Keamanan Akun</h2>

        @if (session('status'))
            <div class="flash">{{ session('status') }}</div>
        @endif
        @if ($errors->any())
            <div class="errors">{{ $errors->first() }}</div>
        @endif

        <form method="post" action="{{ route('password.update') }}">
            @csrf
            @method('PUT')
            <div class="field">
                <label for="current_password">Password Lama</label>
                <input id="current_password" name="current_password" type="password" autocomplete="current-password" required>
            </div>
            <div class="field">
                <label for="password">Password Baru</label>
                <input id="password" name="password" type="password" autocomplete="new-password" required>
            </div>
            <div class="field">
                <label for="password_confirmation">Konfirmasi Password Baru</label>
                <input id="password_confirmation" name="password_confirmation" type="password" autocomplete="new-password" required>
            </div>
            <button type="submit">Simpan Password</button>
        </form>
    </section>
</main>
</body>
</html>
