<?php

namespace App\Http\Controllers;

use App\Support\ThesisGuidanceRequestWorkflow;
use App\Support\GuidanceProgress;
use App\Support\SeminarRequestWorkflow;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class BimbinganController extends Controller
{
    public function dashboard(): View
    {
        $lecturer = $this->lecturer();

        $target = GuidanceProgress::target();
        $active = DB::table('thesis_guidances')
            ->join('students', 'students.id', '=', 'thesis_guidances.student_id')
            ->where('thesis_guidances.lecturer_id', $lecturer->id)
            ->where('thesis_guidances.status', 'active')
            ->select('thesis_guidances.*', 'students.nim', 'students.name as student_name')
            ->orderBy('students.name')
            ->get()
            ->map(function ($guidance) use ($target) {
                $guidance->ta_progress = GuidanceProgress::forStudent((int) $guidance->student_id, 'TA', $target);

                return $guidance;
            });

        return view('bimbingan.dashboard', $this->data($lecturer, [
            'title' => 'Home',
            'activeMenu' => 'dashboard',
            'activeGuidances' => $active,
            'guidanceTarget' => $target,
        ]));
    }

    public function profile(): View
    {
        $lecturer = $this->lecturer();

        return view('bimbingan.profile', $this->data($lecturer, [
            'title' => 'Profil',
            'activeMenu' => 'profile',
        ]));
    }

    public function updateProfile(Request $request)
    {
        $validated = $request->validate([
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'max:50'],
            'address' => ['nullable', 'max:500'],
            'expertise' => ['required', 'max:255'],
            'employment_status' => ['required', 'max:255'],
            'photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:2048'],
        ]);

        $photo = $validated['photo'] ?? null;
        unset($validated['photo']);

        if ($photo) {
            $user = $request->user();

            if ($user->profile_photo_path) {
                Storage::disk('public')->delete($user->profile_photo_path);
            }

            $user->forceFill([
                'profile_photo_path' => $photo->store('photos', 'public'),
            ])->save();
        }

        DB::table('lecturers')->where('id', $this->lecturer()->id)->update($validated + [
            'updated_at' => now(),
        ]);

        return redirect()->route('profile')->with('status', $photo ? 'Profil dan foto berhasil diperbarui.' : 'Profil berhasil diperbarui.');
    }

    public function decideGuidanceRequest(Request $request, int $guidanceRequest, ThesisGuidanceRequestWorkflow $workflow): RedirectResponse
    {
        abort_if(Auth::user()->role !== 'dosen', 403);

        $validated = $request->validate([
            'status' => ['required', 'in:approved,rejected'],
            'note' => ['required_if:status,rejected', 'nullable', 'max:500'],
        ]);

        $decided = $workflow->decideByLecturer(
            $guidanceRequest,
            $this->lecturer()->id,
            $validated['status'],
            $validated['note'] ?? null
        );

        abort_if(! $decided, 404);

        return redirect()->route('guidance-requests.index')->with('status', 'Status pengajuan Bimbingan TA berhasil disimpan.');
    }

    public function guidanceRequests(): View
    {
        $lecturer = $this->lecturer();

        return view('bimbingan.ta-requests', $this->data($lecturer, [
            'title' => 'List Pengajuan TA',
            'activeMenu' => 'ta.requests',
            'guidanceRequests' => $this->guidanceRequestQuery($lecturer),
            'roleLabels' => ThesisGuidanceRequestWorkflow::ROLE_LABELS,
        ]));
    }

    public function myGuidances(Request $request): View
    {
        $lecturer = $this->lecturer();

        $query = DB::table('thesis_guidances')
            ->join('students', 'students.id', '=', 'thesis_guidances.student_id')
            ->where('thesis_guidances.lecturer_id', $lecturer->id);

        foreach (['nim' => 'students.nim', 'nama' => 'students.name', 'judul' => 'thesis_guidances.title'] as $input => $column) {
            if ($request->filled($input)) {
                $query->where($column, 'like', '%'.$request->string($input).'%');
            }
        }

        $target = GuidanceProgress::target();
        $guidances = $query
            ->select('thesis_guidances.*', 'students.nim', 'students.name as student_name')
            ->orderByDesc('thesis_guidances.updated_at')
            ->get()
            ->map(function ($guidance) use ($target) {
                $guidance->ta_progress = GuidanceProgress::forStudent((int) $guidance->student_id, 'TA', $target);

                return $guidance;
            });

        return view('bimbingan.ta-mine', $this->data($lecturer, [
            'title' => 'Bimbingan TA Saya',
            'activeMenu' => 'ta.mine',
            'guidances' => $guidances,
            'filters' => $request->only(['nim', 'nama', 'judul']),
        ]));
    }

    public function storeGuidanceLog(Request $request): RedirectResponse
    {
        abort_if(! in_array(Auth::user()->role, ['dosen', 'examiner'], true), 403);

        $lecturer = $this->lecturer();
        $validated = $request->validate([
            'student_id' => ['required', 'exists:students,id'],
            'type' => ['required', 'in:PA,TA'],
            'completed_at' => ['required', 'date'],
            'notes' => ['nullable', 'max:1000'],
        ]);

        $authorized = $validated['type'] === 'TA'
            ? DB::table('thesis_guidances')
                ->where('student_id', $validated['student_id'])
                ->where('lecturer_id', $lecturer->id)
                ->exists()
            : DB::table('pa_assignments')
                ->where('student_id', $validated['student_id'])
                ->where('lecturer_id', $lecturer->id)
                ->exists();

        abort_if(! $authorized, 403);

        DB::table('guidances')->insert([
            'student_id' => $validated['student_id'],
            'type' => $validated['type'],
            'completed_at' => $validated['completed_at'],
            'notes' => $validated['notes'] ?? null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return back()->with('status', 'Catatan bimbingan selesai berhasil ditambahkan.');
    }

    public function pa(): View
    {
        $lecturer = $this->lecturer();
        $paStudents = $this->advisedStudents($lecturer);
        $paConsultations = $this->paConsultations($lecturer);
        $paMessages = $this->paMessages($paConsultations->pluck('id')->all());

        return view('bimbingan.pa', $this->data($lecturer, [
            'title' => 'Bimbingan PA',
            'activeMenu' => 'pa',
            'paStudents' => $paStudents,
            'paConsultations' => $paConsultations,
            'paMessages' => $paMessages,
            'paReport' => [
                'students' => $paStudents->count(),
                'consultations' => $paConsultations->count(),
                'pending' => $paConsultations->whereIn('status', ['diajukan', 'dijadwalkan'])->count(),
                'done' => $paConsultations->where('status', 'selesai')->count(),
            ],
        ]));
    }

    public function guidance(Request $request): View
    {
        $lecturer = $this->lecturer();

        $query = DB::table('thesis_guidances')
            ->join('students', 'students.id', '=', 'thesis_guidances.student_id')
            ->where('thesis_guidances.lecturer_id', $lecturer->id);

        foreach (['nim' => 'students.nim', 'nama' => 'students.name', 'judul' => 'thesis_guidances.title'] as $input => $column) {
            if ($request->filled($input)) {
                $query->where($column, 'like', '%'.$request->string($input).'%');
            }
        }

        return view('bimbingan.guidance', $this->data($lecturer, [
            'title' => 'Bimbingan Tugas Akhir',
            'activeMenu' => 'guidance',
            'guidances' => $query
                ->select('thesis_guidances.*', 'students.nim', 'students.name as student_name')
                ->orderByDesc('thesis_guidances.updated_at')
                ->get(),
            'filters' => $request->only(['nim', 'nama', 'judul']),
        ]));
    }

    public function approvals(): View
    {
        $lecturer = $this->lecturer();

        $approvals = DB::table('approvals')
            ->join('thesis_guidances', 'thesis_guidances.id', '=', 'approvals.thesis_guidance_id')
            ->join('students', 'students.id', '=', 'thesis_guidances.student_id')
            ->where('thesis_guidances.lecturer_id', $lecturer->id)
            ->where('approvals.status', 'pending')
            ->select('approvals.*', 'thesis_guidances.title', 'students.nim', 'students.name as student_name')
            ->orderBy('approvals.created_at')
            ->get();

        return view('bimbingan.approvals', $this->data($lecturer, [
            'title' => 'Persetujuan',
            'activeMenu' => 'approvals',
            'approvals' => $approvals,
        ]));
    }

    public function decideApproval(Request $request, int $approval)
    {
        $validated = $request->validate([
            'status' => ['required', 'in:approved,rejected'],
            'note' => ['nullable', 'max:500'],
        ]);

        DB::table('approvals')->where('id', $approval)->update($validated + [
            'decided_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->route('approvals')->with('status', 'Status persetujuan berhasil disimpan.');
    }

    public function seminars(): View
    {
        $lecturer = $this->lecturer();

        $seminars = DB::table('seminars')
            ->join('thesis_guidances', 'thesis_guidances.id', '=', 'seminars.thesis_guidance_id')
            ->join('students', 'students.id', '=', 'thesis_guidances.student_id')
            ->leftJoin('examiner_thesis', 'examiner_thesis.thesis_guidance_id', '=', 'thesis_guidances.id')
            ->where(function ($query) use ($lecturer) {
                $query->where('thesis_guidances.lecturer_id', $lecturer->id)
                    ->orWhere('examiner_thesis.examiner_user_id', Auth::id());
            })
            ->select(
                'seminars.*',
                'thesis_guidances.title',
                'thesis_guidances.id as thesis_guidance_id',
                'students.id as student_id',
                'students.nim',
                'students.name as student_name'
            )
            ->orderByDesc('seminars.scheduled_at')
            ->get();

        return view('bimbingan.seminars', $this->data($lecturer, [
            'title' => 'Seminar / Ujian',
            'activeMenu' => 'seminars',
            'seminarRequests' => $this->seminarRequests($lecturer),
            'seminars' => $this->withThesisUploads($seminars),
            'uploadCategories' => $this->uploadCategories(),
        ]));
    }

    public function decideSeminarRequest(Request $request, int $seminarRequest, SeminarRequestWorkflow $workflow): RedirectResponse
    {
        abort_if(! in_array(Auth::user()->role, ['dosen', 'examiner'], true), 403);

        $validated = $request->validate([
            'status' => ['required', 'in:approved,rejected'],
            'note' => ['required_if:status,rejected', 'nullable', 'max:500'],
        ]);

        $decided = $workflow->decideByLecturer(
            $seminarRequest,
            $this->lecturer()->id,
            $validated['status'],
            $validated['note'] ?? null
        );

        abort_if(! $decided, 404);

        return redirect()->route('seminars')->with('status', 'Status pengajuan jadwal seminar/ujian berhasil disimpan.');
    }

    public function gradeSeminar(Request $request, int $seminar)
    {
        $seminarRecord = DB::table('seminars')
            ->join('thesis_guidances', 'thesis_guidances.id', '=', 'seminars.thesis_guidance_id')
            ->leftJoin('examiner_thesis', 'examiner_thesis.thesis_guidance_id', '=', 'thesis_guidances.id')
            ->where('seminars.id', $seminar)
            ->where(function ($query) {
                $query->where('thesis_guidances.lecturer_id', $this->lecturer()->id)
                    ->orWhere('examiner_thesis.examiner_user_id', Auth::id());
            })
            ->select('seminars.id')
            ->first();

        abort_if(! $seminarRecord, 403);

        $validated = $request->validate([
            'score' => ['required', 'integer', 'min:0', 'max:100'],
            'feedback' => ['nullable', 'max:1000'],
        ]);

        DB::table('seminars')->where('id', $seminar)->update($validated + [
            'status' => 'graded',
            'updated_at' => now(),
        ]);

        return redirect()->route('seminars')->with('status', 'Nilai seminar berhasil disimpan.');
    }

    public function repository(): View
    {
        $lecturer = $this->lecturer();

        $documents = DB::table('repositories')
            ->join('thesis_guidances', 'thesis_guidances.id', '=', 'repositories.thesis_guidance_id')
            ->join('students', 'students.id', '=', 'thesis_guidances.student_id')
            ->where('thesis_guidances.lecturer_id', $lecturer->id)
            ->select('repositories.*', 'thesis_guidances.title', 'students.nim', 'students.name as student_name')
            ->orderByDesc('repositories.created_at')
            ->get();

        return view('bimbingan.repository', $this->data($lecturer, [
            'title' => 'Repository',
            'activeMenu' => 'repository',
            'documents' => $documents,
            'guidances' => $this->guidanceOptions($lecturer),
        ]));
    }

    public function storeRepository(Request $request)
    {
        $validated = $request->validate([
            'thesis_guidance_id' => ['required', 'exists:thesis_guidances,id'],
            'document_type' => ['required', 'max:100'],
            'file_name' => ['required', 'max:255'],
            'url' => ['nullable', 'url', 'max:255'],
        ]);

        DB::table('repositories')->insert($validated + [
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->route('repository')->with('status', 'Dokumen repository berhasil ditambahkan.');
    }

    public function qa(): View
    {
        $lecturer = $this->lecturer();

        $questions = DB::table('questions')
            ->leftJoin('students', 'students.id', '=', 'questions.student_id')
            ->where('questions.lecturer_id', $lecturer->id)
            ->select('questions.*', 'students.nim', 'students.name as student_name')
            ->orderByDesc('questions.created_at')
            ->get();

        return view('bimbingan.qa', $this->data($lecturer, [
            'title' => 'Q & A',
            'activeMenu' => 'qa',
            'questions' => $questions,
        ]));
    }

    public function answerQuestion(Request $request, int $question)
    {
        $validated = $request->validate([
            'answer' => ['required', 'max:1500'],
        ]);

        DB::table('questions')->where('id', $question)->update($validated + [
            'answered_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->route('qa')->with('status', 'Jawaban berhasil dikirim.');
    }

    public function manuals(): View
    {
        return view('bimbingan.manuals', $this->data($this->lecturer(), [
            'title' => 'Manual Aplikasi',
            'activeMenu' => 'manuals',
        ]));
    }

    private function data(object $lecturer, array $data = []): array
    {
        return $data + [
            'lecturer' => $lecturer,
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

    private function lecturer(): object
    {
        $lecturer = DB::table('lecturers')->where('user_id', Auth::id())->first();

        abort_if(! $lecturer, 403, 'Profil dosen tidak ditemukan.');

        return $lecturer;
    }

    private function guidanceOptions(object $lecturer)
    {
        return DB::table('thesis_guidances')
            ->join('students', 'students.id', '=', 'thesis_guidances.student_id')
            ->where('thesis_guidances.lecturer_id', $lecturer->id)
            ->select('thesis_guidances.*', 'students.nim', 'students.name as student_name')
            ->orderBy('students.name')
            ->get();
    }

    private function withThesisUploads($seminars)
    {
        $guidanceIds = $seminars->pluck('thesis_guidance_id')->unique()->values()->all();

        if ($guidanceIds === []) {
            return $seminars;
        }

        $uploads = DB::table('thesis_uploads')
            ->whereIn('thesis_guidance_id', $guidanceIds)
            ->get()
            ->map(function ($upload) {
                $upload->url = route('thesis-uploads.show', $upload->id);

                return $upload;
            })
            ->groupBy('thesis_guidance_id')
            ->map(fn ($items) => $items->keyBy('category'));

        return $seminars->map(function ($seminar) use ($uploads) {
            $seminar->uploads = $uploads->get($seminar->thesis_guidance_id, collect());

            return $seminar;
        });
    }

    private function seminarRequests(object $lecturer)
    {
        return DB::table('seminar_requests')
            ->join('thesis_guidances', 'thesis_guidances.id', '=', 'seminar_requests.thesis_guidance_id')
            ->join('students', 'students.id', '=', 'seminar_requests.student_id')
            ->where(function ($query) use ($lecturer) {
                $query->where('seminar_requests.supervisor_1_id', $lecturer->id)
                    ->orWhere('seminar_requests.supervisor_2_id', $lecturer->id)
                    ->orWhere('seminar_requests.examiner_1_id', $lecturer->id)
                    ->orWhere('seminar_requests.examiner_2_id', $lecturer->id);
            })
            ->select(
                'seminar_requests.*',
                'thesis_guidances.title',
                'students.nim',
                'students.name as student_name'
            )
            ->orderByRaw("case when seminar_requests.status = 'pending' then 0 else 1 end")
            ->orderByDesc('seminar_requests.created_at')
            ->get()
            ->map(function ($item) use ($lecturer) {
                $workflow = app(SeminarRequestWorkflow::class);
                $role = $workflow->lecturerRole($item, $lecturer->id);

                $item->current_role = $role;
                $item->current_role_label = $role ? SeminarRequestWorkflow::ROLE_LABELS[$role] : '-';
                $item->current_status = $role ? $item->{"{$role}_status"} : '-';
                $item->current_note = $role ? $item->{"{$role}_note"} : null;

                return $item;
            });
    }

    private function uploadCategories(): array
    {
        return [
            'proposal' => 'Proposal',
            'hasil' => 'Hasil/Skripsi',
            'sidang' => 'Sidang/Ujian',
            'surat_persetujuan' => 'Surat Persetujuan',
            'slide' => 'Slide',
        ];
    }

    private function guidanceRequestQuery(object $lecturer)
    {
        $workflow = app(ThesisGuidanceRequestWorkflow::class);

        return DB::table('thesis_guidance_requests')
            ->join('students', 'students.id', '=', 'thesis_guidance_requests.student_id')
            ->join('lecturers as supervisor_1', 'supervisor_1.id', '=', 'thesis_guidance_requests.supervisor_1_id')
            ->join('lecturers as supervisor_2', 'supervisor_2.id', '=', 'thesis_guidance_requests.supervisor_2_id')
            ->join('lecturers as examiner_1', 'examiner_1.id', '=', 'thesis_guidance_requests.examiner_1_id')
            ->join('lecturers as examiner_2', 'examiner_2.id', '=', 'thesis_guidance_requests.examiner_2_id')
            ->where(function ($query) use ($lecturer) {
                $query->where('supervisor_1_id', $lecturer->id)
                    ->orWhere('supervisor_2_id', $lecturer->id)
                    ->orWhere('examiner_1_id', $lecturer->id)
                    ->orWhere('examiner_2_id', $lecturer->id);
            })
            ->select(
                'thesis_guidance_requests.*',
                'students.nim',
                'students.name as student_name',
                'supervisor_1.name as supervisor_1_name',
                'supervisor_2.name as supervisor_2_name',
                'examiner_1.name as examiner_1_name',
                'examiner_2.name as examiner_2_name'
            )
            ->orderByRaw("case when thesis_guidance_requests.status = 'pending' then 0 else 1 end")
            ->orderByDesc('thesis_guidance_requests.created_at')
            ->get()
            ->map(function ($item) use ($lecturer, $workflow) {
                $role = $workflow->lecturerRole($item, $lecturer->id);

                $item->current_role = $role;
                $item->current_role_label = $role ? ThesisGuidanceRequestWorkflow::ROLE_LABELS[$role] : '-';
                $item->current_status = $role ? $item->{"{$role}_status"} : '-';
                $item->current_note = $role ? $item->{"{$role}_note"} : null;

                return $item;
            });
    }

    private function advisedStudents(object $lecturer)
    {
        return DB::table('pa_assignments')
            ->join('students', 'students.id', '=', 'pa_assignments.student_id')
            ->leftJoin('pa_academic_records', function ($join) {
                $join->on('pa_academic_records.student_id', '=', 'students.id')
                    ->whereRaw('pa_academic_records.semester = (
                        select max(records.semester)
                        from pa_academic_records as records
                        where records.student_id = students.id
                    )');
            })
            ->where('pa_assignments.lecturer_id', $lecturer->id)
            ->select(
                'pa_assignments.id as assignment_id',
                'pa_assignments.academic_year',
                'students.id',
                'students.nim',
                'students.name',
                'students.program',
                'students.email',
                'pa_academic_records.semester',
                'pa_academic_records.ipk',
                'pa_academic_records.sks_total',
                'pa_academic_records.academic_status'
            )
            ->orderBy('students.name')
            ->get();
    }

    private function paConsultations(object $lecturer)
    {
        return DB::table('pa_consultations')
            ->join('students', 'students.id', '=', 'pa_consultations.student_id')
            ->where('pa_consultations.lecturer_id', $lecturer->id)
            ->select('pa_consultations.*', 'students.nim', 'students.name as student_name', 'students.program')
            ->orderByRaw("case when pa_consultations.status = 'diajukan' then 0 when pa_consultations.status = 'dijadwalkan' then 1 else 2 end")
            ->orderByDesc('pa_consultations.created_at')
            ->get();
    }

    private function paMessages(array $consultationIds)
    {
        if ($consultationIds === [] || ! Schema::hasTable('pa_consultation_messages')) {
            return collect();
        }

        return DB::table('pa_consultation_messages')
            ->whereIn('pa_consultation_id', $consultationIds)
            ->orderBy('created_at')
            ->get()
            ->groupBy('pa_consultation_id');
    }
}
