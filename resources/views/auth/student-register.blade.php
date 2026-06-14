<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Register Mahasiswa | Sistem Informasi Manajemen Tugas Akhir</title>
    @vite(['resources/css/auth-student-register.css', 'resources/js/app.js'])
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
