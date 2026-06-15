<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class AdminSeminarController extends Controller
{
    public function index(Request $request): View
    {
        $this->ensureAdmin();

        return $this->view($request, null);
    }

    public function edit(Request $request, int $seminar): View
    {
        $this->ensureAdmin();

        $editing = DB::table('seminars')->where('id', $seminar)->first();

        abort_if(! $editing, 404);

        return $this->view($request, $editing);
    }

    public function store(Request $request)
    {
        $this->ensureAdmin();

        $validated = $this->validatedScheduleRequest($request);
        $taRequest = $this->approvedThesisRequestForGuidance((int) $validated['thesis_guidance_id']);

        $hasOpenRequest = DB::table('seminar_requests')
            ->where('thesis_guidance_id', $validated['thesis_guidance_id'])
            ->where('type', $validated['type'])
            ->where('status', 'pending')
            ->exists();

        if ($hasOpenRequest) {
            throw ValidationException::withMessages([
                'type' => 'Masih ada jadwal '.$validated['type'].' yang menunggu persetujuan dosen.',
            ]);
        }

        $hasActiveSchedule = DB::table('seminars')
            ->where('thesis_guidance_id', $validated['thesis_guidance_id'])
            ->where('type', $validated['type'])
            ->where('status', '!=', 'cancelled')
            ->exists();

        if ($hasActiveSchedule) {
            throw ValidationException::withMessages([
                'type' => 'Jadwal '.$validated['type'].' untuk mahasiswa ini sudah ada.',
            ]);
        }

        DB::table('seminar_requests')->insert([
            'student_id' => $taRequest->student_id,
            'thesis_guidance_id' => $validated['thesis_guidance_id'],
            'supervisor_1_id' => $taRequest->supervisor_1_id,
            'supervisor_2_id' => $taRequest->supervisor_2_id,
            'examiner_1_id' => $taRequest->examiner_1_id,
            'examiner_2_id' => $taRequest->examiner_2_id,
            'type' => $validated['type'],
            'proposed_at' => $validated['proposed_at'],
            'room' => $validated['room'] ?? null,
            'student_note' => $validated['note'] ?? null,
            'admin_status' => 'approved',
            'admin_note' => 'Dibuat oleh admin.',
            'admin_decided_at' => now(),
            'status' => 'pending',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->route('admin.seminars')->with('status', 'Pengajuan jadwal berhasil dibuat. Jadwal resmi akan muncul setelah semua pembimbing dan penguji menyetujui.');
    }

    public function update(Request $request, int $seminar)
    {
        $this->ensureAdmin();

        $validated = $this->validated($request);
        $validated = $this->normalizeGradedStatus($validated);
        $this->ensureGuidanceHasFullApproval((int) $validated['thesis_guidance_id']);

        DB::table('seminars')->where('id', $seminar)->update($validated + [
            'updated_at' => now(),
        ]);

        $this->syncGuidanceSeminarStatus((int) $validated['thesis_guidance_id'], $validated['type']);

        return redirect()->route('admin.seminars')->with('status', 'Jadwal seminar berhasil diperbarui.');
    }

    public function decideSeminarRequest(Request $request, int $seminarRequest)
    {
        $this->ensureAdmin();

        throw ValidationException::withMessages([
            'seminar_request' => 'Admin tidak memvalidasi jadwal seminar/ujian. Admin hanya membuat usulan jadwal, lalu pembimbing dan penguji yang menyetujui atau menolak.',
        ]);
    }

    public function destroy(int $seminar)
    {
        $this->ensureAdmin();

        $record = DB::table('seminars')->where('id', $seminar)->first();

        abort_if(! $record, 404);

        DB::table('seminars')->where('id', $seminar)->delete();

        return redirect()->route('admin.seminars')->with('status', 'Jadwal seminar berhasil dihapus.');
    }

    private function view(Request $request, ?object $editing): View
    {
        $query = trim((string) $request->query('q'));

        $seminars = DB::table('seminars')
            ->join('thesis_guidances', 'thesis_guidances.id', '=', 'seminars.thesis_guidance_id')
            ->join('students', 'students.id', '=', 'thesis_guidances.student_id')
            ->join('lecturers', 'lecturers.id', '=', 'thesis_guidances.lecturer_id')
            ->when($query !== '', function ($builder) use ($query) {
                $builder->where(function ($nested) use ($query) {
                    $nested
                        ->where('students.nim', 'like', '%'.$query.'%')
                        ->orWhere('students.name', 'like', '%'.$query.'%')
                        ->orWhere('thesis_guidances.title', 'like', '%'.$query.'%')
                        ->orWhere('seminars.type', 'like', '%'.$query.'%')
                        ->orWhere('seminars.room', 'like', '%'.$query.'%');
                });
            })
            ->select(
                'seminars.*',
                'thesis_guidances.title',
                'students.nim',
                'students.name as student_name',
                'lecturers.name as lecturer_name',
            )
            ->orderByDesc('seminars.scheduled_at')
            ->paginate(12)
            ->withQueryString();

        return view('admin.seminars.index', [
            'seminars' => $seminars,
            'seminarRequests' => $this->seminarRequests(),
            'guidances' => $this->guidanceOptions(),
            'query' => $query,
            'editing' => $editing,
            'types' => ['Seminar Proposal', 'Seminar Hasil', 'Ujian TA'],
            'statuses' => ['scheduled', 'done', 'graded', 'cancelled'],
        ]);
    }

    private function guidanceOptions()
    {
        return DB::table('thesis_guidances')
            ->join('students', 'students.id', '=', 'thesis_guidances.student_id')
            ->join('lecturers', 'lecturers.id', '=', 'thesis_guidances.lecturer_id')
            ->join('thesis_guidance_requests', function ($join) {
                $join->on('thesis_guidance_requests.student_id', '=', 'thesis_guidances.student_id')
                    ->on('thesis_guidance_requests.title', '=', 'thesis_guidances.title')
                    ->where('thesis_guidance_requests.status', '=', 'approved')
                    ->where('thesis_guidance_requests.admin_status', '=', 'approved')
                    ->where('thesis_guidance_requests.supervisor_1_status', '=', 'approved')
                    ->where('thesis_guidance_requests.supervisor_2_status', '=', 'approved')
                    ->where('thesis_guidance_requests.examiner_1_status', '=', 'approved')
                    ->where('thesis_guidance_requests.examiner_2_status', '=', 'approved');
            })
            ->where('thesis_guidances.status', 'active')
            ->select(
                'thesis_guidances.id',
                'thesis_guidances.title',
                'students.nim',
                'students.name as student_name',
                'lecturers.name as lecturer_name',
            )
            ->orderBy('students.name')
            ->get();
    }

    private function seminarRequests()
    {
        return DB::table('seminar_requests')
            ->join('thesis_guidances', 'thesis_guidances.id', '=', 'seminar_requests.thesis_guidance_id')
            ->join('students', 'students.id', '=', 'seminar_requests.student_id')
            ->select(
                'seminar_requests.*',
                'thesis_guidances.title',
                'students.nim',
                'students.name as student_name'
            )
            ->orderByRaw("case when seminar_requests.status = 'pending' then 0 else 1 end")
            ->orderByDesc('seminar_requests.created_at')
            ->get();
    }

    private function ensureGuidanceHasFullApproval(int $guidanceId): void
    {
        $request = $this->latestThesisRequestForGuidance($guidanceId);

        if (! $request) {
            throw ValidationException::withMessages([
                'thesis_guidance_id' => 'Seminar/Ujian belum dapat dijadwalkan karena belum ada pengajuan TA yang disetujui lengkap oleh admin, pembimbing, dan penguji.',
            ]);
        }

        $statuses = [
            'Admin' => $request->admin_status,
            'Pembimbing 1' => $request->supervisor_1_status,
            'Pembimbing 2' => $request->supervisor_2_status,
            'Penguji 1' => $request->examiner_1_status,
            'Penguji 2' => $request->examiner_2_status,
        ];

        $pending = collect($statuses)
            ->filter(fn ($status) => $status !== 'approved')
            ->keys()
            ->implode(', ');

        if ($request->status !== 'approved' || $pending !== '') {
            throw ValidationException::withMessages([
                'thesis_guidance_id' => 'Seminar/Ujian belum dapat dijadwalkan. Persetujuan belum lengkap pada: '.$pending.'.',
            ]);
        }
    }

    private function approvedThesisRequestForGuidance(int $guidanceId): object
    {
        $request = $this->latestThesisRequestForGuidance($guidanceId);

        if (! $request) {
            throw ValidationException::withMessages([
                'thesis_guidance_id' => 'Jadwal belum bisa dibuat karena belum ada pengajuan TA yang terkait dengan bimbingan ini.',
            ]);
        }

        $this->ensureGuidanceHasFullApproval($guidanceId);

        return $request;
    }

    private function latestThesisRequestForGuidance(int $guidanceId): ?object
    {
        $guidance = DB::table('thesis_guidances')->where('id', $guidanceId)->first();

        abort_if(! $guidance, 404);

        return DB::table('thesis_guidance_requests')
            ->where('student_id', $guidance->student_id)
            ->where('title', $guidance->title)
            ->orderByDesc('created_at')
            ->first();
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'thesis_guidance_id' => ['required', 'exists:thesis_guidances,id'],
            'type' => ['required', 'in:Seminar Proposal,Seminar Hasil,Ujian TA'],
            'scheduled_at' => ['required', 'date'],
            'room' => ['nullable', 'max:255'],
            'status' => ['required', 'in:scheduled,done,graded,cancelled'],
            'score' => ['nullable', 'integer', 'min:0', 'max:100'],
            'feedback' => ['nullable', 'max:1000'],
        ]);
    }

    private function validatedScheduleRequest(Request $request): array
    {
        return $request->validate([
            'thesis_guidance_id' => ['required', 'exists:thesis_guidances,id'],
            'type' => ['required', 'in:Seminar Proposal,Seminar Hasil,Ujian TA'],
            'proposed_at' => ['required', 'date'],
            'room' => ['nullable', 'max:255'],
            'note' => ['nullable', 'max:1000'],
        ]);
    }

    private function syncGuidanceSeminarStatus(int $guidanceId, string $type): void
    {
        DB::table('thesis_guidances')->where('id', $guidanceId)->update([
            'seminar_status' => $type,
            'updated_at' => now(),
        ]);
    }

    private function normalizeGradedStatus(array $validated): array
    {
        if ($validated['score'] !== null && $validated['score'] !== '') {
            $validated['status'] = 'graded';
        }

        return $validated;
    }

    private function ensureAdmin(): void
    {
        abort_if(Auth::user()->role !== 'admin', 403);
    }
}
