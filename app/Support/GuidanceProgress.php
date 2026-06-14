<?php

namespace App\Support;

use App\Models\Setting;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class GuidanceProgress
{
    public static function target(): int
    {
        if (! Schema::hasTable('settings')) {
            return 16;
        }

        return max(1, (int) Setting::value('guidance_target_default', '16'));
    }

    public static function forStudent(int $studentId, string $type, ?int $target = null): array
    {
        $target ??= self::target();
        $completed = 0;

        if (Schema::hasTable('guidances')) {
            $completed = DB::table('guidances')
                ->where('student_id', $studentId)
                ->where('type', $type)
                ->whereNotNull('completed_at')
                ->count();
        }

        return [
            'completed' => $completed,
            'target' => $target,
            'fraction' => "{$completed}/{$target}",
            'percent' => min(100, (int) round(($completed / $target) * 100)),
        ];
    }
}
