<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class MahasiswaController extends Controller
{
    public function dashboard(): View
    {
        $student = $this->student();
        $guidances = $this->guidanceQuery($student)->get();
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
        return view('mahasiswa.profile', $this->data($this->student(), [
            'title' => 'Profil Mahasiswa',
            'activeMenu' => 'profile',
        ]));
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
        ];
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

    private function seminars(object $student)
    {
        return DB::table('seminars')
            ->join('thesis_guidances', 'thesis_guidances.id', '=', 'seminars.thesis_guidance_id')
            ->where('thesis_guidances.student_id', $student->id)
            ->select('seminars.*', 'thesis_guidances.title')
            ->orderByDesc('seminars.scheduled_at')
            ->get();
    }
}
