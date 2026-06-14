<?php

namespace App\Support;

use Illuminate\Support\Facades\DB;

class ThesisGuidanceRequestWorkflow
{
    public const ROLE_LABELS = [
        'supervisor_1' => 'Dosen Pembimbing 1',
        'supervisor_2' => 'Dosen Pembimbing 2',
        'examiner_1' => 'Dosen Penguji 1',
        'examiner_2' => 'Dosen Penguji 2',
    ];

    public const LECTURER_ROLES = ['supervisor_1', 'supervisor_2', 'examiner_1', 'examiner_2'];

    public function decideByAdmin(int $requestId, string $status, ?string $note = null): void
    {
        DB::transaction(function () use ($requestId, $status, $note) {
            DB::table('thesis_guidance_requests')
                ->where('id', $requestId)
                ->lockForUpdate()
                ->update([
                    'admin_status' => $status,
                    'admin_note' => $note,
                    'admin_decided_at' => now(),
                    'updated_at' => now(),
                ]);

            $this->refreshStatus($requestId);
        });
    }

    public function decideByLecturer(int $requestId, int $lecturerId, string $status, ?string $note = null): bool
    {
        return DB::transaction(function () use ($requestId, $lecturerId, $status, $note) {
            $request = DB::table('thesis_guidance_requests')
                ->where('id', $requestId)
                ->lockForUpdate()
                ->first();

            if (! $request) {
                return false;
            }

            $role = $this->lecturerRole($request, $lecturerId);

            if (! $role) {
                return false;
            }

            DB::table('thesis_guidance_requests')
                ->where('id', $requestId)
                ->update([
                    "{$role}_status" => $status,
                    "{$role}_note" => $note,
                    "{$role}_decided_at" => now(),
                    'updated_at' => now(),
                ]);

            $this->refreshStatus($requestId);

            return true;
        });
    }

    public function lecturerRole(object $request, int $lecturerId): ?string
    {
        foreach (self::LECTURER_ROLES as $role) {
            if ((int) $request->{"{$role}_id"} === $lecturerId) {
                return $role;
            }
        }

        return null;
    }

    private function refreshStatus(int $requestId): void
    {
        $request = DB::table('thesis_guidance_requests')->where('id', $requestId)->first();

        if (! $request) {
            return;
        }

        $statuses = [
            $request->admin_status,
            $request->supervisor_1_status,
            $request->supervisor_2_status,
            $request->examiner_1_status,
            $request->examiner_2_status,
        ];

        if (in_array('rejected', $statuses, true)) {
            DB::table('thesis_guidance_requests')->where('id', $requestId)->update([
                'status' => 'rejected',
                'updated_at' => now(),
            ]);

            return;
        }

        if ($statuses !== array_fill(0, count($statuses), 'approved')) {
            DB::table('thesis_guidance_requests')->where('id', $requestId)->update([
                'status' => 'pending',
                'updated_at' => now(),
            ]);

            return;
        }

        $this->activateRequest($request);
    }

    private function activateRequest(object $request): void
    {
        if ($request->activated_at) {
            return;
        }

        foreach (['supervisor_1_id', 'supervisor_2_id'] as $column) {
            $exists = DB::table('thesis_guidances')
                ->where('student_id', $request->student_id)
                ->where('lecturer_id', $request->{$column})
                ->where('title', $request->title)
                ->exists();

            if ($exists) {
                continue;
            }

            DB::table('thesis_guidances')->insert([
                'lecturer_id' => $request->{$column},
                'student_id' => $request->student_id,
                'title' => $request->title,
                'status' => 'active',
                'seminar_status' => 'Belum Seminar',
                'progress' => 0,
                'started_at' => now()->toDateString(),
                'last_note' => 'Bimbingan aktif setelah pengajuan disetujui admin dan dosen terkait.',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $guidanceIds = DB::table('thesis_guidances')
            ->where('student_id', $request->student_id)
            ->where('title', $request->title)
            ->pluck('id');

        $examinerUserIds = DB::table('lecturers')
            ->whereIn('id', [$request->examiner_1_id, $request->examiner_2_id])
            ->whereNotNull('user_id')
            ->pluck('user_id');

        foreach ($examinerUserIds as $userId) {
            DB::table('users')
                ->where('id', $userId)
                ->where('role', 'dosen')
                ->update(['role' => 'examiner', 'updated_at' => now()]);

            foreach ($guidanceIds as $guidanceId) {
                DB::table('examiner_thesis')->updateOrInsert(
                    [
                        'thesis_guidance_id' => $guidanceId,
                        'examiner_user_id' => $userId,
                    ],
                    [
                        'created_at' => now(),
                        'updated_at' => now(),
                    ],
                );
            }
        }

        DB::table('thesis_guidance_requests')->where('id', $request->id)->update([
            'status' => 'approved',
            'activated_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
