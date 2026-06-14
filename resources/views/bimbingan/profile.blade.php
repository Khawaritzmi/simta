@extends('bimbingan.layout')

@section('content')
    <div class="grid-2">
        <section class="panel" style="padding:0">
            <div class="profile-photo">
                <div class="avatar">{{ mb_substr($lecturer->name, 0, 1) }}</div>
            </div>
            <div class="photo-action">
                <button class="button secondary" type="button">Ganti Foto</button>
            </div>
        </section>

        <section class="panel">
            <h3>Profil Dosen</h3>
            @foreach ([
                'NIP' => $lecturer->nip,
                'NIDN' => $lecturer->nidn,
                'No Sertifikasi' => $lecturer->certificate_number,
                'Status Kepegawaian' => $lecturer->employment_status,
                'Bidang Keahlian' => $lecturer->expertise,
                'Nama' => $lecturer->name,
                'Jenis Kelamin' => $lecturer->gender,
                'Tempat / Tanggal Lahir' => trim(($lecturer->birth_place ?? '').', '.optional($lecturer->birth_date ? \Carbon\Carbon::parse($lecturer->birth_date) : null)->translatedFormat('j F Y'), ', '),
                'Email' => $lecturer->email,
                'No. Telp' => $lecturer->phone,
                'Alamat' => $lecturer->address,
            ] as $label => $value)
                <div class="detail-row">
                    <strong>{{ $label }}</strong>
                    <span>{{ $value ?: '-' }}</span>
                </div>
            @endforeach
        </section>
    </div>

    <section class="panel">
        <h3>Edit Profil</h3>
        <form method="post" action="{{ route('profile.update') }}">
            @csrf
            <div class="form-grid">
                <div>
                    <label for="employment_status">Status Kepegawaian</label>
                    <input id="employment_status" name="employment_status" value="{{ old('employment_status', $lecturer->employment_status) }}">
                </div>
                <div>
                    <label for="expertise">Bidang Keahlian</label>
                    <input id="expertise" name="expertise" value="{{ old('expertise', $lecturer->expertise) }}">
                </div>
                <div>
                    <label for="email">Email</label>
                    <input id="email" name="email" value="{{ old('email', $lecturer->email) }}">
                </div>
                <div>
                    <label for="phone">No. Telp</label>
                    <input id="phone" name="phone" value="{{ old('phone', $lecturer->phone) }}">
                </div>
            </div>
            <div style="margin-top:18px">
                <label for="address">Alamat</label>
                <textarea id="address" name="address">{{ old('address', $lecturer->address) }}</textarea>
            </div>
            <div style="margin-top:18px">
                <button class="button" type="submit">Edit</button>
            </div>
        </form>
    </section>
@endsection
