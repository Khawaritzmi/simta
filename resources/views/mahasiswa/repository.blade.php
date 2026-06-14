@extends('mahasiswa.layout')

@section('content')
    <section class="panel">
        <h3>Dokumen Tugas Akhir Saya</h3>
        <table class="table">
            <thead><tr><th>Judul</th><th>Dosen</th><th>Dokumen</th><th>Tautan</th></tr></thead>
            <tbody>
            @forelse ($documents as $document)
                <tr>
                    <td>{{ $document->title }}</td>
                    <td>{{ $document->lecturer_name }}</td>
                    <td>{{ $document->document_type }}<br><span class="muted">{{ $document->file_name }}</span></td>
                    <td>@if ($document->url)<a href="{{ $document->url }}" target="_blank">Buka</a>@else - @endif</td>
                </tr>
            @empty
                <tr><td colspan="4">Belum ada dokumen repository.</td></tr>
            @endforelse
            </tbody>
        </table>
    </section>
@endsection
