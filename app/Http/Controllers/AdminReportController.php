<?php

namespace App\Http\Controllers;

use App\Support\GuidanceProgress;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

class AdminReportController extends Controller
{
    public function export(): Response
    {
        abort_if(Auth::user()->role !== 'admin', 403);

        $target = GuidanceProgress::target();
        $filename = 'laporan-simta-'.now()->format('Ymd-His').'.csv';
        $handle = fopen('php://temp', 'r+');

        // BOM helps Excel read UTF-8 Indonesian names and titles correctly.
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
        ], ',', '"', '\\');

        DB::table('students')
            ->orderBy('students.name')
            ->chunk(100, function ($students) use ($handle, $target) {
                foreach ($students as $student) {
                    $pa = GuidanceProgress::forStudent((int) $student->id, 'PA', $target);
                    $ta = GuidanceProgress::forStudent((int) $student->id, 'TA', $target);
                    $paNames = DB::table('pa_assignments')
                        ->join('lecturers', 'lecturers.id', '=', 'pa_assignments.lecturer_id')
                        ->where('pa_assignments.student_id', $student->id)
                        ->orderBy('lecturers.name')
                        ->pluck('lecturers.name')
                        ->unique()
                        ->implode(', ');
                    $taRows = DB::table('thesis_guidances')
                        ->join('lecturers', 'lecturers.id', '=', 'thesis_guidances.lecturer_id')
                        ->where('thesis_guidances.student_id', $student->id)
                        ->orderBy('lecturers.name')
                        ->select('lecturers.name as lecturer_name', 'thesis_guidances.title')
                        ->get();

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
                    ], ',', '"', '\\');
                }
            });

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

    private function cohortFromNim(string $nim): string
    {
        preg_match('/20\d{2}/', $nim, $matches);

        return $matches[0] ?? '-';
    }
}
