@extends('mahasiswa.layout')

@section('content')
    @if ($paAssignment)
        <section class="pa-chat-shell student-chat">
            <aside class="pa-chat-sidebar">
                <div class="pa-chat-sidebar-head">
                    <h3>Dosen PA</h3>
                    <span>{{ $paAssignment->academic_year }}</span>
                </div>

                <div class="pa-advisor-card">
                    <span class="chat-avatar">{{ mb_substr($paAssignment->lecturer_name, 0, 1) }}</span>
                    <div>
                        <strong>{{ $paAssignment->lecturer_name }}</strong>
                        <small>{{ $paAssignment->lecturer_email ?? '-' }}</small>
                        <small>{{ $paAssignment->lecturer_phone ?? '-' }}</small>
                    </div>
                </div>

                <div class="pa-stat-list">
                    <div><span>IPK Terakhir</span><strong>{{ $latestRecord?->ipk ?? '-' }}</strong></div>
                    <div><span>SKS Total</span><strong>{{ $latestRecord?->sks_total ?? '-' }}</strong></div>
                    <div><span>Konsultasi</span><strong>{{ $paConsultations->count() }}</strong></div>
                </div>

                <details class="pa-roster" open>
                    <summary>Monitoring IPK dan SKS</summary>
                    <div class="pa-roster-list">
                        @forelse ($paRecords as $record)
                            <div>
                                <strong>Semester {{ $record->semester }}</strong>
                                <span>IPK {{ $record->ipk }} · SKS {{ $record->sks_semester }}</span>
                                <small>Total {{ $record->sks_total }} SKS</small>
                            </div>
                        @empty
                            <p class="muted">Belum ada data akademik.</p>
                        @endforelse
                    </div>
                </details>
            </aside>

            <div class="pa-chat-main">
                <article class="pa-chat-thread active">
                    <header class="pa-chat-header">
                        <div class="chat-avatar large">{{ mb_substr($paAssignment->lecturer_name, 0, 1) }}</div>
                        <div>
                            <h3>{{ $paAssignment->lecturer_name }}</h3>
                            <p>Bimbingan PA · {{ $paAssignment->academic_year }}</p>
                        </div>
                    </header>

                    <div class="pa-chat-messages">
                        @forelse ($paConsultations->sortBy('created_at') as $item)
                            <div class="chat-topic-divider">
                                <span>{{ $item->topic }}</span>
                                <small>Status: {{ $item->status }}</small>
                            </div>

                            @php($messages = $paMessages->get($item->id, collect()))
                            @if ($messages->isEmpty())
                                <div class="chat-bubble outgoing">
                                    <span class="chat-meta">{{ \Carbon\Carbon::parse($item->created_at)->format('d M Y H:i') }}</span>
                                    <p>{{ $item->student_note }}</p>
                                    @if ($item->requested_at)
                                        <small>Usulan waktu: {{ \Carbon\Carbon::parse($item->requested_at)->format('d M Y H:i') }}</small>
                                    @endif
                                </div>

                                @if ($item->lecturer_note || $item->recommendation || $item->scheduled_at)
                                    <div class="chat-bubble incoming">
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
                                    <div class="chat-bubble {{ $message->sender_role === 'mahasiswa' ? 'outgoing' : 'incoming' }}">
                                        <span class="chat-meta">{{ $message->sender_role === 'mahasiswa' ? 'Anda' : 'Dosen PA' }} · {{ \Carbon\Carbon::parse($message->created_at)->format('d M Y H:i') }}</span>
                                        <p>{{ $message->message }}</p>
                                    </div>
                                @endforeach
                            @endif
                        @empty
                            <div class="pa-empty-state roomy">
                                <strong>Belum ada konsultasi PA</strong>
                                <span>Kirim pesan pertama melalui form di bawah.</span>
                            </div>
                        @endforelse
                    </div>

                    @if ($paConsultations->isNotEmpty())
                        @php($activeConsultation = $paConsultations->first())
                        <form class="pa-chat-composer" method="post" action="{{ route('pa.mahasiswa.consultations.messages.store', $activeConsultation->id) }}">
                            @csrf
                            <label for="message">Balas percakapan terbaru</label>
                            <textarea id="message" name="message" required placeholder="Tulis pesan lanjutan untuk Dosen PA.">{{ old('message') }}</textarea>
                            <button class="button" type="submit">Kirim Balasan</button>
                        </form>

                        <details class="pa-new-topic">
                            <summary>Buka topik konsultasi baru</summary>
                            <form class="pa-chat-composer" method="post" action="{{ route('pa.mahasiswa.consultations.store') }}">
                                @csrf
                                <div class="form-grid">
                                    <div>
                                        <label for="topic">Topik Konsultasi</label>
                                        <input id="topic" name="topic" value="{{ old('topic') }}" placeholder="Contoh: Rencana KRS semester depan" required>
                                    </div>
                                    <div>
                                        <label for="requested_at">Waktu yang Diusulkan</label>
                                        <input id="requested_at" name="requested_at" type="datetime-local" value="{{ old('requested_at') }}">
                                    </div>
                                </div>
                                <label for="student_note">Pesan untuk Dosen PA</label>
                                <textarea id="student_note" name="student_note" required placeholder="Tulis kebutuhan konsultasi, kendala akademik, atau rencana KRS.">{{ old('student_note') }}</textarea>
                                <button class="button" type="submit">Kirim Topik Baru</button>
                            </form>
                        </details>
                    @else
                        <form class="pa-chat-composer" method="post" action="{{ route('pa.mahasiswa.consultations.store') }}">
                            @csrf
                            <div class="form-grid">
                                <div>
                                    <label for="topic">Topik Konsultasi</label>
                                    <input id="topic" name="topic" value="{{ old('topic') }}" placeholder="Contoh: Rencana KRS semester depan" required>
                                </div>
                                <div>
                                    <label for="requested_at">Waktu yang Diusulkan</label>
                                    <input id="requested_at" name="requested_at" type="datetime-local" value="{{ old('requested_at') }}">
                                </div>
                            </div>
                            <label for="student_note">Pesan untuk Dosen PA</label>
                            <textarea id="student_note" name="student_note" required placeholder="Tulis kebutuhan konsultasi, kendala akademik, atau rencana KRS.">{{ old('student_note') }}</textarea>
                            <button class="button" type="submit">Kirim Pesan PA</button>
                        </form>
                    @endif
                </article>
            </div>
        </section>
    @else
        <section class="panel">
            <h3>Bimbingan PA</h3>
            <p class="muted">Dosen PA belum ditetapkan oleh admin.</p>
        </section>
    @endif
@endsection
