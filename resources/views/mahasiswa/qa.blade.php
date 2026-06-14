@extends('mahasiswa.layout')

@section('content')
    <section class="panel">
        <h3>Q & A dengan Dosen</h3>
        <table class="table">
            <thead><tr><th>Dosen</th><th>Pertanyaan</th><th>Jawaban</th></tr></thead>
            <tbody>
            @forelse ($questions as $question)
                <tr>
                    <td>{{ $question->lecturer_name }}</td>
                    <td><strong>{{ $question->subject }}</strong><br>{{ $question->question }}</td>
                    <td>{{ $question->answer ?: 'Belum dijawab.' }}</td>
                </tr>
            @empty
                <tr><td colspan="3">Belum ada pertanyaan.</td></tr>
            @endforelse
            </tbody>
        </table>
    </section>
@endsection
