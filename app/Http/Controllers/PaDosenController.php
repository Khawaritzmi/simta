<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class PaDosenController extends Controller
{
    public function dashboard(): View
    {
        $lecturer = $this->lecturer();
        $students = $this->advisedStudents($lecturer);
        $consultations = $this->consultations($lecturer);

        return view('pa.dosen', [
            'lecturer' => $lecturer,
            'students' => $students,
            'consultations' => $consultations,
            'report' => [
                'students' => $students->count(),
                'consultations' => $consultations->count(),
                'pending' => $consultations->whereIn('status', ['diajukan', 'dijadwalkan'])->count(),
                'done' => $consultations->where('status', 'selesai')->count(),
            ],
        ]);
    }

    public function updateConsultation(Request $request, int $consultation): RedirectResponse
    {
        $lecturer = $this->lecturer();

        $exists = DB::table('pa_consultations')
            ->where('id', $consultation)
            ->where('lecturer_id', $lecturer->id)
            ->exists();

        abort_if(! $exists, 404);

        $validated = $request->validate([
            'status' => ['required', 'in:diajukan,dijadwalkan,selesai,dibatalkan'],
            'scheduled_at' => ['nullable', 'date'],
            'lecturer_note' => ['nullable', 'max:1500'],
            'recommendation' => ['nullable', 'max:1500'],
        ]);

        DB::table('pa_consultations')->where('id', $consultation)->update($validated + [
            'updated_at' => now(),
        ]);

        return redirect()->route('pa.dashboard')->with('status', 'Catatan bimbingan PA berhasil disimpan.');
    }

    public function storeMessage(Request $request, int $consultation): RedirectResponse
    {
        $lecturer = $this->lecturer();
        $record = DB::table('pa_consultations')
            ->where('id', $consultation)
            ->where('lecturer_id', $lecturer->id)
            ->first();

        abort_if(! $record, 404);

        $validated = $request->validate([
            'status' => ['required', 'in:diajukan,dijadwalkan,selesai,dibatalkan'],
            'scheduled_at' => ['nullable', 'date'],
            'message' => ['nullable', 'max:1500'],
            'recommendation' => ['nullable', 'max:1500'],
        ]);

        $message = trim(implode("\n\n", array_filter([
            $validated['message'] ?? null,
            ($validated['recommendation'] ?? null) ? 'Rekomendasi: '.$validated['recommendation'] : null,
        ])));

        DB::transaction(function () use ($consultation, $validated, $message): void {
            DB::table('pa_consultations')->where('id', $consultation)->update([
                'status' => $validated['status'],
                'scheduled_at' => $validated['scheduled_at'] ?? null,
                'lecturer_note' => $validated['message'] ?? null,
                'recommendation' => $validated['recommendation'] ?? null,
                'updated_at' => now(),
            ]);

            if ($message !== '') {
                DB::table('pa_consultation_messages')->insert([
                    'pa_consultation_id' => $consultation,
                    'sender_role' => 'dosen',
                    'sender_user_id' => Auth::id(),
                    'message' => $message,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        });

        return redirect()->route('pa.dashboard')->with('status', 'Pesan Bimbingan PA berhasil dikirim.');
    }

    private function lecturer(): object
    {
        $lecturer = DB::table('lecturers')->where('user_id', Auth::id())->first();

        abort_if(! $lecturer, 403, 'Profil dosen tidak ditemukan.');

        return $lecturer;
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

    private function consultations(object $lecturer)
    {
        return DB::table('pa_consultations')
            ->join('students', 'students.id', '=', 'pa_consultations.student_id')
            ->where('pa_consultations.lecturer_id', $lecturer->id)
            ->select('pa_consultations.*', 'students.nim', 'students.name as student_name', 'students.program')
            ->orderByRaw("case when pa_consultations.status = 'diajukan' then 0 when pa_consultations.status = 'dijadwalkan' then 1 else 2 end")
            ->orderByDesc('pa_consultations.created_at')
            ->get();
    }
}
