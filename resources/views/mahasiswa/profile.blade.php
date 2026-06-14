@extends('mahasiswa.layout')

@section('content')
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
