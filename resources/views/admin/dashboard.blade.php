<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin | Sistem Informasi Manajemen Tugas Akhir</title>
    <style>
        body { margin: 0; min-height: 100vh; display: grid; place-items: center; background: #f5f7fc; color: #252b36; font-family: Inter, ui-sans-serif, system-ui, -apple-system, "Segoe UI", Arial, sans-serif; }
        .panel { width: min(720px, calc(100vw - 32px)); background: white; border: 1px solid #dfe7ef; padding: 36px; }
        h1 { margin: 0 0 14px; }
        p { color: #68707d; line-height: 1.65; }
        form { margin-top: 22px; }
        button { border: 0; border-radius: 5px; padding: 12px 18px; background: #1f91e8; color: white; font: inherit; font-weight: 800; cursor: pointer; }
        .button { display: inline-flex; border-radius: 5px; padding: 12px 18px; background: #edf2f7; color: #252b36; font: inherit; font-weight: 800; text-decoration: none; margin-right: 10px; }
    </style>
</head>
<body>
<main class="panel">
    <h1>Dashboard Admin</h1>
    <p>Halaman admin awal sudah aktif. Gunakan menu berikut untuk mengelola data master dan layanan bimbingan.</p>
    <a class="button" href="{{ route('admin.bimbingan-pa') }}">Kelola Bimbingan PA</a>
    <a class="button" href="{{ route('admin.database-ta') }}">Kelola DELTA-MAT</a>
    <form method="post" action="{{ route('logout') }}">
        @csrf
        <button type="submit">Logout</button>
    </form>
</main>
</body>
</html>
