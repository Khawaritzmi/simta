<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title }} | Sistem Informasi Manajemen Tugas Akhir</title>
    @vite(['resources/css/auth-login.css', 'resources/js/app.js'])
</head>
<body>
<main class="login-shell">
    <section class="brand-panel">
        <div>
            <x-unm-logo class="brand-logo" />
            <h1>SIMTA</h1>
            <p>Kelola bimbingan tugas akhir, persetujuan, seminar, repository, dan tanya jawab mahasiswa dalam satu aplikasi.</p>
        </div>
        <p>Univeristas Negeri Makassar</p>
    </section>
    <section class="login-panel">
        <h2>{{ $title }}</h2>

        @if (session('status'))
            <div class="flash">{{ session('status') }}</div>
        @endif
        @if ($errors->any())
            <div class="errors">{{ $errors->first() }}</div>
        @endif

        <form method="post" action="{{ $loginRoute }}">
            @csrf
            @if (! empty($next))
                <input type="hidden" name="next" value="{{ $next }}">
            @endif
            <div class="field">
                <label for="email">Email</label>
                <input id="email" name="email" type="email" value="{{ old('email', $defaultEmail) }}" autofocus required>
            </div>
            <div class="field">
                <label for="password">Password</label>
                <input id="password" name="password" type="password" required>
            </div>
            <label class="check">
                <input type="checkbox" name="remember" value="1">
                Ingat saya
            </label>
            <button type="submit">Login</button>
        </form>

        <p class="hint">{{ $demoText }}</p>
        @if ($showRegisterLink)
            <p class="switch">Belum punya akun? <a href="{{ $registerRoute }}">Register</a></p>
        @endif
        <p class="switch"><a href="{{ route('landing') }}">Kembali ke Beranda</a></p>
    </section>
</main>
</body>
</html>
