<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title }} | Sistem Informasi Manajemen Tugas Akhir</title>
    <style>
        :root {
            --blue: #1f91e8;
            --blue-dark: #176fb2;
            --ink: #252b36;
            --muted: #747b86;
            --line: #dfe7ef;
            --page: #f5f7fc;
            --panel: #ffffff;
        }
        * { box-sizing: border-box; }
        body {
            margin: 0;
            min-height: 100vh;
            display: grid;
            place-items: center;
            background: var(--page);
            color: var(--ink);
            font-family: Inter, ui-sans-serif, system-ui, -apple-system, "Segoe UI", Arial, sans-serif;
        }
        .login-shell {
            width: min(960px, calc(100vw - 32px));
            display: grid;
            grid-template-columns: 1fr 420px;
            background: var(--panel);
            border: 1px solid var(--line);
            min-height: 560px;
        }
        .brand-panel { padding: 56px; display: flex; flex-direction: column; justify-content: space-between; background: #eef6ff; }
        .brand-panel strong { display: block; font-size: 28px; letter-spacing: .08em; margin-bottom: 36px; }
        .brand-logo { width: 72px; height: 72px; object-fit: contain; margin-bottom: 30px; }
        .brand-panel h1 { margin: 0 0 18px; font-size: 42px; line-height: 1.1; }
        .brand-panel p { margin: 0; color: var(--muted); max-width: 420px; line-height: 1.65; }
        .login-panel { padding: 56px 42px; display: flex; flex-direction: column; justify-content: center; }
        h2 { margin: 0 0 28px; font-size: 30px; }
        label { display: block; font-weight: 800; margin-bottom: 8px; }
        input { width: 100%; border: 1px solid var(--line); border-radius: 5px; padding: 14px; font: inherit; color: var(--ink); }
        .field { margin-bottom: 18px; }
        .check { display: flex; align-items: center; gap: 10px; margin: 6px 0 22px; color: var(--muted); }
        .check input { width: auto; }
        button { width: 100%; border: 0; border-radius: 5px; padding: 14px 18px; background: var(--blue); color: white; font: inherit; font-weight: 800; cursor: pointer; }
        button:hover { background: var(--blue-dark); }
        .flash { background: #e6f4ea; color: #146c43; border: 1px solid #badbcc; padding: 13px 14px; margin-bottom: 18px; border-radius: 5px; }
        .errors { background: #fdecec; color: #842029; border: 1px solid #f5c2c7; padding: 13px 14px; margin-bottom: 18px; border-radius: 5px; }
        .hint { margin-top: 24px; color: var(--muted); font-size: 14px; line-height: 1.6; }
        .switch { margin-top: 18px; text-align: center; color: var(--muted); }
        .switch a { color: var(--blue); font-weight: 800; text-decoration: none; }
        @media (max-width: 820px) {
            .login-shell { grid-template-columns: 1fr; }
            .brand-panel, .login-panel { padding: 34px; }
        }
    </style>
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
