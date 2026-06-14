<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Register Mahasiswa | Sistem Informasi Manajemen Tugas Akhir</title>
    <style>
        :root { --blue:#1f91e8; --green:#16855b; --ink:#252b36; --muted:#747b86; --line:#dfe7ef; --page:#f5f7fc; --panel:#fff; }
        * { box-sizing: border-box; }
        body { margin: 0; min-height: 100vh; display: grid; place-items: center; background: var(--page); color: var(--ink); font-family: Inter, ui-sans-serif, system-ui, -apple-system, "Segoe UI", Arial, sans-serif; }
        .shell { width: min(920px, calc(100vw - 32px)); display: grid; grid-template-columns: 320px 1fr; background: var(--panel); border: 1px solid var(--line); min-height: 560px; }
        .brand { padding: 42px; background: #ecfdf5; display: flex; flex-direction: column; justify-content: space-between; }
        .brand strong { font-size: 28px; letter-spacing: .08em; }
        .brand-logo { width: 72px; height: 72px; object-fit: contain; }
        .brand h1 { margin: 34px 0 16px; font-size: 36px; line-height: 1.1; }
        .brand p, .switch { color: var(--muted); line-height: 1.65; }
        .panel { padding: 42px; display: flex; flex-direction: column; justify-content: center; }
        h2 { margin: 0 0 24px; font-size: 30px; }
        .grid { display: grid; grid-template-columns: repeat(2, minmax(190px, 1fr)); gap: 18px; }
        label { display: block; font-weight: 800; margin-bottom: 8px; }
        input { width: 100%; border: 1px solid var(--line); border-radius: 5px; padding: 13px 14px; font: inherit; }
        button { border: 0; border-radius: 5px; padding: 14px 22px; background: var(--green); color: white; font: inherit; font-weight: 800; cursor: pointer; margin-top: 22px; }
        .errors { background: #fdecec; color: #842029; border: 1px solid #f5c2c7; padding: 13px 14px; margin-bottom: 18px; border-radius: 5px; }
        a { color: var(--blue); font-weight: 800; text-decoration: none; }
        @media (max-width: 760px) { .shell, .grid { grid-template-columns: 1fr; } .brand, .panel { padding: 28px; } }
    </style>
</head>
<body>
<main class="shell">
    <section class="brand">
        <div>
            <x-unm-logo class="brand-logo" />
            <h1>Register Mahasiswa</h1>
            <p>Buat akun mahasiswa untuk melihat status bimbingan tugas akhir, jadwal seminar, repository, dan jawaban dosen.</p>
        </div>
        <p>Univeristas Negeri Makassar</p>
    </section>
    <section class="panel">
        <h2>Data Mahasiswa</h2>
        @if ($errors->any())
            <div class="errors">{{ $errors->first() }}</div>
        @endif
        <form method="post" action="{{ route('mahasiswa.register.store') }}">
            @csrf
            <div class="grid">
                <div>
                    <label for="name">Nama Lengkap</label>
                    <input id="name" name="name" value="{{ old('name') }}" required>
                </div>
                <div>
                    <label for="nim">NIM</label>
                    <input id="nim" name="nim" value="{{ old('nim') }}" required>
                </div>
                <div>
                    <label for="program">Program Studi</label>
                    <input id="program" name="program" value="{{ old('program', 'Matematika') }}" required>
                </div>
                <div>
                    <label for="email">Email</label>
                    <input id="email" name="email" type="email" value="{{ old('email') }}" required>
                </div>
                <div>
                    <label for="password">Password</label>
                    <input id="password" name="password" type="password" required>
                </div>
                <div>
                    <label for="password_confirmation">Konfirmasi Password</label>
                    <input id="password_confirmation" name="password_confirmation" type="password" required>
                </div>
            </div>
            <button type="submit">Register Mahasiswa</button>
        </form>
        <p class="switch">Sudah punya akun? <a href="{{ route('mahasiswa.login') }}">Login Mahasiswa</a></p>
        <p class="switch"><a href="{{ route('landing') }}">Kembali ke Beranda</a></p>
    </section>
</main>
</body>
</html>
