<?php

namespace App\Http\Controllers;

use App\Support\GuidanceProgress;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

class DosenReportController extends Controller
{
    public function export(): Response
    {
        abort_if(! in_array(Auth::user()->role, ['dosen', 'examiner'], true), 403);

        $lecturer = DB::table('lecturers')->where('user_id', Auth::id())->first();

        abort_if(! $lecturer, 403, 'Profil dosen tidak ditemukan.');

        $target = GuidanceProgress::target();
        $filename = 'laporan-bimbingan-dosen-'.now()->format('Ymd-His').'.csv';
        $handle = fopen('php://temp', 'r+');

        fwrite($handle, "\xEF\xBB\xBF");

        fputcsv($handle, [
            'Nama Mahasiswa',
            'Prodi',
            'Dosen PA',
            'Dosen TA',
            'Email',
            'Progress PA',
            'Progress TA',
            'Angkatan',
            'Judul Tugas Akhir',
            'Peran Dosen',
        ], ',', '"', '\\');

        foreach ($this->studentsForLecturer((int) $lecturer->id) as $student) {
            $pa = GuidanceProgress::forStudent((int) $student->id, 'PA', $target);
            $ta = GuidanceProgress::forStudent((int) $student->id, 'TA', $target);
            $paNames = $this->paNames((int) $student->id);
            $taRows = $this->taRows((int) $student->id);

            fputcsv($handle, [
                $student->name,
                $student->program,
                $paNames ?: '-',
                $taRows->pluck('lecturer_name')->unique()->implode(', ') ?: '-',
                $student->email,
                $pa['fraction'],
                $ta['fraction'],
                $this->cohortFromNim((string) $student->nim),
                $taRows->pluck('title')->unique()->implode(' | ') ?: '-',
                $student->roles ?: '-',
            ], ',', '"', '\\');
        }

        rewind($handle);
        $csv = stream_get_contents($handle);
        fclose($handle);

        $disposition = (new ResponseHeaderBag())->makeDisposition(
            ResponseHeaderBag::DISPOSITION_ATTACHMENT,
            $filename
        );

        return response($csv, 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => $disposition,
            'Content-Length' => (string) strlen($csv),
            'Content-Transfer-Encoding' => 'binary',
            'X-Content-Type-Options' => 'nosniff',
            'Cache-Control' => 'no-store, no-cache, must-revalidate, max-age=0',
            'Pragma' => 'no-cache',
            'Expires' => '0',
        ]);
    }

    private function studentsForLecturer(int $lecturerId)
    {
        return DB::query()
            ->fromSub(function ($query) use ($lecturerId) {
                $query->from('students')
                    ->leftJoin('pa_assignments', function ($join) use ($lecturerId) {
                        $join->on('pa_assignments.student_id', '=', 'students.id')
                            ->where('pa_assignments.lecturer_id', $lecturerId);
                    })
                    ->leftJoin('thesis_guidances', function ($join) use ($lecturerId) {
                        $join->on('thesis_guidances.student_id', '=', 'students.id')
                            ->where('thesis_guidances.lecturer_id', $lecturerId);
                    })
                    ->where(function ($where) {
                        $where->whereNotNull('pa_assignments.id')
                            ->orWhereNotNull('thesis_guidances.id');
                    })
                    ->select(
                        'students.id',
                        'students.nim',
                        'students.name',
                        'students.program',
                        'students.email',
                        DB::raw("max(case when pa_assignments.id is not null then 1 else 0 end) as is_pa"),
                        DB::raw("max(case when thesis_guidances.id is not null then 1 else 0 end) as is_ta")
                    )
                    ->groupBy('students.id', 'students.nim', 'students.name', 'students.program', 'students.email');
            }, 'student_scope')
            ->orderBy('name')
            ->get()
            ->map(function ($student) {
                $roles = [];

                if ((int) $student->is_pa === 1) {
                    $roles[] = 'Dosen PA';
                }

                if ((int) $student->is_ta === 1) {
                    $roles[] = 'Dosen TA';
                }

                $student->roles = implode(', ', $roles);

                return $student;
            });
    }

    private function paNames(int $studentId): string
    {
        return DB::table('pa_assignments')
            ->join('lecturers', 'lecturers.id', '=', 'pa_assignments.lecturer_id')
            ->where('pa_assignments.student_id', $studentId)
            ->orderBy('lecturers.name')
            ->pluck('lecturers.name')
            ->unique()
            ->implode(', ');
    }

    private function taRows(int $studentId)
    {
        return DB::table('thesis_guidances')
            ->join('lecturers', 'lecturers.id', '=', 'thesis_guidances.lecturer_id')
            ->where('thesis_guidances.student_id', $studentId)
            ->orderBy('lecturers.name')
            ->select('lecturers.name as lecturer_name', 'thesis_guidances.title')
            ->get();
    }

    private function cohortFromNim(string $nim): string
    {
        preg_match('/20\d{2}/', $nim, $matches);

        return $matches[0] ?? '-';
    }
}
