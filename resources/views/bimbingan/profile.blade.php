@extends('bimbingan.layout')

@section('content')
    <div class="grid-2">
        <section class="panel profile-panel">
            <div class="profile-photo">
                @if ($profilePhotoUrl)
                    <img class="avatar photo-avatar" src="{{ $profilePhotoUrl }}" alt="Foto profil {{ $lecturer->name }}">
                @else
                    <div class="avatar">{{ mb_substr($lecturer->name, 0, 1) }}</div>
                @endif
            </div>
            <form class="photo-action" method="post" action="{{ route('profile.update') }}" enctype="multipart/form-data">
                @csrf
                <input name="employment_status" type="hidden" value="{{ $lecturer->employment_status }}">
                <input name="expertise" type="hidden" value="{{ $lecturer->expertise }}">
                <input name="email" type="hidden" value="{{ $lecturer->email }}">
                <input name="phone" type="hidden" value="{{ $lecturer->phone }}">
                <input name="address" type="hidden" value="{{ $lecturer->address }}">
                <label class="photo-upload">Ganti Foto
                    <input name="photo" type="file" accept="image/png,image/jpeg" required>
                </label>
                <button class="button secondary" type="submit">Upload</button>
            </form>
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
        <form method="post" action="{{ route('profile.update') }}" enctype="multipart/form-data">
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
            <div class="field-block">
                <label for="address">Alamat</label>
                <textarea id="address" name="address">{{ old('address', $lecturer->address) }}</textarea>
            </div>
            <div class="form-actions">
                <button class="button" type="submit">Edit</button>
            </div>
        </form>
    </section>
@endsection
