<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class BimbinganController extends Controller
{
    public function dashboard(): View
    {
        $lecturer = $this->lecturer();

        $active = DB::table('thesis_guidances')
            ->join('students', 'students.id', '=', 'thesis_guidances.student_id')
            ->where('thesis_guidances.lecturer_id', $lecturer->id)
            ->where('thesis_guidances.status', 'active')
            ->select('thesis_guidances.*', 'students.nim', 'students.name as student_name')
            ->orderBy('students.name')
            ->get();

        return view('bimbingan.dashboard', $this->data($lecturer, [
            'title' => 'Home',
            'activeMenu' => 'dashboard',
            'activeGuidances' => $active,
        ]));
    }

    public function profile(): View
    {
        return view('bimbingan.profile', $this->data($this->lecturer(), [
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
        ]);

        DB::table('lecturers')->where('id', $this->lecturer()->id)->update($validated + [
            'updated_at' => now(),
        ]);

        return redirect()->route('profile')->with('status', 'Profil berhasil diperbarui.');
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
            ->where('thesis_guidances.lecturer_id', $lecturer->id)
            ->select('seminars.*', 'thesis_guidances.title', 'students.nim', 'students.name as student_name')
            ->orderByDesc('seminars.scheduled_at')
            ->get();

        return view('bimbingan.seminars', $this->data($lecturer, [
            'title' => 'Seminar / Ujian',
            'activeMenu' => 'seminars',
            'seminars' => $seminars,
        ]));
    }

    public function gradeSeminar(Request $request, int $seminar)
    {
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
        ];
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
            ->select('thesis_guidances.id', 'thesis_guidances.title', 'students.name as student_name')
            ->orderBy('students.name')
            ->get();
    }
}
