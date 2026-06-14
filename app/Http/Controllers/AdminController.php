<?php

namespace App\Http\Controllers;

use App\Support\ThesisGuidanceRequestWorkflow;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class AdminController extends Controller
{
    public function dashboard(): View
    {
        $this->ensureAdmin();

        $guidanceRequests = $this->guidanceRequests();

        return view('admin.dashboard', [
            'admin' => Auth::user(),
            'guidanceRequests' => $guidanceRequests,
            'roleLabels' => ThesisGuidanceRequestWorkflow::ROLE_LABELS,
            'paAssignments' => $this->paAssignments(),
            'stats' => [
                'ta_requests' => $guidanceRequests->count(),
                'ta_pending' => $guidanceRequests->where('status', 'pending')->count(),
                'pa_assignments' => DB::table('pa_assignments')->count(),
                'pa_consultations' => DB::table('pa_consultations')->count(),
                'students' => DB::table('students')->count(),
                'lecturers' => DB::table('lecturers')->count(),
            ],
        ]);
    }

    public function decideGuidanceRequest(Request $request, int $guidanceRequest, ThesisGuidanceRequestWorkflow $workflow): RedirectResponse
    {
        $this->ensureAdmin();

        $validated = $request->validate([
            'status' => ['required', 'in:approved,rejected'],
            'note' => ['required_if:status,rejected', 'nullable', 'max:500'],
        ]);

        $exists = DB::table('thesis_guidance_requests')->where('id', $guidanceRequest)->exists();

        abort_if(! $exists, 404);

        $workflow->decideByAdmin($guidanceRequest, $validated['status'], $validated['note'] ?? null);

        return redirect()->route('admin.profile')->with('status', 'Status pengajuan Bimbingan TA berhasil disimpan.');
    }

    private function guidanceRequests()
    {
        return DB::table('thesis_guidance_requests')
            ->join('students', 'students.id', '=', 'thesis_guidance_requests.student_id')
            ->join('lecturers as supervisor_1', 'supervisor_1.id', '=', 'thesis_guidance_requests.supervisor_1_id')
            ->join('lecturers as supervisor_2', 'supervisor_2.id', '=', 'thesis_guidance_requests.supervisor_2_id')
            ->join('lecturers as examiner_1', 'examiner_1.id', '=', 'thesis_guidance_requests.examiner_1_id')
            ->join('lecturers as examiner_2', 'examiner_2.id', '=', 'thesis_guidance_requests.examiner_2_id')
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
            ->get();
    }

    private function paAssignments()
    {
        return DB::table('pa_assignments')
            ->join('lecturers', 'lecturers.id', '=', 'pa_assignments.lecturer_id')
            ->join('students', 'students.id', '=', 'pa_assignments.student_id')
            ->select('pa_assignments.*', 'lecturers.name as lecturer_name', 'students.name as student_name', 'students.nim')
            ->orderBy('students.name')
            ->get();
    }

    private function ensureAdmin(): void
    {
        abort_if(Auth::user()->role !== 'admin', 403);
    }
}
