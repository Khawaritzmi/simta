@extends('bimbingan.layout')

@section('content')
    <section class="panel">
        <h3>Manual Aplikasi</h3>
        <table class="table">
            <thead>
            <tr>
                <th>Dokumen</th>
                <th>Pengguna</th>
                <th>Keterangan</th>
            </tr>
            </thead>
            <tbody>
            <tr>
                <td>Admin Manual.pdf</td>
                <td>Admin</td>
                <td>Panduan pengelolaan data master dan validasi sistem.</td>
            </tr>
            <tr>
                <td>Student Manual.pdf</td>
                <td>Mahasiswa</td>
                <td>Panduan pengajuan bimbingan, seminar, dan repository.</td>
            </tr>
            <tr>
                <td>Validator Manual.pdf</td>
                <td>Validator</td>
                <td>Panduan validasi usulan dan kelengkapan dokumen.</td>
            </tr>
            </tbody>
        </table>
    </section>
@endsection
