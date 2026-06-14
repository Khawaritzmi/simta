@extends('bimbingan.layout')

@section('content')
    @if ($approvals->isEmpty())
        <div class="notice">Tidak ada usulan yang memerlukan persetujuan anda.</div>
    @else
        <section class="panel">
            <h3>Usulan Menunggu Persetujuan</h3>
            <table class="table">
                <thead>
                <tr>
                    <th>Mahasiswa</th>
                    <th>Jenis</th>
                    <th>Judul</th>
                    <th>Aksi</th>
                </tr>
                </thead>
                <tbody>
                @foreach ($approvals as $approval)
                    <tr>
                        <td><strong>{{ $approval->student_name }}</strong><br><span class="muted">{{ $approval->nim }}</span></td>
                        <td>{{ $approval->type }}<br><span class="muted">{{ $approval->description }}</span></td>
                        <td>{{ $approval->title }}</td>
                        <td>
                            <div class="actions">
                                <form method="post" action="{{ route('approvals.decide', $approval->id) }}">
                                    @csrf
                                    <input type="hidden" name="status" value="approved">
                                    <button class="button small success" type="submit">Setujui</button>
                                </form>
                                <form method="post" action="{{ route('approvals.decide', $approval->id) }}">
                                    @csrf
                                    <input type="hidden" name="status" value="rejected">
                                    <button class="button small danger" type="submit">Tolak</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </section>
    @endif
@endsection
