@extends('bimbingan.layout')

@section('content')
    <section class="panel">
        <h3>List Pengajuan TA</h3>
        <table class="table">
            <thead>
            <tr>
                <th>Mahasiswa</th>
                <th>Judul</th>
                <th>Peran Anda</th>
                <th>Status</th>
                <th>Alasan</th>
                <th>Aksi</th>
            </tr>
            </thead>
            <tbody>
            @forelse ($guidanceRequests as $item)
                <tr>
                    <td><strong>{{ $item->student_name }}</strong><br><span class="muted">{{ $item->nim }}</span></td>
                    <td>
                        {{ $item->title }}<br>
                        <span class="muted">
                            Pembimbing: {{ $item->supervisor_1_name }}, {{ $item->supervisor_2_name }}<br>
                            Penguji: {{ $item->examiner_1_name }}, {{ $item->examiner_2_name }}
                        </span>
                    </td>
                    <td>{{ $item->current_role_label }}</td>
                    <td>
                        <span class="badge {{ $item->current_status === 'approved' ? 'success' : ($item->current_status === 'rejected' ? 'danger' : 'warning') }}">{{ $item->current_status }}</span>
                        <br><span class="muted">Status akhir: {{ $item->status }}</span>
                    </td>
                    <td>{{ $item->current_note ?: '-' }}</td>
                    <td>
                        @if ($item->current_status === 'pending' && $item->status === 'pending')
                            <div class="actions">
                                <form method="post" action="{{ route('guidance-requests.decide', $item->id) }}">
                                    @csrf
                                    <input type="hidden" name="status" value="approved">
                                    <button class="button small success" type="submit">Setujui</button>
                                </form>
                                <form class="reject-form" method="post" action="{{ route('guidance-requests.decide', $item->id) }}">
                                    @csrf
                                    <input type="hidden" name="status" value="rejected">
                                    <textarea name="note" required placeholder="Alasan penolakan"></textarea>
                                    <button class="button small danger" type="submit">Tolak</button>
                                </form>
                            </div>
                        @else
                            <span class="muted">{{ $item->current_note ?: 'Keputusan sudah tercatat.' }}</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr><td colspan="6">Tidak ada pengajuan TA untuk Anda.</td></tr>
            @endforelse
            </tbody>
        </table>
    </section>
@endsection
