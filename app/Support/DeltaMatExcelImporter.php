<?php

namespace App\Support;

use DateTimeImmutable;
use Illuminate\Support\Facades\DB;
use ZipArchive;

class DeltaMatExcelImporter
{
    private const MAIN_SHEET = 'xl/worksheets/sheet1.xml';
    private const MAIN_SHEET_RELS = 'xl/worksheets/_rels/sheet1.xml.rels';
    private const SHARED_STRINGS = 'xl/sharedStrings.xml';

    /**
     * @return array{files:int, rows:int, inserted:int, updated:int}
     */
    public function import(?string $directory = null): array
    {
        $records = $this->records($directory);
        $result = [
            'files' => count($this->files($directory)),
            'rows' => count($records),
            'inserted' => 0,
            'updated' => 0,
        ];

        foreach ($records as $record) {
            $exists = DB::table('thesis_title_databases')
                ->where('nim', $record['nim'])
                ->where('title', $record['title'])
                ->exists();

            DB::table('thesis_title_databases')->updateOrInsert(
                [
                    'nim' => $record['nim'],
                    'title' => $record['title'],
                ],
                $record + [
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
            );

            $exists ? $result['updated']++ : $result['inserted']++;
        }

        return $result;
    }

    /**
     * @return array<int, array<string, string|null>>
     */
    public function records(?string $directory = null): array
    {
        $records = [];

        foreach ($this->files($directory) as $file) {
            foreach ($this->readWorkbook($file) as $record) {
                $records[] = $record;
            }
        }

        return $records;
    }

    /**
     * @return array<int, string>
     */
    private function files(?string $directory): array
    {
        $directory ??= database_path('excel');
        $files = glob(rtrim($directory, DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR.'*.xlsx') ?: [];
        sort($files);

        return $files;
    }

    /**
     * @return array<int, array<string, string|null>>
     */
    private function readWorkbook(string $file): array
    {
        $zip = new ZipArchive();

        if ($zip->open($file) !== true) {
            return [];
        }

        $sharedStrings = $this->sharedStrings($zip);
        $relationships = $this->relationships($zip);
        $sheetXml = $zip->getFromName(self::MAIN_SHEET);
        $zip->close();

        if ($sheetXml === false) {
            return [];
        }

        $sheet = simplexml_load_string($sheetXml);

        if (! $sheet) {
            return [];
        }

        $sheet->registerXPathNamespace('m', 'http://schemas.openxmlformats.org/spreadsheetml/2006/main');
        $sheet->registerXPathNamespace('r', 'http://schemas.openxmlformats.org/officeDocument/2006/relationships');

        $hyperlinks = $this->hyperlinks($sheet, $relationships);
        $rows = $sheet->xpath('//m:sheetData/m:row') ?: [];
        $headers = [];
        $records = [];

        foreach ($rows as $row) {
            $rowAttributes = $row->attributes();
            $rowNumber = (int) ((string) ($rowAttributes['r'] ?? 0));
            $cells = $this->rowCells($row, $sharedStrings, $hyperlinks);

            if ($rowNumber === 1) {
                $headers = $this->headers($cells);
                continue;
            }

            if ($headers === []) {
                continue;
            }

            $values = $this->valuesByHeader($headers, $cells);
            $record = $this->recordFromValues($values);

            if ($record !== null) {
                $records[] = $record;
            }
        }

        return $records;
    }

    /**
     * @return array<int, string>
     */
    private function sharedStrings(ZipArchive $zip): array
    {
        $xml = $zip->getFromName(self::SHARED_STRINGS);

        if ($xml === false) {
            return [];
        }

        $root = simplexml_load_string($xml);

        if (! $root) {
            return [];
        }

        $root->registerXPathNamespace('m', 'http://schemas.openxmlformats.org/spreadsheetml/2006/main');
        $strings = [];

        foreach ($root->xpath('//m:si') ?: [] as $item) {
            $item->registerXPathNamespace('m', 'http://schemas.openxmlformats.org/spreadsheetml/2006/main');
            $parts = [];

            foreach ($item->xpath('.//m:t') ?: [] as $text) {
                $parts[] = (string) $text;
            }

            $strings[] = implode('', $parts);
        }

        return $strings;
    }

    /**
     * @return array<string, string>
     */
    private function relationships(ZipArchive $zip): array
    {
        $xml = $zip->getFromName(self::MAIN_SHEET_RELS);

        if ($xml === false) {
            return [];
        }

        $root = simplexml_load_string($xml);

        if (! $root) {
            return [];
        }

        $relationships = [];

        foreach ($root->children('http://schemas.openxmlformats.org/package/2006/relationships') as $relationship) {
            $attributes = $relationship->attributes();
            $relationships[(string) $attributes['Id']] = (string) $attributes['Target'];
        }

        return $relationships;
    }

    /**
     * @return array<string, string>
     */
    private function hyperlinks(\SimpleXMLElement $sheet, array $relationships): array
    {
        $links = [];

        foreach ($sheet->xpath('//m:hyperlinks/m:hyperlink') ?: [] as $hyperlink) {
            $attributes = $hyperlink->attributes();
            $relationAttributes = $hyperlink->attributes('http://schemas.openxmlformats.org/officeDocument/2006/relationships');
            $id = (string) ($relationAttributes['id'] ?? '');

            if ($id !== '' && isset($relationships[$id])) {
                $links[(string) $attributes['ref']] = $relationships[$id];
            }
        }

        return $links;
    }

    /**
     * @return array<string, array{value:string, link:string|null}>
     */
    private function rowCells(\SimpleXMLElement $row, array $sharedStrings, array $hyperlinks): array
    {
        $cells = [];

        foreach ($row->children('http://schemas.openxmlformats.org/spreadsheetml/2006/main')->c as $cell) {
            $attributes = $cell->attributes();
            $children = $cell->children('http://schemas.openxmlformats.org/spreadsheetml/2006/main');
            $ref = (string) ($attributes['r'] ?? '');

            if ($ref === '') {
                continue;
            }

            $column = preg_replace('/\d+/', '', $ref) ?: $ref;
            $type = (string) ($attributes['t'] ?? '');
            $value = (string) ($children->v ?? '');

            if ($type === 's' && $value !== '') {
                $value = $sharedStrings[(int) $value] ?? '';
            }

            $formula = (string) ($children->f ?? '');
            $link = $hyperlinks[$ref] ?? $this->linkFromFormula($formula);

            $cells[$column] = [
                'value' => $value,
                'link' => $link,
            ];
        }

        return $cells;
    }

    /**
     * @param array<string, array{value:string, link:string|null}> $cells
     * @return array<string, string>
     */
    private function headers(array $cells): array
    {
        $headers = [];

        foreach ($cells as $column => $cell) {
            $header = $this->clean($cell['value']);

            if ($header !== null) {
                $headers[$column] = $header;
            }
        }

        return $headers;
    }

    /**
     * @param array<string, string> $headers
     * @param array<string, array{value:string, link:string|null}> $cells
     * @return array<string, array{value:string|null, link:string|null}>
     */
    private function valuesByHeader(array $headers, array $cells): array
    {
        $values = [];

        foreach ($headers as $column => $header) {
            $cell = $cells[$column] ?? ['value' => '', 'link' => null];
            $values[$header] = [
                'value' => $this->clean($cell['value']),
                'link' => $cell['link'],
            ];
        }

        return $values;
    }

    /**
     * @param array<string, array{value:string|null, link:string|null}> $values
     * @return array<string, string|null>|null
     */
    private function recordFromValues(array $values): ?array
    {
        $timestamp = $this->get($values, 'Timestamp');
        $nim = $this->normalizeNumber($this->get($values, 'NIM'));
        $studentName = $this->get($values, 'Nama Lengkap');
        $title = $this->get($values, 'Judul yang disetujui');

        if ($nim === null || $studentName === null || $title === null) {
            return null;
        }

        return [
            'submission_date' => $this->get($values, 'Tanggal Pengajuan') ?? $this->excelDate($timestamp),
            'phone' => $this->normalizeNumber($this->get($values, 'No. Hp Aktif (WA)')),
            'email' => $this->get($values, 'E-mail Aktif'),
            'nim' => $nim,
            'student_name' => $studentName,
            'title' => $title,
            'supervisor_1' => $this->get($values, 'Pembimbing 1'),
            'supervisor_1_nip' => $this->normalizeNumber($this->get($values, 'NIP Pembimbing 1')),
            'supervisor_2' => $this->get($values, 'Pembimbing 2'),
            'supervisor_2_nip' => $this->normalizeNumber($this->get($values, 'NIP Pembimbing 2')),
            'document_url' => $this->documentUrl($values),
        ];
    }

    /**
     * @param array<string, array{value:string|null, link:string|null}> $values
     */
    private function get(array $values, string $header): ?string
    {
        return $values[$header]['value'] ?? null;
    }

    /**
     * @param array<string, array{value:string|null, link:string|null}> $values
     */
    private function documentUrl(array $values): ?string
    {
        foreach ($values as $header => $cell) {
            if (str_starts_with($header, 'Link to merged Doc') || str_starts_with($header, 'Merged Doc URL')) {
                return $cell['link'] ?? $cell['value'];
            }
        }

        foreach ($values as $header => $cell) {
            if (str_starts_with($header, 'Scan Lembar U1')) {
                return $cell['link'] ?? $cell['value'];
            }
        }

        return null;
    }

    private function clean(?string $value): ?string
    {
        $value = trim((string) $value);

        if ($value === '' || $value === '#N/A') {
            return null;
        }

        return preg_replace('/\s+/', ' ', $value) ?: null;
    }

    private function normalizeNumber(?string $value): ?string
    {
        $value = $this->clean($value);

        if ($value === null) {
            return null;
        }

        if (preg_match('/^-?\d+(?:\.\d+)?E[+-]?\d+$/i', $value) || preg_match('/^\d+\.0+$/', $value)) {
            return number_format((float) $value, 0, '', '');
        }

        return $value;
    }

    private function excelDate(?string $serial): ?string
    {
        if ($serial === null || ! is_numeric($serial)) {
            return null;
        }

        $seconds = ((float) $serial - 25569) * 86400;
        $date = (new DateTimeImmutable('@'.(int) round($seconds)))->setTimezone(new \DateTimeZone('Asia/Makassar'));
        $months = [
            1 => 'Januari',
            2 => 'Februari',
            3 => 'Maret',
            4 => 'April',
            5 => 'Mei',
            6 => 'Juni',
            7 => 'Juli',
            8 => 'Agustus',
            9 => 'September',
            10 => 'Oktober',
            11 => 'November',
            12 => 'Desember',
        ];

        return $date->format('d').' '.$months[(int) $date->format('n')].' '.$date->format('Y');
    }

    private function linkFromFormula(string $formula): ?string
    {
        if (preg_match('/HYPERLINK\("([^"]+)"/i', $formula, $matches)) {
            return $matches[1];
        }

        return null;
    }
}
