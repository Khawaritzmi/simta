<?php

namespace App\Http\Controllers;

use App\Support\GuidanceProgress;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class MahasiswaController extends Controller
{
    public function dashboard(): View
    {
        $student = $this->student();
        $guidances = $this->withTaProgress($this->guidanceQuery($student)->get());
        $seminars = $this->seminars($student);

        return view('mahasiswa.dashboard', $this->data($student, [
            'title' => 'Dashboard Mahasiswa',
            'activeMenu' => 'dashboard',
            'guidances' => $guidances,
            'seminars' => $seminars,
        ]));
    }

    public function profile(): View
    {
        $student = $this->student();

        return view('mahasiswa.profile', $this->data($student, [
            'title' => 'Profil Mahasiswa',
            'activeMenu' => 'profile',
        ]));
    }

    public function updateProfile(Request $request): RedirectResponse
    {
        abort_if(Auth::user()->role !== 'mahasiswa', 403);

        $validated = $request->validate([
            'photo' => ['required', 'image', 'mimes:jpg,jpeg,png', 'max:2048'],
        ]);

        $user = $request->user();

        if ($user->profile_photo_path) {
            Storage::disk('public')->delete($user->profile_photo_path);
        }

        $user->forceFill([
            'profile_photo_path' => $validated['photo']->store('photos', 'public'),
        ])->save();

        return redirect()->route('mahasiswa.profile')->with('status', 'Foto profil berhasil diperbarui.');
    }

    public function guidanceRequest(): View
    {
        $student = $this->student();

        return view('mahasiswa.ta-request', $this->data($student, [
            'title' => 'Pengajuan TA',
            'activeMenu' => 'ta.request',
            'lecturers' => DB::table('lecturers')->orderBy('name')->get(),
            'guidanceRequests' => $this->guidanceRequests($student),
        ]));
    }

    public function myThesis(): View
    {
        $student = $this->student();
        $guidances = $this->withTaProgress($this->guidanceQuery($student)->get());

        return view('mahasiswa.ta-mine', $this->data($student, [
            'title' => 'Tugas Akhir Saya',
            'activeMenu' => 'ta.mine',
            'guidances' => $guidances,
            'seminars' => $this->seminars($student),
            'seminarRequests' => $this->seminarRequests($student),
            'uploads' => $this->thesisUploads($student, $guidances),
            'uploadCategories' => $this->uploadCategories(),
            'seminarTypes' => ['Seminar Proposal', 'Seminar Hasil', 'Ujian TA'],
        ]));
    }

    public function pa(): View
    {
        $student = $this->student();
        $records = DB::table('pa_academic_records')
            ->where('student_id', $student->id)
            ->orderByDesc('semester')
            ->get();

        return view('mahasiswa.pa', $this->data($student, [
            'title' => 'Bimbingan PA',
            'activeMenu' => 'pa',
            'paAssignment' => $this->paAssignment($student),
            'paRecords' => $records,
            'latestRecord' => $records->first(),
            'paConsultations' => DB::table('pa_consultations')
                ->where('student_id', $student->id)
                ->orderByDesc('created_at')
                ->get(),
        ]));
    }

    public function storeGuidanceRequest(Request $request): RedirectResponse
    {
        abort_if(Auth::user()->role !== 'mahasiswa', 403);

        $student = $this->student();

        $validated = $request->validate([
            'title' => ['required', 'max:255'],
            'supervisor_1_id' => ['required', 'exists:lecturers,id'],
            'supervisor_2_id' => ['required', 'exists:lecturers,id'],
            'examiner_1_id' => ['required', 'exists:lecturers,id'],
            'examiner_2_id' => ['required', 'exists:lecturers,id'],
        ]);

        $lecturerIds = [
            $validated['supervisor_1_id'],
            $validated['supervisor_2_id'],
            $validated['examiner_1_id'],
            $validated['examiner_2_id'],
        ];

        if (count(array_unique($lecturerIds)) !== 4) {
            throw ValidationException::withMessages([
                'supervisor_1_id' => 'Dosen pembimbing dan penguji harus terdiri dari empat dosen yang berbeda.',
            ]);
        }

        $hasOpenRequest = DB::table('thesis_guidance_requests')
            ->where('student_id', $student->id)
            ->whereIn('status', ['pending', 'approved'])
            ->exists();

        if ($hasOpenRequest) {
            throw ValidationException::withMessages([
                'title' => 'Anda masih memiliki pengajuan Bimbingan TA yang sedang diproses atau sudah disetujui.',
            ]);
        }

        DB::table('thesis_guidance_requests')->insert($validated + [
            'student_id' => $student->id,
            'admin_status' => 'pending',
            'supervisor_1_status' => 'pending',
            'supervisor_2_status' => 'pending',
            'examiner_1_status' => 'pending',
            'examiner_2_status' => 'pending',
            'status' => 'pending',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->route('mahasiswa.guidance-requests.index')->with('status', 'Pengajuan Bimbingan TA berhasil dikirim untuk persetujuan admin dan dosen terkait.');
    }

    public function storeThesisUpload(Request $request): RedirectResponse
    {
        abort_if(Auth::user()->role !== 'mahasiswa', 403);

        $student = $this->student();
        $categories = implode(',', array_keys($this->uploadCategories()));

        $validated = $request->validate([
            'thesis_guidance_id' => ['required', 'exists:thesis_guidances,id'],
            'category' => ['required', "in:{$categories}"],
            'file' => ['required', 'file', 'mimes:pdf', 'max:2048'],
        ]);

        $guidance = DB::table('thesis_guidances')
            ->where('id', $validated['thesis_guidance_id'])
            ->where('student_id', $student->id)
            ->whereIn('status', ['approved', 'active'])
            ->first();

        abort_if(! $guidance, 403, 'Upload hanya tersedia setelah TA disetujui.');

        $existing = DB::table('thesis_uploads')
            ->where('student_id', $student->id)
            ->where('thesis_guidance_id', $guidance->id)
            ->where('category', $validated['category'])
            ->first();

        if ($existing) {
            Storage::disk('public')->delete($existing->path);
        }

        $path = $validated['file']->storeAs(
            "uploads/{$student->id}",
            "{$validated['category']}.pdf",
            'public'
        );

        DB::table('thesis_uploads')->updateOrInsert(
            [
                'student_id' => $student->id,
                'thesis_guidance_id' => $guidance->id,
                'category' => $validated['category'],
            ],
            [
                'path' => $path,
                'original_name' => $validated['file']->getClientOriginalName(),
                'created_at' => $existing?->created_at ?? now(),
                'updated_at' => now(),
            ],
        );

        return redirect()->route('mahasiswa.guidance.mine')->with('status', 'Dokumen TA berhasil diunggah.');
    }

    public function storeSeminarRequest(Request $request): RedirectResponse
    {
        abort_if(Auth::user()->role !== 'mahasiswa', 403);

        $student = $this->student();
        $validated = $request->validate([
            'thesis_guidance_id' => ['required', 'exists:thesis_guidances,id'],
            'type' => ['required', 'in:Seminar Proposal,Seminar Hasil,Ujian TA'],
            'proposed_at' => ['required', 'date'],
            'room' => ['nullable', 'max:255'],
            'student_note' => ['nullable', 'max:1000'],
        ]);

        $guidance = DB::table('thesis_guidances')
            ->where('id', $validated['thesis_guidance_id'])
            ->where('student_id', $student->id)
            ->whereIn('status', ['approved', 'active'])
            ->first();

        abort_if(! $guidance, 403, 'Pengajuan jadwal hanya tersedia setelah TA disetujui.');

        $taRequest = DB::table('thesis_guidance_requests')
            ->where('student_id', $student->id)
            ->where('title', $guidance->title)
            ->where('status', 'approved')
            ->where('admin_status', 'approved')
            ->where('supervisor_1_status', 'approved')
            ->where('supervisor_2_status', 'approved')
            ->where('examiner_1_status', 'approved')
            ->where('examiner_2_status', 'approved')
            ->orderByDesc('created_at')
            ->first();

        if (! $taRequest) {
            throw ValidationException::withMessages([
                'thesis_guidance_id' => 'Pengajuan jadwal belum bisa dikirim karena pengajuan TA belum disetujui lengkap oleh admin, pembimbing, dan penguji.',
            ]);
        }

        $hasPending = DB::table('seminar_requests')
            ->where('thesis_guidance_id', $guidance->id)
            ->where('type', $validated['type'])
            ->where('status', 'pending')
            ->exists();

        if ($hasPending) {
            throw ValidationException::withMessages([
                'type' => 'Masih ada pengajuan jadwal '.$validated['type'].' yang menunggu persetujuan.',
            ]);
        }

        DB::table('seminar_requests')->insert([
            'student_id' => $student->id,
            'thesis_guidance_id' => $guidance->id,
            'supervisor_1_id' => $taRequest->supervisor_1_id,
            'supervisor_2_id' => $taRequest->supervisor_2_id,
            'examiner_1_id' => $taRequest->examiner_1_id,
            'examiner_2_id' => $taRequest->examiner_2_id,
            'type' => $validated['type'],
            'proposed_at' => $validated['proposed_at'],
            'room' => $validated['room'] ?? null,
            'student_note' => $validated['student_note'] ?? null,
            'status' => 'pending',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->route('mahasiswa.guidance.mine')->with('status', 'Pengajuan jadwal seminar/ujian berhasil dikirim untuk validasi admin dan dosen terkait.');
    }

    public function guidance(): View
    {
        $student = $this->student();

        return view('mahasiswa.guidance', $this->data($student, [
            'title' => 'Bimbingan Tugas Akhir',
            'activeMenu' => 'guidance',
            'guidances' => $this->guidanceQuery($student)->get(),
        ]));
    }

    public function repository(): View
    {
        $student = $this->student();
        $documents = DB::table('repositories')
            ->join('thesis_guidances', 'thesis_guidances.id', '=', 'repositories.thesis_guidance_id')
            ->join('lecturers', 'lecturers.id', '=', 'thesis_guidances.lecturer_id')
            ->where('thesis_guidances.student_id', $student->id)
            ->select('repositories.*', 'thesis_guidances.title', 'lecturers.name as lecturer_name')
            ->orderByDesc('repositories.created_at')
            ->get();

        return view('mahasiswa.repository', $this->data($student, [
            'title' => 'Repository Tugas Akhir',
            'activeMenu' => 'repository',
            'documents' => $documents,
        ]));
    }

    public function qa(): View
    {
        $student = $this->student();
        $questions = DB::table('questions')
            ->join('lecturers', 'lecturers.id', '=', 'questions.lecturer_id')
            ->where('questions.student_id', $student->id)
            ->select('questions.*', 'lecturers.name as lecturer_name')
            ->orderByDesc('questions.created_at')
            ->get();

        return view('mahasiswa.qa', $this->data($student, [
            'title' => 'Q & A',
            'activeMenu' => 'qa',
            'questions' => $questions,
        ]));
    }

    private function data(object $student, array $data = []): array
    {
        return $data + [
            'student' => $student,
            'profilePhotoUrl' => $this->profilePhotoUrl(),
        ];
    }

    private function profilePhotoUrl(): ?string
    {
        $user = Auth::user();

        if (! $user->profile_photo_path || ! Storage::disk('public')->exists($user->profile_photo_path)) {
            return null;
        }

        return route('profile-photos.show', [
            'file' => basename($user->profile_photo_path),
            'v' => optional($user->updated_at)->timestamp ?? time(),
        ]);
    }

    private function student(): object
    {
        $student = DB::table('students')->where('user_id', Auth::id())->first();

        abort_if(! $student, 403, 'Profil mahasiswa tidak ditemukan.');

        return $student;
    }

    private function guidanceQuery(object $student)
    {
        return DB::table('thesis_guidances')
            ->join('lecturers', 'lecturers.id', '=', 'thesis_guidances.lecturer_id')
            ->where('thesis_guidances.student_id', $student->id)
            ->select('thesis_guidances.*', 'lecturers.name as lecturer_name', 'lecturers.email as lecturer_email')
            ->orderByDesc('thesis_guidances.updated_at');
    }

    private function guidanceRequests(object $student)
    {
        return DB::table('thesis_guidance_requests')
            ->join('lecturers as supervisor_1', 'supervisor_1.id', '=', 'thesis_guidance_requests.supervisor_1_id')
            ->join('lecturers as supervisor_2', 'supervisor_2.id', '=', 'thesis_guidance_requests.supervisor_2_id')
            ->join('lecturers as examiner_1', 'examiner_1.id', '=', 'thesis_guidance_requests.examiner_1_id')
            ->join('lecturers as examiner_2', 'examiner_2.id', '=', 'thesis_guidance_requests.examiner_2_id')
            ->where('thesis_guidance_requests.student_id', $student->id)
            ->select(
                'thesis_guidance_requests.*',
                'supervisor_1.name as supervisor_1_name',
                'supervisor_2.name as supervisor_2_name',
                'examiner_1.name as examiner_1_name',
                'examiner_2.name as examiner_2_name'
            )
            ->orderByDesc('thesis_guidance_requests.created_at')
            ->get();
    }

    private function paAssignment(object $student): ?object
    {
        return DB::table('pa_assignments')
            ->join('lecturers', 'lecturers.id', '=', 'pa_assignments.lecturer_id')
            ->where('pa_assignments.student_id', $student->id)
            ->select('pa_assignments.*', 'lecturers.name as lecturer_name', 'lecturers.email as lecturer_email', 'lecturers.phone as lecturer_phone')
            ->first();
    }

    private function seminars(object $student)
    {
        return DB::table('seminars')
            ->join('thesis_guidances', 'thesis_guidances.id', '=', 'seminars.thesis_guidance_id')
            ->where('thesis_guidances.student_id', $student->id)
            ->select('seminars.*', 'thesis_guidances.title')
            ->orderByDesc('seminars.scheduled_at')
            ->get();
    }

    private function seminarRequests(object $student)
    {
        return DB::table('seminar_requests')
            ->join('thesis_guidances', 'thesis_guidances.id', '=', 'seminar_requests.thesis_guidance_id')
            ->where('seminar_requests.student_id', $student->id)
            ->select('seminar_requests.*', 'thesis_guidances.title')
            ->orderByDesc('seminar_requests.created_at')
            ->get();
    }

    private function withTaProgress($guidances)
    {
        $target = GuidanceProgress::target();

        return $guidances->map(function ($guidance) use ($target) {
            $guidance->ta_progress = GuidanceProgress::forStudent((int) $guidance->student_id, 'TA', $target);

            return $guidance;
        });
    }

    private function thesisUploads(object $student, $guidances)
    {
        $ids = $guidances->pluck('id')->all();

        if ($ids === []) {
            return collect();
        }

        return DB::table('thesis_uploads')
            ->where('student_id', $student->id)
            ->whereIn('thesis_guidance_id', $ids)
            ->get()
            ->map(function ($upload) {
                $upload->url = Storage::disk('public')->url($upload->path);

                return $upload;
            })
            ->groupBy('thesis_guidance_id')
            ->map(fn ($items) => $items->keyBy('category'));
    }

    private function uploadCategories(): array
    {
        return [
            'proposal' => 'Proposal',
            'hasil' => 'Hasil',
            'sidang' => 'Sidang',
            'surat_persetujuan' => 'Surat Persetujuan',
            'slide' => 'Slide',
        ];
    }
}
