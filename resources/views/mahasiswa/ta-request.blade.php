@extends('mahasiswa.layout')

@section('content')
    <section class="panel">
        <h3>Form Pengajuan TA</h3>
        <form method="post" action="{{ route('mahasiswa.guidance-requests.store') }}">
            @csrf
            <div class="field">
                <label for="title">Judul Skripsi</label>
                <textarea id="title" name="title" required>{{ old('title') }}</textarea>
            </div>
            <div class="form-grid">
                <div>
                    <label for="supervisor_1_id">Dosen Pembimbing 1</label>
                    <select id="supervisor_1_id" name="supervisor_1_id" required>
                        <option value="">Pilih dosen</option>
                        @foreach ($lecturers as $lecturer)
                            <option value="{{ $lecturer->id }}" @selected((int) old('supervisor_1_id') === $lecturer->id)>{{ $lecturer->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="supervisor_2_id">Dosen Pembimbing 2</label>
                    <select id="supervisor_2_id" name="supervisor_2_id" required>
                        <option value="">Pilih dosen</option>
                        @foreach ($lecturers as $lecturer)
                            <option value="{{ $lecturer->id }}" @selected((int) old('supervisor_2_id') === $lecturer->id)>{{ $lecturer->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="examiner_1_id">Dosen Penguji 1</label>
                    <select id="examiner_1_id" name="examiner_1_id" required>
                        <option value="">Pilih dosen</option>
                        @foreach ($lecturers as $lecturer)
                            <option value="{{ $lecturer->id }}" @selected((int) old('examiner_1_id') === $lecturer->id)>{{ $lecturer->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="examiner_2_id">Dosen Penguji 2</label>
                    <select id="examiner_2_id" name="examiner_2_id" required>
                        <option value="">Pilih dosen</option>
                        @foreach ($lecturers as $lecturer)
                            <option value="{{ $lecturer->id }}" @selected((int) old('examiner_2_id') === $lecturer->id)>{{ $lecturer->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <p><button class="button" type="submit">Kirim Pengajuan TA</button></p>
        </form>
    </section>

    <section class="panel">
        <h3>Status Pengajuan TA</h3>
        <table class="table">
            <thead>
            <tr>
                <th>Judul</th>
                <th>Pembimbing</th>
                <th>Penguji</th>
                <th>Admin</th>
                <th>Alasan</th>
                <th>Status Akhir</th>
            </tr>
            </thead>
            <tbody>
            @forelse ($guidanceRequests as $item)
                <tr>
                    <td><strong>{{ $item->title }}</strong><br><span class="muted">{{ $item->created_at }}</span></td>
                    <td>
                        {{ $item->supervisor_1_name }} <span class="badge {{ $item->supervisor_1_status === 'approved' ? 'success' : ($item->supervisor_1_status === 'rejected' ? 'danger' : 'warning') }}">{{ $item->supervisor_1_status }}</span><br>
                        {{ $item->supervisor_2_name }} <span class="badge {{ $item->supervisor_2_status === 'approved' ? 'success' : ($item->supervisor_2_status === 'rejected' ? 'danger' : 'warning') }}">{{ $item->supervisor_2_status }}</span>
                    </td>
                    <td>
                        {{ $item->examiner_1_name }} <span class="badge {{ $item->examiner_1_status === 'approved' ? 'success' : ($item->examiner_1_status === 'rejected' ? 'danger' : 'warning') }}">{{ $item->examiner_1_status }}</span><br>
                        {{ $item->examiner_2_name }} <span class="badge {{ $item->examiner_2_status === 'approved' ? 'success' : ($item->examiner_2_status === 'rejected' ? 'danger' : 'warning') }}">{{ $item->examiner_2_status }}</span>
                    </td>
                    <td><span class="badge {{ $item->admin_status === 'approved' ? 'success' : ($item->admin_status === 'rejected' ? 'danger' : 'warning') }}">{{ $item->admin_status }}</span></td>
                    <td>
                        @foreach ([
                            'Admin' => $item->admin_note,
                            'Pembimbing 1' => $item->supervisor_1_note,
                            'Pembimbing 2' => $item->supervisor_2_note,
                            'Penguji 1' => $item->examiner_1_note,
                            'Penguji 2' => $item->examiner_2_note,
                        ] as $label => $note)
                            @if ($note)
                                <strong>{{ $label }}:</strong> {{ $note }}<br>
                            @endif
                        @endforeach
                        @if (! $item->admin_note && ! $item->supervisor_1_note && ! $item->supervisor_2_note && ! $item->examiner_1_note && ! $item->examiner_2_note)
                            <span class="muted">-</span>
                        @endif
                    </td>
                    <td><span class="badge {{ $item->status === 'approved' ? 'success' : ($item->status === 'rejected' ? 'danger' : 'warning') }}">{{ $item->status }}</span></td>
                </tr>
            @empty
                <tr><td colspan="6">Belum ada pengajuan TA.</td></tr>
            @endforelse
            </tbody>
        </table>
    </section>
@endsection
