<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Register | Sistem Informasi Manajemen Tugas Akhir</title>
    @vite(['resources/css/auth-register.css', 'resources/js/app.js'])
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
