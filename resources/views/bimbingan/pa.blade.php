@extends('bimbingan.layout')

@section('content')
    <section class="pa-summary">
        <article><span>Mahasiswa PA</span><strong>{{ $paReport['students'] }}</strong></article>
        <article><span>Total Konsultasi</span><strong>{{ $paReport['consultations'] }}</strong></article>
        <article><span>Perlu Diproses</span><strong>{{ $paReport['pending'] }}</strong></article>
        <article><span>Selesai</span><strong>{{ $paReport['done'] }}</strong></article>
        <a class="button secondary" href="{{ route('dosen.export-report') }}" download>Export CSV</a>
    </section>

    <section class="pa-chat-shell" data-pa-chat>
        <aside class="pa-chat-sidebar">
            <div class="pa-chat-sidebar-head">
                <h3>Chat PA</h3>
                <span>{{ $paConsultations->count() }} percakapan</span>
            </div>

            <div class="pa-chat-list" role="list">
                @forelse ($paConsultations as $item)
                    <button class="pa-chat-contact {{ $loop->first ? 'active' : '' }}" type="button" data-chat-target="pa-chat-{{ $item->id }}" role="listitem">
                        <span class="chat-avatar">{{ mb_substr($item->student_name, 0, 1) }}</span>
                        <span class="chat-contact-body">
                            <strong>{{ $item->student_name }}</strong>
                            <small>{{ $item->nim }} - {{ $item->topic }}</small>
                            <small>{{ \Carbon\Carbon::parse($item->created_at)->format('d M Y H:i') }}</small>
                        </span>
                        <span class="badge {{ $item->status === 'selesai' ? 'success' : ($item->status === 'dibatalkan' ? 'danger' : 'warning') }}">{{ $item->status }}</span>
                    </button>
                @empty
                    <div class="pa-empty-state">
                        <strong>Belum ada chat PA</strong>
                        <span>Pengajuan dari mahasiswa akan muncul di sini.</span>
                    </div>
                @endforelse
            </div>

            <details class="pa-roster">
                <summary>Daftar Mahasiswa PA</summary>
                <div class="pa-roster-list">
                    @forelse ($paStudents as $student)
                        <div>
                            <strong>{{ $student->name }}</strong>
                            <span>{{ $student->nim }} - {{ $student->program }}</span>
                            <small>{{ $student->ipk ?? '-' }} / {{ $student->sks_total ?? '-' }} SKS · {{ $student->academic_status ?? 'Belum ada data' }}</small>
                        </div>
                    @empty
                        <p class="muted">Belum ada mahasiswa PA.</p>
                    @endforelse
                </div>
            </details>
        </aside>

        <div class="pa-chat-main">
            @forelse ($paConsultations as $item)
                <article class="pa-chat-thread {{ $loop->first ? 'active' : '' }}" id="pa-chat-{{ $item->id }}" data-chat-thread>
                    <header class="pa-chat-header">
                        <div class="chat-avatar large">{{ mb_substr($item->student_name, 0, 1) }}</div>
                        <div>
                            <h3>{{ $item->student_name }}</h3>
                            <p>{{ $item->nim }} · {{ $item->program }}</p>
                        </div>
                        <span class="badge {{ $item->status === 'selesai' ? 'success' : ($item->status === 'dibatalkan' ? 'danger' : 'warning') }}">{{ $item->status }}</span>
                    </header>

                    <div class="pa-chat-messages">
                        @php($messages = $paMessages->get($item->id, collect()))
                        @if ($messages->isEmpty())
                            <div class="chat-bubble incoming">
                                <span class="chat-meta">{{ \Carbon\Carbon::parse($item->created_at)->format('d M Y H:i') }}</span>
                                <strong>{{ $item->topic }}</strong>
                                <p>{{ $item->student_note }}</p>
                                @if ($item->requested_at)
                                    <small>Usulan waktu: {{ \Carbon\Carbon::parse($item->requested_at)->format('d M Y H:i') }}</small>
                                @endif
                            </div>

                            @if ($item->lecturer_note || $item->recommendation || $item->scheduled_at)
                                <div class="chat-bubble outgoing">
                                    <span class="chat-meta">Respons Dosen PA</span>
                                    @if ($item->scheduled_at)
                                        <strong>Jadwal: {{ \Carbon\Carbon::parse($item->scheduled_at)->format('d M Y H:i') }}</strong>
                                    @endif
                                    @if ($item->lecturer_note)
                                        <p>{{ $item->lecturer_note }}</p>
                                    @endif
                                    @if ($item->recommendation)
                                        <p><strong>Rekomendasi:</strong> {{ $item->recommendation }}</p>
                                    @endif
                                </div>
                            @endif
                        @else
                            @foreach ($messages as $message)
                                <div class="chat-bubble {{ $message->sender_role === 'dosen' ? 'outgoing' : 'incoming' }}">
                                    <span class="chat-meta">{{ $message->sender_role === 'dosen' ? 'Dosen PA' : 'Mahasiswa' }} · {{ \Carbon\Carbon::parse($message->created_at)->format('d M Y H:i') }}</span>
                                    <p>{{ $message->message }}</p>
                                </div>
                            @endforeach
                        @endif
                    </div>

                    <form class="pa-chat-composer" method="post" action="{{ route('pa.dosen.consultations.messages.store', $item->id) }}">
                        @csrf
                        <div class="form-grid">
                            <div>
                                <label>Status</label>
                                <select name="status">
                                    @foreach (['diajukan', 'dijadwalkan', 'selesai', 'dibatalkan'] as $status)
                                        <option value="{{ $status }}" @selected($item->status === $status)>{{ ucfirst($status) }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label>Jadwal konsultasi</label>
                                <input name="scheduled_at" type="datetime-local" value="{{ $item->scheduled_at ? date('Y-m-d\TH:i', strtotime($item->scheduled_at)) : '' }}">
                            </div>
                        </div>
                        <label>Pesan untuk mahasiswa</label>
                        <textarea name="message" placeholder="Tulis balasan atau catatan hasil bimbingan PA."></textarea>
                        <label>Rekomendasi PA</label>
                        <textarea name="recommendation" placeholder="Tulis rekomendasi akademik, KRS, SKS, atau tindak lanjut.">{{ $item->recommendation }}</textarea>
                        <button class="button" type="submit">Kirim Catatan</button>
                    </form>
                </article>
            @empty
                <article class="pa-chat-thread active">
                    <div class="pa-empty-state roomy">
                        <strong>Belum ada percakapan PA</strong>
                        <span>Ketika mahasiswa mengirim konsultasi, percakapan akan muncul dalam tampilan chat.</span>
                    </div>
                </article>
            @endforelse
        </div>
    </section>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            document.querySelectorAll('[data-pa-chat]').forEach((chat) => {
                const contacts = chat.querySelectorAll('[data-chat-target]');
                const threads = chat.querySelectorAll('[data-chat-thread]');

                contacts.forEach((contact) => {
                    contact.addEventListener('click', () => {
                        contacts.forEach((item) => item.classList.remove('active'));
                        threads.forEach((thread) => thread.classList.remove('active'));

                        contact.classList.add('active');
                        chat.querySelector(`#${contact.dataset.chatTarget}`)?.classList.add('active');
                    });
                });
            });
        });
    </script>
@endpush
