<?php

namespace App\Http\Controllers;

use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ThesisUploadController extends Controller
{
    public function show(int $upload): Response
    {
        $record = DB::table('thesis_uploads')
            ->join('thesis_guidances', 'thesis_guidances.id', '=', 'thesis_uploads.thesis_guidance_id')
            ->where('thesis_uploads.id', $upload)
            ->select(
                'thesis_uploads.*',
                'thesis_guidances.lecturer_id',
                'thesis_guidances.student_id as guidance_student_id',
            )
            ->first();

        abort_if(! $record, 404);
        abort_unless($this->canOpen($record), 403);
        abort_unless(Storage::disk('public')->exists($record->path), 404);

        $fileName = str_replace(['"', "\r", "\n"], '', $record->original_name ?: basename($record->path));

        return response(Storage::disk('public')->get($record->path), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="'.$fileName.'"',
            'Cache-Control' => 'private, max-age=0, no-cache',
        ]);
    }

    private function canOpen(object $record): bool
    {
        $user = Auth::user();

        if (! $user) {
            return false;
        }

        if ($user->role === 'admin') {
            return true;
        }

        $student = DB::table('students')->where('user_id', $user->id)->first();

        if ($student && (int) $student->id === (int) $record->student_id) {
            return true;
        }

        $lecturer = DB::table('lecturers')->where('user_id', $user->id)->first();

        if (! $lecturer) {
            return false;
        }

        if ((int) $lecturer->id === (int) $record->lecturer_id) {
            return true;
        }

        return DB::table('examiner_thesis')
            ->where('thesis_guidance_id', $record->thesis_guidance_id)
            ->where('examiner_user_id', $user->id)
            ->exists();
    }
}
