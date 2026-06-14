<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class AdminBimbinganPaController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorizeAdmin();

        return view('admin.bimbingan-pa.index', [
            'lecturers' => DB::table('lecturers')->orderBy('name')->get(),
            'students' => DB::table('students')->orderBy('name')->get(),
            'assignments' => $this->assignmentQuery()->get(),
            'records' => $this->recordQuery()->get(),
            'consultations' => $this->consultationQuery()->get(),
            'editingAssignment' => $this->editingAssignment($request),
            'editingRecord' => $this->editingRecord($request),
            'editingConsultation' => $this->editingConsultation($request),
        ]);
    }

    public function storeAssignment(Request $request): RedirectResponse
    {
        $this->authorizeAdmin();

        $validated = $request->validate([
            'lecturer_id' => ['required', 'exists:lecturers,id'],
            'student_id' => ['required', 'exists:students,id', Rule::unique('pa_assignments', 'student_id')],
            'academic_year' => ['required', 'max:20'],
            'status' => ['required', 'in:aktif,nonaktif'],
        ]);

        DB::table('pa_assignments')->insert($validated + [
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->route('admin.bimbingan-pa')->with('status', 'Data dosen PA berhasil ditambahkan.');
    }

    public function updateAssignment(Request $request, int $assignment): RedirectResponse
    {
        $this->authorizeAdmin();

        $validated = $request->validate([
            'lecturer_id' => ['required', 'exists:lecturers,id'],
            'student_id' => ['required', 'exists:students,id', Rule::unique('pa_assignments', 'student_id')->ignore($assignment)],
            'academic_year' => ['required', 'max:20'],
            'status' => ['required', 'in:aktif,nonaktif'],
        ]);

        DB::table('pa_assignments')->where('id', $assignment)->update($validated + [
            'updated_at' => now(),
        ]);

        return redirect()->route('admin.bimbingan-pa')->with('status', 'Data dosen PA berhasil diperbarui.');
    }

    public function destroyAssignment(int $assignment): RedirectResponse
    {
        $this->authorizeAdmin();

        DB::table('pa_assignments')->where('id', $assignment)->delete();

        return redirect()->route('admin.bimbingan-pa')->with('status', 'Data dosen PA berhasil dihapus.');
    }

    public function storeRecord(Request $request): RedirectResponse
    {
        $this->authorizeAdmin();

        $validated = $request->validate([
            'student_id' => ['required', 'exists:students,id'],
            'semester' => ['required', 'integer', 'min:1', 'max:14'],
            'ipk' => ['required', 'numeric', 'min:0', 'max:4'],
            'sks_semester' => ['required', 'integer', 'min:0', 'max:30'],
            'sks_total' => ['required', 'integer', 'min:0', 'max:180'],
            'academic_status' => ['required', 'max:50'],
        ]);

        $exists = DB::table('pa_academic_records')
            ->where('student_id', $validated['student_id'])
            ->where('semester', $validated['semester'])
            ->exists();

        if ($exists) {
            return back()->withErrors(['semester' => 'Data semester mahasiswa ini sudah ada.'])->withInput();
        }

        DB::table('pa_academic_records')->insert($validated + [
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->route('admin.bimbingan-pa')->with('status', 'Data IPK/SKS berhasil ditambahkan.');
    }

    public function updateRecord(Request $request, int $record): RedirectResponse
    {
        $this->authorizeAdmin();

        $validated = $request->validate([
            'student_id' => ['required', 'exists:students,id'],
            'semester' => ['required', 'integer', 'min:1', 'max:14'],
            'ipk' => ['required', 'numeric', 'min:0', 'max:4'],
            'sks_semester' => ['required', 'integer', 'min:0', 'max:30'],
            'sks_total' => ['required', 'integer', 'min:0', 'max:180'],
            'academic_status' => ['required', 'max:50'],
        ]);

        $exists = DB::table('pa_academic_records')
            ->where('student_id', $validated['student_id'])
            ->where('semester', $validated['semester'])
            ->where('id', '!=', $record)
            ->exists();

        if ($exists) {
            return back()->withErrors(['semester' => 'Data semester mahasiswa ini sudah ada.'])->withInput();
        }

        DB::table('pa_academic_records')->where('id', $record)->update($validated + [
            'updated_at' => now(),
        ]);

        return redirect()->route('admin.bimbingan-pa')->with('status', 'Data IPK/SKS berhasil diperbarui.');
    }

    public function destroyRecord(int $record): RedirectResponse
    {
        $this->authorizeAdmin();

        DB::table('pa_academic_records')->where('id', $record)->delete();

        return redirect()->route('admin.bimbingan-pa')->with('status', 'Data IPK/SKS berhasil dihapus.');
    }

    public function storeConsultation(Request $request): RedirectResponse
    {
        $this->authorizeAdmin();

        $validated = $this->validateConsultation($request);
        $assignment = DB::table('pa_assignments')->where('id', $validated['pa_assignment_id'])->first();

        DB::table('pa_consultations')->insert($validated + [
            'student_id' => $assignment->student_id,
            'lecturer_id' => $assignment->lecturer_id,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->route('admin.bimbingan-pa')->with('status', 'Data konsultasi PA berhasil ditambahkan.');
    }

    public function updateConsultation(Request $request, int $consultation): RedirectResponse
    {
        $this->authorizeAdmin();

        $validated = $this->validateConsultation($request);
        $assignment = DB::table('pa_assignments')->where('id', $validated['pa_assignment_id'])->first();

        DB::table('pa_consultations')->where('id', $consultation)->update($validated + [
            'student_id' => $assignment->student_id,
            'lecturer_id' => $assignment->lecturer_id,
            'updated_at' => now(),
        ]);

        return redirect()->route('admin.bimbingan-pa')->with('status', 'Data konsultasi PA berhasil diperbarui.');
    }

    public function destroyConsultation(int $consultation): RedirectResponse
    {
        $this->authorizeAdmin();

        DB::table('pa_consultations')->where('id', $consultation)->delete();

        return redirect()->route('admin.bimbingan-pa')->with('status', 'Data konsultasi PA berhasil dihapus.');
    }

    private function validateConsultation(Request $request): array
    {
        return $request->validate([
            'pa_assignment_id' => ['required', 'exists:pa_assignments,id'],
            'topic' => ['required', 'max:150'],
            'student_note' => ['required', 'max:1200'],
            'requested_at' => ['nullable', 'date'],
            'scheduled_at' => ['nullable', 'date'],
            'status' => ['required', 'in:diajukan,dijadwalkan,selesai,dibatalkan'],
            'lecturer_note' => ['nullable', 'max:1500'],
            'recommendation' => ['nullable', 'max:1500'],
        ]);
    }

    private function assignmentQuery()
    {
        return DB::table('pa_assignments')
            ->join('lecturers', 'lecturers.id', '=', 'pa_assignments.lecturer_id')
            ->join('students', 'students.id', '=', 'pa_assignments.student_id')
            ->select('pa_assignments.*', 'lecturers.name as lecturer_name', 'students.name as student_name', 'students.nim')
            ->orderBy('students.name');
    }

    private function recordQuery()
    {
        return DB::table('pa_academic_records')
            ->join('students', 'students.id', '=', 'pa_academic_records.student_id')
            ->select('pa_academic_records.*', 'students.name as student_name', 'students.nim')
            ->orderBy('students.name')
            ->orderByDesc('pa_academic_records.semester');
    }

    private function consultationQuery()
    {
        return DB::table('pa_consultations')
            ->join('students', 'students.id', '=', 'pa_consultations.student_id')
            ->join('lecturers', 'lecturers.id', '=', 'pa_consultations.lecturer_id')
            ->select('pa_consultations.*', 'students.name as student_name', 'students.nim', 'lecturers.name as lecturer_name')
            ->orderByDesc('pa_consultations.created_at');
    }

    private function editingAssignment(Request $request): ?object
    {
        if (! $request->filled('edit_assignment')) {
            return null;
        }

        return DB::table('pa_assignments')->where('id', $request->integer('edit_assignment'))->first();
    }

    private function editingRecord(Request $request): ?object
    {
        if (! $request->filled('edit_record')) {
            return null;
        }

        return DB::table('pa_academic_records')->where('id', $request->integer('edit_record'))->first();
    }

    private function editingConsultation(Request $request): ?object
    {
        if (! $request->filled('edit_consultation')) {
            return null;
        }

        return DB::table('pa_consultations')->where('id', $request->integer('edit_consultation'))->first();
    }

    private function authorizeAdmin(): void
    {
        abort_if(Auth::user()->role !== 'admin', 403);
    }
}
