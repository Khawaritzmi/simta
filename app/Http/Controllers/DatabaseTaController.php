<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\View\View;

class DatabaseTaController extends Controller
{
    public function index(Request $request): View
    {
        $query = trim((string) $request->query('q'));
        $records = DB::table('thesis_title_databases')
            ->select(
                'id',
                'title',
                'nim',
                'student_name',
                'supervisor_1',
                'supervisor_2',
                'submission_date',
                'document_url',
            )
            ->orderByDesc('id')
            ->get();

        $recommendations = $records->map(function ($record) use ($query) {
            $record->similarity = $query === '' ? 0 : $this->similarity($query, $record->title);
            $record->match_label = $this->matchLabel($record->similarity);

            return $record;
        });

        if ($query !== '') {
            $recommendations = $recommendations->sortByDesc('similarity')->values();
        }

        $highest = $recommendations->max('similarity') ?? 0;

        return view('database-ta.index', [
            'query' => $query,
            'highest' => $highest,
            'status' => $this->status($highest),
            'recommendations' => $recommendations,
        ]);
    }

    public function show(int $record): View
    {
        $record = DB::table('thesis_title_databases')->where('id', $record)->first();

        abort_if(! $record, 404);

        return view('database-ta.show', [
            'record' => $record,
            'previewUrl' => $this->documentPreviewUrl($record->document_url),
        ]);
    }

    private function similarity(string $needle, string $haystack): float
    {
        $needleWords = $this->words($needle);
        $haystackWords = $this->words($haystack);

        if ($needleWords === [] || $haystackWords === []) {
            return 0;
        }

        $intersection = array_intersect($needleWords, $haystackWords);
        $union = array_unique(array_merge($needleWords, $haystackWords));

        return round((count($intersection) / max(count($union), 1)) * 100, 2);
    }

    private function words(string $value): array
    {
        $normalized = Str::of($value)
            ->lower()
            ->replaceMatches('/[^a-z0-9\s]+/i', ' ')
            ->squish()
            ->explode(' ')
            ->filter(fn ($word) => mb_strlen($word) > 2)
            ->values()
            ->all();

        return array_values(array_unique($normalized));
    }

    private function status(float $score): string
    {
        if ($score >= 70) {
            return 'DUPLIKASI';
        }

        if ($score >= 30) {
            return 'WASPADA';
        }

        return 'UNIK';
    }

    private function matchLabel(float $score): string
    {
        if ($score >= 70) {
            return 'Kecocokan Tinggi';
        }

        if ($score >= 30) {
            return 'Kecocokan Sedang';
        }

        return 'Kecocokan Rendah';
    }

    private function documentPreviewUrl(?string $url): ?string
    {
        $url = trim((string) $url);

        if ($url === '') {
            return null;
        }

        if (preg_match('~/file/d/([^/]+)~', $url, $matches) || preg_match('~[?&]id=([^&]+)~', $url, $matches)) {
            return 'https://drive.google.com/file/d/'.$matches[1].'/preview';
        }

        return $url;
    }
}
