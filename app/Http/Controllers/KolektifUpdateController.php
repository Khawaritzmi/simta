<?php

namespace App\Http\Controllers;

use App\Models\UpdateHistory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class KolektifUpdateController extends Controller
{
    private const TARGETS = [
        'delta_mat' => [
            'label' => 'DELTA-MAT - Database Judul Skripsi',
            'table' => 'thesis_title_databases',
            'keys' => ['nim', 'title'],
            'fields' => [
                'submission_date', 'phone', 'email', 'nim', 'student_name', 'title',
                'supervisor_1', 'supervisor_1_nip', 'supervisor_2', 'supervisor_2_nip',
                'document_url',
            ],
            'required' => ['nim', 'student_name', 'title'],
        ],
        'students' => [
            'label' => 'SIMTA - Mahasiswa',
            'table' => 'students',
            'keys' => ['nim'],
            'fields' => ['nim', 'name', 'program', 'email'],
            'required' => ['nim', 'name'],
        ],
        'lecturers' => [
            'label' => 'SIMTA - Dosen',
            'table' => 'lecturers',
            'keys' => ['nidn', 'nip'],
            'fields' => [
                'nip', 'nidn', 'certificate_number', 'employment_status', 'expertise',
                'name', 'gender', 'birth_place', 'birth_date', 'email', 'phone', 'address',
            ],
            'required' => ['name'],
        ],
    ];

    public function index(): View
    {
        $this->ensureAdmin();
        $historyTableReady = Schema::hasTable('update_histories');

        return view('admin.kolektif-update.index', [
            'targets' => self::TARGETS,
            'historyTableReady' => $historyTableReady,
            'histories' => $historyTableReady
                ? UpdateHistory::query()
                    ->with('user:id,name,email')
                    ->latest('created_at')
                    ->latest('id')
                    ->paginate(20)
                : new LengthAwarePaginator([], 0, 20),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $this->ensureAdmin();

        if (! Schema::hasTable('update_histories')) {
            throw ValidationException::withMessages([
                'data_file' => 'Tabel riwayat update belum tersedia. Jalankan php artisan migrate terlebih dahulu sebelum memakai Update Database Kolektif.',
            ]);
        }

        $validated = $request->validate([
            'target' => ['required', 'in:'.implode(',', array_keys(self::TARGETS))],
            'mode' => ['required', 'in:update_existing,insert_only'],
            'data_file' => ['nullable', 'file', 'max:4096'],
            'data_text' => ['nullable', 'string'],
        ]);

        if (! $request->hasFile('data_file') && trim((string) $request->input('data_text')) === '') {
            throw ValidationException::withMessages([
                'data_file' => 'Upload CSV atau isi data massal terlebih dahulu.',
            ]);
        }

        $rows = $this->rowsFromRequest($request);
        $target = self::TARGETS[$validated['target']];
        $summary = [
            'inserted' => 0,
            'updated' => 0,
            'skipped' => 0,
        ];

        DB::transaction(function () use ($rows, $target, $validated, &$summary): void {
            foreach ($rows as $index => $row) {
                $payload = $this->payloadForTarget($row, $target, $validated['target']);

                if (! $this->hasRequiredFields($payload, $target, $validated['target'])) {
                    $summary['skipped']++;

                    continue;
                }

                $existing = $this->findExistingRow($target, $payload, $validated['target']);

                if ($existing && $validated['mode'] === 'insert_only') {
                    $summary['skipped']++;

                    continue;
                }

                if ($existing) {
                    $changes = $this->fieldChanges($existing, $payload);

                    if ($changes === []) {
                        $summary['skipped']++;

                        continue;
                    }

                    DB::table($target['table'])->where('id', $existing->id)->update($payload + [
                        'updated_at' => now(),
                    ]);

                    $this->recordHistory($target['table'], 'update', [
                        'row' => $index + 2,
                        'key' => $this->historyKey($target, $payload, $validated['target']),
                        'run_mode' => $validated['mode'],
                        'fields' => $changes,
                    ]);

                    $summary['updated']++;

                    continue;
                }

                $insert = $payload + [
                    'created_at' => now(),
                    'updated_at' => now(),
                ];

                DB::table($target['table'])->insert($insert);

                $this->recordHistory($target['table'], 'insert', [
                    'row' => $index + 2,
                    'key' => $this->historyKey($target, $payload, $validated['target']),
                    'run_mode' => $validated['mode'],
                    'after' => $payload,
                ]);

                $summary['inserted']++;
            }
        });

        return redirect()
            ->route('admin.kolektif-update')
            ->with('status', "Update kolektif selesai. Ditambahkan: {$summary['inserted']}, diperbarui: {$summary['updated']}, dilewati: {$summary['skipped']}.");
    }

    private function rowsFromRequest(Request $request): array
    {
        if ($request->hasFile('data_file')) {
            $extension = strtolower($request->file('data_file')->getClientOriginalExtension());

            if ($extension === 'xlsx') {
                return $this->rowsFromXlsx($request->file('data_file')->getRealPath());
            }

            if ($extension === 'xls') {
                throw ValidationException::withMessages([
                    'data_file' => 'Format .xls lama belum didukung. Simpan ulang sebagai .xlsx atau CSV, lalu unggah kembali.',
                ]);
            }

            if (! in_array($extension, ['csv', 'txt'], true)) {
                throw ValidationException::withMessages([
                    'data_file' => 'Format file harus CSV atau XLSX. File TXT berisi CSV juga didukung.',
                ]);
            }

            $content = file_get_contents($request->file('data_file')->getRealPath());
        } else {
            $content = (string) $request->input('data_text');
        }

        $stream = fopen('php://temp', 'r+');
        fwrite($stream, $content);
        rewind($stream);

        $table = [];

        while (($line = fgetcsv($stream, 0, ',', '"', '\\')) !== false) {
            if ($line === [null] || $line === false) {
                continue;
            }

            $table[] = $line;
        }

        fclose($stream);

        return $this->rowsFromTable($table);
    }

    private function rowsFromXlsx(string $path): array
    {
        if (! class_exists(\ZipArchive::class)) {
            throw ValidationException::withMessages([
                'data_file' => 'Server belum memiliki ekstensi PHP ZipArchive. Gunakan CSV atau aktifkan ekstensi zip.',
            ]);
        }

        $zip = new \ZipArchive();
        if ($zip->open($path) !== true) {
            throw ValidationException::withMessages([
                'data_file' => 'File XLSX tidak bisa dibaca.',
            ]);
        }

        $sharedStrings = $this->xlsxSharedStrings($zip);
        $sheetXml = $zip->getFromName('xl/worksheets/sheet1.xml');

        if ($sheetXml === false) {
            for ($index = 0; $index < $zip->numFiles; $index++) {
                $name = $zip->getNameIndex($index);
                if (str_starts_with($name, 'xl/worksheets/sheet') && str_ends_with($name, '.xml')) {
                    $sheetXml = $zip->getFromName($name);
                    break;
                }
            }
        }

        $zip->close();

        if ($sheetXml === false) {
            throw ValidationException::withMessages([
                'data_file' => 'Worksheet XLSX tidak ditemukan.',
            ]);
        }

        $sheet = simplexml_load_string($sheetXml);
        if (! $sheet) {
            throw ValidationException::withMessages([
                'data_file' => 'Worksheet XLSX tidak valid.',
            ]);
        }

        $table = [];
        foreach ($sheet->sheetData->row as $row) {
            $line = [];
            foreach ($row->c as $cell) {
                $column = $this->xlsxColumnIndex((string) $cell['r']);
                $line[$column] = $this->xlsxCellValue($cell, $sharedStrings);
            }

            if ($line !== []) {
                ksort($line);
                $table[] = $line;
            }
        }

        return $this->rowsFromTable($table);
    }

    private function rowsFromTable(array $table): array
    {
        if ($table === []) {
            throw ValidationException::withMessages([
                'data_file' => 'File harus memiliki header dan minimal satu baris data.',
            ]);
        }

        $header = array_map(fn ($value) => $this->normalizeHeader((string) $value), array_shift($table));
        $rows = [];

        foreach ($table as $line) {
            $row = [];
            foreach ($header as $position => $column) {
                if ($column === '') {
                    continue;
                }

                $row[$column] = array_key_exists($position, $line) ? trim((string) $line[$position]) : null;
            }

            if (array_filter($row, fn ($value) => $value !== null && $value !== '') !== []) {
                $rows[] = $row;
            }
        }

        if ($rows === []) {
            throw ValidationException::withMessages([
                'data_file' => 'File harus memiliki minimal satu baris data setelah header.',
            ]);
        }

        return $rows;
    }

    private function xlsxSharedStrings(\ZipArchive $zip): array
    {
        $xml = $zip->getFromName('xl/sharedStrings.xml');
        if ($xml === false) {
            return [];
        }

        $sharedStrings = [];
        $document = simplexml_load_string($xml);

        if (! $document) {
            return [];
        }

        foreach ($document->si as $item) {
            if (isset($item->t)) {
                $sharedStrings[] = (string) $item->t;
                continue;
            }

            $text = '';
            foreach ($item->r as $run) {
                $text .= (string) $run->t;
            }
            $sharedStrings[] = $text;
        }

        return $sharedStrings;
    }

    private function xlsxColumnIndex(string $reference): int
    {
        preg_match('/^[A-Z]+/i', $reference, $matches);
        $letters = strtoupper($matches[0] ?? 'A');
        $index = 0;

        foreach (str_split($letters) as $letter) {
            $index = ($index * 26) + (ord($letter) - 64);
        }

        return $index - 1;
    }

    private function xlsxCellValue(\SimpleXMLElement $cell, array $sharedStrings): string
    {
        $type = (string) $cell['t'];

        if ($type === 's') {
            return $sharedStrings[(int) $cell->v] ?? '';
        }

        if ($type === 'inlineStr') {
            return (string) ($cell->is->t ?? '');
        }

        return (string) ($cell->v ?? '');
    }

    private function payloadForTarget(array $row, array $target, string $targetName): array
    {
        $payload = [];
        $aliases = $this->aliasesForTarget($targetName);

        foreach ($row as $header => $value) {
            $field = $aliases[$header] ?? $header;

            if (! in_array($field, $target['fields'], true)) {
                continue;
            }

            $payload[$field] = $value === '' ? null : $value;
        }

        if ($targetName === 'students' && ! array_key_exists('program', $payload)) {
            $payload['program'] = 'Matematika';
        }

        return $payload;
    }

    private function hasRequiredFields(array $payload, array $target, string $targetName): bool
    {
        foreach ($target['required'] as $required) {
            if (! isset($payload[$required]) || trim((string) $payload[$required]) === '') {
                return false;
            }
        }

        if ($targetName === 'lecturers') {
            return ! empty($payload['nip']);
        }

        return true;
    }

    private function findExistingRow(array $target, array $payload, string $targetName): ?object
    {
        $query = DB::table($target['table']);

        if ($targetName === 'lecturers') {
            return $query
                ->when(! empty($payload['nidn']), fn ($builder) => $builder->orWhere('nidn', $payload['nidn']))
                ->when(! empty($payload['nip']), fn ($builder) => $builder->orWhere('nip', $payload['nip']))
                ->first();
        }

        foreach ($target['keys'] as $key) {
            $query->where($key, $payload[$key]);
        }

        return $query->first();
    }

    private function fieldChanges(object $existing, array $payload): array
    {
        $changes = [];

        foreach ($payload as $field => $value) {
            $old = $existing->{$field} ?? null;

            if ((string) $old === (string) $value) {
                continue;
            }

            $changes[$field] = [
                'from' => $old,
                'to' => $value,
            ];
        }

        return $changes;
    }

    private function recordHistory(string $table, string $mode, array $changes): void
    {
        UpdateHistory::query()->create([
            'user_id' => Auth::id(),
            'target_table' => $table,
            'mode' => $mode,
            'changes' => $changes,
            'created_at' => now(),
        ]);
    }

    private function historyKey(array $target, array $payload, string $targetName): array
    {
        if ($targetName === 'lecturers') {
            return array_filter([
                'nidn' => $payload['nidn'] ?? null,
                'nip' => $payload['nip'] ?? null,
            ], fn ($value) => $value !== null && $value !== '');
        }

        $key = [];
        foreach ($target['keys'] as $column) {
            $key[$column] = $payload[$column] ?? null;
        }

        return $key;
    }

    private function normalizeHeader(string $header): string
    {
        $header = preg_replace('/^\xEF\xBB\xBF/', '', $header) ?? $header;

        $header = Str::of($header)
            ->lower()
            ->replace(['.', '/', '-', '(', ')'], ' ')
            ->squish()
            ->replace(' ', '_')
            ->toString();

        return $header;
    }

    private function aliasesForTarget(string $target): array
    {
        $common = [
            'nama_mahasiswa' => $target === 'delta_mat' ? 'student_name' : 'name',
            'mahasiswa' => $target === 'delta_mat' ? 'student_name' : 'name',
            'nama_dosen' => 'name',
            'nama' => $target === 'delta_mat' ? 'student_name' : 'name',
            'prodi' => 'program',
            'program_studi' => 'program',
            'judul' => 'title',
            'judul_skripsi' => 'title',
            'judul_tugas_akhir' => 'title',
            'dosen_pembimbing_1' => 'supervisor_1',
            'pembimbing_1' => 'supervisor_1',
            'dosen_pembimbing_2' => 'supervisor_2',
            'pembimbing_2' => 'supervisor_2',
            'nip_pembimbing_1' => 'supervisor_1_nip',
            'nip_pembimbing_2' => 'supervisor_2_nip',
            'tanggal_pengajuan' => 'submission_date',
            'tanggal_submit' => 'submission_date',
            'tautan_dokumen' => 'document_url',
            'link_dokumen' => 'document_url',
            'no_hp' => 'phone',
            'telepon' => 'phone',
            'sertifikat_pendidik' => 'certificate_number',
            'status_kepegawaian' => 'employment_status',
            'bidang_keahlian' => 'expertise',
            'jenis_kelamin' => 'gender',
            'tempat_lahir' => 'birth_place',
            'tanggal_lahir' => 'birth_date',
            'alamat' => 'address',
        ];

        return $common;
    }

    private function ensureAdmin(): void
    {
        abort_if(Auth::user()->role !== 'admin', 403);
    }
}
