@extends('bimbingan.layout')

@section('content')
    <section class="panel">
        <h3>Validasi Jadwal Seminar / Ujian dari Admin</h3>
        <table class="table">
            <thead>
            <tr>
                <th>Mahasiswa</th>
                <th>Jenis</th>
                <th>Usulan Jadwal</th>
                <th>Peran Anda</th>
                <th>Status</th>
                <th>Alasan</th>
                <th>Aksi</th>
            </tr>
            </thead>
            <tbody>
            @forelse ($seminarRequests as $request)
                <tr>
                    <td><strong>{{ $request->student_name }}</strong><br><span class="muted">{{ $request->nim }}</span></td>
                    <td>{{ $request->type }}<br><span class="muted">{{ $request->title }}</span></td>
                    <td>{{ \Carbon\Carbon::parse($request->proposed_at)->format('d M Y H:i') }}<br><span class="muted">{{ $request->room ?: '-' }}</span></td>
                    <td>{{ $request->current_role_label }}</td>
                    <td>
                        <span class="badge {{ $request->current_status === 'approved' ? 'success' : ($request->current_status === 'rejected' ? 'danger' : 'warning') }}">{{ $request->current_status }}</span><br>
                        <span class="muted">Final: {{ $request->status }}</span>
                    </td>
                    <td>{{ $request->current_note ?: '-' }}</td>
                    <td>
                        @if ($request->current_status === 'pending' && $request->status === 'pending')
                            <div class="actions">
                                <form method="post" action="{{ route('seminar-requests.decide', $request->id) }}">
                                    @csrf
                                    <input type="hidden" name="status" value="approved">
                                    <button class="button small success" type="submit">Setujui</button>
                                </form>
                                <form class="reject-form" method="post" action="{{ route('seminar-requests.decide', $request->id) }}">
                                    @csrf
                                    <input type="hidden" name="status" value="rejected">
                                    <textarea name="note" required placeholder="Alasan penolakan"></textarea>
                                    <button class="button small danger" type="submit">Tolak</button>
                                </form>
                            </div>
                        @else
                            <span class="muted">Keputusan sudah tercatat.</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr><td colspan="7">Belum ada pengajuan jadwal seminar/ujian untuk Anda.</td></tr>
            @endforelse
            </tbody>
        </table>
    </section>

    <section class="panel">
        <h3>Riwayat Seminar / Ujian</h3>
        <div class="seminar-list">
            @forelse ($seminars as $seminar)
                <article class="seminar-card" id="seminar-{{ $seminar->id }}">
                    <div class="seminar-student">
                        <strong>{{ $seminar->student_name }}</strong>
                        <span class="muted">{{ $seminar->nim }}</span>
                    </div>

                    <div class="seminar-detail">
                        <span class="badge">{{ $seminar->type }}</span>
                        <h4>{{ $seminar->title }}</h4>
                        <p>{{ \Carbon\Carbon::parse($seminar->scheduled_at)->format('d M Y H:i') }}</p>
                        <p class="muted">{{ $seminar->room ?: '-' }}</p>
                    </div>

                    <div class="seminar-documents" id="dokumen-seminar-{{ $seminar->id }}">
                        <strong>Lihat dokumen mahasiswa</strong>
                        <div class="document-grid">
                            @foreach ($uploadCategories as $category => $label)
                                @php($upload = $seminar->uploads->get($category))
                                @if ($upload)
                                    <a class="document-link" href="{{ $upload->url }}" target="_blank" rel="noopener">
                                        <span>{{ $label }}</span>
                                        <small>{{ $upload->original_name }}</small>
                                    </a>
                                @else
                                    <span class="document-missing">{{ $label }} belum ada</span>
                                @endif
                            @endforeach
                        </div>
                    </div>

                    <div class="seminar-score">
                        <strong>Nilai</strong>
                        <span>{{ $seminar->score ?? '-' }}</span>
                        <span class="badge {{ $seminar->status === 'graded' ? 'success' : '' }}">{{ $seminar->status }}</span>
                    </div>

                    <div class="seminar-grade-summary">
                        <strong>Penilaian</strong>
                        <p class="muted">{{ $seminar->feedback ?: 'Belum ada catatan penilaian.' }}</p>
                        <button class="button small" type="button" data-open-modal="seminar-modal-{{ $seminar->id }}">Isi/Edit Penilaian</button>
                    </div>
                </article>

                <div class="modal-backdrop" id="seminar-modal-{{ $seminar->id }}" hidden>
                    <section class="modal-panel" role="dialog" aria-modal="true" aria-labelledby="seminar-modal-title-{{ $seminar->id }}">
                        <div class="modal-header">
                            <div>
                                <p class="modal-kicker">{{ $seminar->type }} - {{ $seminar->student_name }}</p>
                                <h3 id="seminar-modal-title-{{ $seminar->id }}">Penilaian Seminar / Ujian</h3>
                            </div>
                            <button class="modal-close" type="button" aria-label="Tutup modal" data-close-modal>&times;</button>
                        </div>

                        <div class="modal-body">
                            <div class="modal-meta">
                                <div>
                                    <strong>Judul</strong>
                                    <p>{{ $seminar->title }}</p>
                                </div>
                                <div>
                                    <strong>Jadwal</strong>
                                    <p>{{ \Carbon\Carbon::parse($seminar->scheduled_at)->format('d M Y H:i') }} - {{ $seminar->room ?: '-' }}</p>
                                </div>
                            </div>

                            <div class="modal-documents">
                                <strong>Dokumen mahasiswa</strong>
                                <div class="document-grid">
                                    @foreach ($uploadCategories as $category => $label)
                                        @php($upload = $seminar->uploads->get($category))
                                        @if ($upload)
                                            <a class="document-link" href="{{ $upload->url }}" target="_blank" rel="noopener">
                                                <span>{{ $label }}</span>
                                                <small>{{ $upload->original_name }}</small>
                                            </a>
                                        @else
                                            <span class="document-missing">{{ $label }} belum ada</span>
                                        @endif
                                    @endforeach
                                </div>
                            </div>

                            <form class="modal-form" method="post" action="{{ route('seminars.grade', $seminar->id) }}">
                                @csrf
                                <div class="form-grid compact-grid">
                                    <div>
                                        <label for="score-{{ $seminar->id }}">Nilai</label>
                                        <input id="score-{{ $seminar->id }}" type="number" name="score" min="0" max="100" value="{{ $seminar->score }}" placeholder="0-100" required>
                                    </div>
                                    <div>
                                        <label>Status setelah simpan</label>
                                        <input value="graded" disabled>
                                    </div>
                                </div>

                                <div class="field-block">
                                    <label for="feedback-{{ $seminar->id }}">Catatan penilaian lengkap</label>
                                    <textarea id="feedback-{{ $seminar->id }}" name="feedback" maxlength="1000" placeholder="Tulis catatan, komentar revisi, atau rekomendasi penilaian.">{{ $seminar->feedback }}</textarea>
                                </div>

                                <div class="modal-actions">
                                    <button class="button" type="submit">Simpan Penilaian</button>
                                    <button class="button secondary" type="button" data-close-modal>Batal</button>
                                </div>
                            </form>
                        </div>
                    </section>
                </div>
            @empty
                <p class="muted">Belum ada jadwal seminar atau ujian.</p>
            @endforelse
        </div>
    </section>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const openModal = (id) => {
                const modal = document.getElementById(id);
                if (!modal) return;

                modal.hidden = false;
                document.body.classList.add('modal-open');
                modal.querySelector('input, textarea, button')?.focus();
            };

            const closeModal = (modal) => {
                modal.hidden = true;
                document.body.classList.remove('modal-open');
            };

            document.querySelectorAll('[data-open-modal]').forEach((button) => {
                button.addEventListener('click', () => openModal(button.dataset.openModal));
            });

            document.querySelectorAll('[data-close-modal]').forEach((button) => {
                button.addEventListener('click', () => closeModal(button.closest('.modal-backdrop')));
            });

            document.querySelectorAll('.modal-backdrop').forEach((modal) => {
                modal.addEventListener('click', (event) => {
                    if (event.target === modal) {
                        closeModal(modal);
                    }
                });
            });

            document.addEventListener('keydown', (event) => {
                if (event.key !== 'Escape') return;

                document.querySelectorAll('.modal-backdrop:not([hidden])').forEach(closeModal);
            });
        });
    </script>
@endpush
