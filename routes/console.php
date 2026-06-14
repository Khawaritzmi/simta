<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use App\Support\DeltaMatExcelImporter;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('deltamat:import-excel', function () {
    $result = app(DeltaMatExcelImporter::class)->import();

    $this->info("DELTA-MAT Excel import selesai.");
    $this->line("Files: {$result['files']}");
    $this->line("Rows: {$result['rows']}");
    $this->line("Inserted: {$result['inserted']}");
    $this->line("Updated: {$result['updated']}");
})->purpose('Import and merge DELTA-MAT records from database/excel');
