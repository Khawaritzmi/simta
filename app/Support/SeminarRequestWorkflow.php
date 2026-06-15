<?php

namespace App\Support;

use Illuminate\Support\Facades\DB;

class SeminarRequestWorkflow
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
        DB::transaction(function () use ($requestId, $status, $note): void {
            DB::table('seminar_requests')
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
        return DB::transaction(function () use ($requestId, $lecturerId, $status, $note): bool {
            $request = DB::table('seminar_requests')
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

            DB::table('seminar_requests')
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
        $request = DB::table('seminar_requests')
            ->join('thesis_guidances', 'thesis_guidances.id', '=', 'seminar_requests.thesis_guidance_id')
            ->where('seminar_requests.id', $requestId)
            ->select('seminar_requests.*', 'thesis_guidances.title')
            ->first();

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
            DB::table('seminar_requests')->where('id', $requestId)->update([
                'status' => 'rejected',
                'updated_at' => now(),
            ]);

            return;
        }

        if ($statuses !== array_fill(0, count($statuses), 'approved')) {
            DB::table('seminar_requests')->where('id', $requestId)->update([
                'status' => 'pending',
                'updated_at' => now(),
            ]);

            return;
        }

        $this->scheduleApprovedRequest($request);
    }

    private function scheduleApprovedRequest(object $request): void
    {
        if ($request->seminar_id) {
            DB::table('seminar_requests')->where('id', $request->id)->update([
                'status' => 'approved',
                'scheduled_at' => $request->scheduled_at ?? $request->proposed_at,
                'updated_at' => now(),
            ]);

            return;
        }

        $seminarId = DB::table('seminars')->insertGetId([
            'thesis_guidance_id' => $request->thesis_guidance_id,
            'type' => $request->type,
            'scheduled_at' => $request->proposed_at,
            'room' => $request->room,
            'status' => 'scheduled',
            'score' => null,
            'feedback' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('seminar_requests')->where('id', $request->id)->update([
            'seminar_id' => $seminarId,
            'status' => 'approved',
            'scheduled_at' => $request->proposed_at,
            'updated_at' => now(),
        ]);

        DB::table('thesis_guidances')->where('id', $request->thesis_guidance_id)->update([
            'seminar_status' => $request->type,
            'updated_at' => now(),
        ]);
    }
}
