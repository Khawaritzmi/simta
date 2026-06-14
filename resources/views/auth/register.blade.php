<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Register | Sistem Informasi Manajemen Tugas Akhir</title>
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
            background: var(--page);
            color: var(--ink);
            font-family: Inter, ui-sans-serif, system-ui, -apple-system, "Segoe UI", Arial, sans-serif;
        }
        .page { width: min(1080px, calc(100vw - 32px)); margin: 32px auto; }
        .register-shell {
            display: grid;
            grid-template-columns: 320px 1fr;
            background: var(--panel);
            border: 1px solid var(--line);
            min-height: 680px;
        }
        .brand-panel { padding: 42px; background: #eef6ff; display: flex; flex-direction: column; justify-content: space-between; }
        .brand-panel strong { display: block; font-size: 28px; letter-spacing: .08em; margin-bottom: 30px; }
        .brand-logo { width: 72px; height: 72px; object-fit: contain; margin-bottom: 28px; }
        .brand-panel h1 { margin: 0 0 18px; font-size: 36px; line-height: 1.12; }
        .brand-panel p { margin: 0; color: var(--muted); line-height: 1.65; }
        .form-panel { padding: 42px; }
        h2 { margin: 0 0 24px; font-size: 30px; }
        h3 { margin: 26px 0 16px; font-size: 18px; }
        .grid { display: grid; grid-template-columns: repeat(2, minmax(220px, 1fr)); gap: 18px; }
        label { display: block; font-weight: 800; margin-bottom: 8px; }
        input, select, textarea {
            width: 100%;
            border: 1px solid var(--line);
            border-radius: 5px;
            padding: 13px 14px;
            font: inherit;
            color: var(--ink);
            background: white;
        }
        textarea { min-height: 96px; resize: vertical; }
        .full { grid-column: 1 / -1; }
        button {
            border: 0;
            border-radius: 5px;
            padding: 14px 22px;
            background: var(--blue);
            color: white;
            font: inherit;
            font-weight: 800;
            cursor: pointer;
            margin-top: 22px;
        }
        button:hover { background: var(--blue-dark); }
        .errors { background: #fdecec; color: #842029; border: 1px solid #f5c2c7; padding: 13px 14px; margin-bottom: 18px; border-radius: 5px; }
        .switch { margin-top: 18px; color: var(--muted); }
        .switch a { color: var(--blue); font-weight: 800; text-decoration: none; }
        @media (max-width: 860px) {
            .register-shell, .grid { grid-template-columns: 1fr; }
            .brand-panel, .form-panel { padding: 28px; }
            .full { grid-column: auto; }
        }
    </style>
</head>
<body>
<main class="page">
    <div class="register-shell">
        <section class="brand-panel">
            <div>
                <x-unm-logo class="brand-logo" />
                <h1>Register Dosen</h1>
                <p>Akun baru akan tersimpan di tabel users dan profil dosen akan tersimpan di tabel lecturers.</p>
            </div>
            <p>Univeristas Negeri Makassar</p>
        </section>

        <section class="form-panel">
            <h2>Buat Akun</h2>

            @if ($errors->any())
                <div class="errors">{{ $errors->first() }}</div>
            @endif

            <form method="post" action="{{ route('dosen.register.store') }}">
                @csrf

                <h3>Data Login</h3>
                <div class="grid">
                    <div>
                        <label for="name">Nama Lengkap</label>
                        <input id="name" name="name" value="{{ old('name') }}" required>
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

                <h3>Profil Dosen</h3>
                <div class="grid">
                    <div>
                        <label for="nip">NIP</label>
                        <input id="nip" name="nip" value="{{ old('nip') }}" required>
                    </div>
                    <div>
                        <label for="nidn">NIDN</label>
                        <input id="nidn" name="nidn" value="{{ old('nidn') }}">
                    </div>
                    <div>
                        <label for="employment_status">Status Kepegawaian</label>
                        <input id="employment_status" name="employment_status" value="{{ old('employment_status', 'Dosen Tetap') }}" required>
                    </div>
                    <div>
                        <label for="expertise">Bidang Keahlian</label>
                        <input id="expertise" name="expertise" value="{{ old('expertise') }}" required>
                    </div>
                    <div>
                        <label for="gender">Jenis Kelamin</label>
                        <select id="gender" name="gender">
                            <option value="">Pilih</option>
                            <option value="Laki-Laki" @selected(old('gender') === 'Laki-Laki')>Laki-Laki</option>
                            <option value="Perempuan" @selected(old('gender') === 'Perempuan')>Perempuan</option>
                        </select>
                    </div>
                    <div>
                        <label for="phone">No. Telp</label>
                        <input id="phone" name="phone" value="{{ old('phone') }}">
                    </div>
                    <div>
                        <label for="birth_place">Tempat Lahir</label>
                        <input id="birth_place" name="birth_place" value="{{ old('birth_place') }}">
                    </div>
                    <div>
                        <label for="birth_date">Tanggal Lahir</label>
                        <input id="birth_date" name="birth_date" type="date" value="{{ old('birth_date') }}">
                    </div>
                    <div class="full">
                        <label for="address">Alamat</label>
                        <textarea id="address" name="address">{{ old('address') }}</textarea>
                    </div>
                </div>

                <button type="submit">Register</button>
            </form>

            <p class="switch">Sudah punya akun? <a href="{{ route('dosen.login') }}">Login</a></p>
            <p class="switch"><a href="{{ route('landing') }}">Kembali ke Beranda</a></p>
        </section>
    </div>
</main>
</body>
</html>
