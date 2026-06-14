<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class PaMahasiswaController extends Controller
{
    public function dashboard(): View
    {
        abort_if(Auth::user()->role !== 'mahasiswa', 403);

        $student = $this->student();
        $assignment = $this->assignment($student);

        $records = DB::table('pa_academic_records')
            ->where('student_id', $student->id)
            ->orderByDesc('semester')
            ->get();

        $consultations = DB::table('pa_consultations')
            ->where('student_id', $student->id)
            ->orderByDesc('created_at')
            ->get();

        return view('pa.mahasiswa', [
            'student' => $student,
            'assignment' => $assignment,
            'records' => $records,
            'latestRecord' => $records->first(),
            'consultations' => $consultations,
        ]);
    }

    public function storeConsultation(Request $request): RedirectResponse
    {
        abort_if(Auth::user()->role !== 'mahasiswa', 403);

        $student = $this->student();
        $assignment = $this->assignment($student);

        $validated = $request->validate([
            'topic' => ['required', 'max:150'],
            'student_note' => ['required', 'max:1200'],
            'requested_at' => ['nullable', 'date'],
        ]);

        DB::table('pa_consultations')->insert($validated + [
            'pa_assignment_id' => $assignment->id,
            'student_id' => $student->id,
            'lecturer_id' => $assignment->lecturer_id,
            'status' => 'diajukan',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->route('pa.mahasiswa.dashboard')->with('status', 'Pengajuan konsultasi PA berhasil dikirim.');
    }

    private function student(): object
    {
        $student = DB::table('students')->where('user_id', Auth::id())->first();

        abort_if(! $student, 403, 'Profil mahasiswa tidak ditemukan.');

        return $student;
    }

    private function assignment(object $student): object
    {
        $assignment = DB::table('pa_assignments')
            ->join('lecturers', 'lecturers.id', '=', 'pa_assignments.lecturer_id')
            ->where('pa_assignments.student_id', $student->id)
            ->select('pa_assignments.*', 'lecturers.name as lecturer_name', 'lecturers.email as lecturer_email', 'lecturers.phone as lecturer_phone')
            ->first();

        abort_if(! $assignment, 404, 'Dosen PA belum ditetapkan.');

        return $assignment;
    }
}
