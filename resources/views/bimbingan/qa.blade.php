@extends('bimbingan.layout')

@section('content')
    <section class="panel">
        <h3>Q & A Mahasiswa</h3>
        <table class="table">
            <thead>
            <tr>
                <th>Mahasiswa</th>
                <th>Pertanyaan</th>
                <th>Jawaban</th>
            </tr>
            </thead>
            <tbody>
            @foreach ($questions as $question)
                <tr>
                    <td>{{ $question->student_name ?? 'Umum' }}<br><span class="muted">{{ $question->nim }}</span></td>
                    <td><strong>{{ $question->subject }}</strong><br>{{ $question->question }}</td>
                    <td>
                        <form method="post" action="{{ route('qa.answer', $question->id) }}">
                            @csrf
                            <textarea name="answer" placeholder="Tulis jawaban">{{ $question->answer }}</textarea>
                            <button class="button small" type="submit">Kirim</button>
                        </form>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </section>
@endsection
