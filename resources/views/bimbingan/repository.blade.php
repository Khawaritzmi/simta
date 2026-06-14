@extends('bimbingan.layout')

@section('content')
    <section class="panel">
        <h3>Tambah Dokumen</h3>
        <form method="post" action="{{ route('repository.store') }}">
            @csrf
            <div class="form-grid">
                <div>
                    <label for="thesis_guidance_id">Bimbingan</label>
                    <select id="thesis_guidance_id" name="thesis_guidance_id">
                        @foreach ($guidances as $guidance)
                            <option value="{{ $guidance->id }}">{{ $guidance->student_name }} - {{ $guidance->title }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="document_type">Jenis Dokumen</label>
                    <input id="document_type" name="document_type" placeholder="Draft Skripsi">
                </div>
                <div>
                    <label for="file_name">Nama File</label>
                    <input id="file_name" name="file_name" placeholder="dokumen.pdf">
                </div>
                <div>
                    <label for="url">URL</label>
                    <input id="url" name="url" placeholder="https://...">
                </div>
            </div>
            <div style="margin-top:18px">
                <button class="button" type="submit">Tambah</button>
            </div>
        </form>
    </section>

    <section class="panel">
        <h3>Repository</h3>
        <table class="table">
            <thead>
            <tr>
                <th>Mahasiswa</th>
                <th>Judul</th>
                <th>Dokumen</th>
                <th>Tautan</th>
            </tr>
            </thead>
            <tbody>
            @forelse ($documents as $document)
                <tr>
                    <td>{{ $document->student_name }}<br><span class="muted">{{ $document->nim }}</span></td>
                    <td>{{ $document->title }}</td>
                    <td><strong>{{ $document->document_type }}</strong><br><span class="muted">{{ $document->file_name }}</span></td>
                    <td>
                        @if ($document->url)
                            <a class="button small secondary" href="{{ $document->url }}" target="_blank">Buka</a>
                        @else
                            -
                        @endif
                    </td>
                </tr>
            @empty
                <tr><td colspan="4">Data repository belum tersedia.</td></tr>
            @endforelse
            </tbody>
        </table>
    </section>
@endsection
