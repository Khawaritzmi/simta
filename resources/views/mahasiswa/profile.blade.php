@extends('mahasiswa.layout')

@section('content')
    <section class="panel">
        <h3>Foto Profil</h3>
        <div class="actions">
            @if ($profilePhotoUrl)
                <img class="avatar photo-avatar" src="{{ $profilePhotoUrl }}" alt="Foto profil {{ $student->name }}">
            @else
                <div class="avatar">{{ mb_substr($student->name, 0, 1) }}</div>
            @endif
            <form method="post" action="{{ route('mahasiswa.profile.update') }}" enctype="multipart/form-data">
                @csrf
                <label for="photo">Ganti Foto</label>
                <input id="photo" name="photo" type="file" accept="image/png,image/jpeg" required>
                <div class="form-actions">
                    <button class="button" type="submit">Upload Foto</button>
                </div>
            </form>
        </div>
    </section>

    <section class="panel">
        <h3>Profil Mahasiswa</h3>
        <table class="table">
            <tr><th>NIM</th><td>{{ $student->nim }}</td></tr>
            <tr><th>Nama</th><td>{{ $student->name }}</td></tr>
            <tr><th>Program Studi</th><td>{{ $student->program }}</td></tr>
            <tr><th>Email</th><td>{{ $student->email }}</td></tr>
        </table>
    </section>
@endsection
