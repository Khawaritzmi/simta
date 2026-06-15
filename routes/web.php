<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AdminBimbinganPaController;
use App\Http\Controllers\AdminDatabaseTaController;
use App\Http\Controllers\AdminReportController;
use App\Http\Controllers\AdminSeminarController;
use App\Http\Controllers\AdminSettingsController;
use App\Http\Controllers\BimbinganController;
use App\Http\Controllers\DatabaseTaController;
use App\Http\Controllers\DosenReportController;
use App\Http\Controllers\KolektifUpdateController;
use App\Http\Controllers\MahasiswaController;
use App\Http\Controllers\PaDosenController;
use App\Http\Controllers\PaMahasiswaController;
use App\Http\Controllers\ProfilePhotoController;
use App\Http\Controllers\ThesisUploadController;
use Illuminate\Support\Facades\Route;

Route::get('/images/Logo_Universitas_Negeri_Makassar.png', function () {
    return response()->file(public_path('images/Logo_Universitas_Negeri_Makassar.png'), [
        'Content-Type' => 'image/png',
    ]);
})->name('logo.unm');

Route::get('/assets/{file}', function (string $file) {
    abort_unless(in_array($file, ['fmipa.png', 'jurmat.png', 'TA_logo.png', 'PA_logo.png', 'DELTAMAT_logo.png'], true), 404);

    return response()->file(base_path("assets/{$file}"), [
        'Content-Type' => 'image/png',
    ]);
})->where('file', 'fmipa\.png|jurmat\.png|TA_logo\.png|PA_logo\.png|DELTAMAT_logo\.png')->name('project.asset');

Route::get('/', fn () => view('landing'))->name('landing');
Route::get('/about', fn () => view('about'))->name('about');
Route::get('/database-ta', [DatabaseTaController::class, 'index'])->name('database-ta');
Route::get('/database-ta/{record}', [DatabaseTaController::class, 'show'])->whereNumber('record')->name('database-ta.show');
Route::get('/profile-photos/{file}', [ProfilePhotoController::class, 'show'])
    ->where('file', '[A-Za-z0-9._-]+')
    ->name('profile-photos.show');

Route::middleware('guest')->group(function () {
    Route::get('/login', fn () => redirect()->route('dosen.login'))->name('login');

    Route::get('/dosen/login', [AuthController::class, 'showLogin'])->name('dosen.login');
    Route::post('/dosen/login', [AuthController::class, 'login'])->name('dosen.login.store');
    Route::get('/dosen/register', [AuthController::class, 'showRegister'])->name('dosen.register');
    Route::post('/dosen/register', [AuthController::class, 'register'])->name('dosen.register.store');

    Route::get('/mahasiswa/login', [AuthController::class, 'showStudentLogin'])->name('mahasiswa.login');
    Route::post('/mahasiswa/login', [AuthController::class, 'studentLogin'])->name('mahasiswa.login.store');
    Route::get('/mahasiswa/register', [AuthController::class, 'showStudentRegister'])->name('mahasiswa.register');
    Route::post('/mahasiswa/register', [AuthController::class, 'studentRegister'])->name('mahasiswa.register.store');

    Route::get('/admin/login', [AuthController::class, 'showAdminLogin'])->name('admin.login');
    Route::post('/admin/login', [AuthController::class, 'adminLogin'])->name('admin.login.store');
});

Route::middleware('auth')->group(function () {
    Route::get('/portal', [AuthController::class, 'portal'])->name('portal');
    Route::get('/password', [AuthController::class, 'showPasswordForm'])->name('password.edit');
    Route::put('/password', [AuthController::class, 'updatePassword'])->name('password.update');
    Route::get('/ta/uploads/{upload}', [ThesisUploadController::class, 'show'])
        ->whereNumber('upload')
        ->name('thesis-uploads.show');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::prefix('dosen')->group(function () {
        Route::get('/', [BimbinganController::class, 'dashboard'])->name('dashboard');
        Route::get('/profil', [BimbinganController::class, 'profile'])->name('profile');
        Route::post('/profil', [BimbinganController::class, 'updateProfile'])->name('profile.update');
        Route::get('/ta/pengajuan', [BimbinganController::class, 'guidanceRequests'])->name('guidance-requests.index');
        Route::get('/ta/bimbingan-saya', [BimbinganController::class, 'myGuidances'])->name('guidance.mine');
        Route::get('/pa', [BimbinganController::class, 'pa'])->name('pa.dashboard');
        Route::post('/bimbingan-log', [BimbinganController::class, 'storeGuidanceLog'])->name('guidance-log.store');
        Route::post('/pengajuan-ta/{guidanceRequest}/persetujuan', [BimbinganController::class, 'decideGuidanceRequest'])->name('guidance-requests.decide');
        Route::get('/bimbingan-ta', fn () => redirect()->route('guidance.mine'))->name('guidance');
        Route::get('/persetujuan', fn () => redirect()->route('guidance-requests.index'))->name('approvals');
        Route::post('/persetujuan/{approval}', [BimbinganController::class, 'decideApproval'])->name('approvals.decide');
        Route::get('/seminar-ujian', [BimbinganController::class, 'seminars'])->name('seminars');
        Route::post('/seminar-ujian/pengajuan/{seminarRequest}/persetujuan', [BimbinganController::class, 'decideSeminarRequest'])->name('seminar-requests.decide');
        Route::post('/seminar-ujian/{seminar}', [BimbinganController::class, 'gradeSeminar'])->name('seminars.grade');
        Route::get('/repository', [BimbinganController::class, 'repository'])->name('repository');
        Route::post('/repository', [BimbinganController::class, 'storeRepository'])->name('repository.store');
        Route::get('/qa', [BimbinganController::class, 'qa'])->name('qa');
        Route::post('/qa/{question}', [BimbinganController::class, 'answerQuestion'])->name('qa.answer');
        Route::get('/manual-aplikasi', [BimbinganController::class, 'manuals'])->name('manuals');
        Route::get('/export-report', [DosenReportController::class, 'export'])->name('dosen.export-report');
    });

    Route::prefix('mahasiswa')->name('mahasiswa.')->group(function () {
        Route::get('/', [MahasiswaController::class, 'dashboard'])->name('dashboard');
        Route::get('/profil', [MahasiswaController::class, 'profile'])->name('profile');
        Route::post('/profil', [MahasiswaController::class, 'updateProfile'])->name('profile.update');
        Route::get('/ta/pengajuan', [MahasiswaController::class, 'guidanceRequest'])->name('guidance-requests.index');
        Route::get('/ta/tugas-akhir-saya', [MahasiswaController::class, 'myThesis'])->name('guidance.mine');
        Route::post('/ta/uploads', [MahasiswaController::class, 'storeThesisUpload'])->name('thesis-uploads.store');
        Route::post('/seminar-ujian/pengajuan', [MahasiswaController::class, 'storeSeminarRequest'])->name('seminar-requests.store');
        Route::get('/pa', [MahasiswaController::class, 'pa'])->name('pa.dashboard');
        Route::post('/bimbingan-ta/pengajuan', [MahasiswaController::class, 'storeGuidanceRequest'])->name('guidance-requests.store');
        Route::get('/bimbingan-ta', fn () => redirect()->route('mahasiswa.guidance.mine'))->name('guidance');
        Route::get('/repository', [MahasiswaController::class, 'repository'])->name('repository');
        Route::get('/qa', [MahasiswaController::class, 'qa'])->name('qa');
    });

    Route::prefix('pa')->name('pa.')->group(function () {
        Route::get('/dosen', fn () => redirect()->route('pa.dashboard'))->name('dosen.dashboard');
        Route::post('/dosen/konsultasi/{consultation}', [PaDosenController::class, 'updateConsultation'])->name('dosen.consultations.update');
        Route::post('/dosen/konsultasi/{consultation}/pesan', [PaDosenController::class, 'storeMessage'])->name('dosen.consultations.messages.store');
        Route::get('/mahasiswa', fn () => redirect()->route('mahasiswa.pa.dashboard'))->name('mahasiswa.dashboard');
        Route::post('/mahasiswa/konsultasi', [PaMahasiswaController::class, 'storeConsultation'])->name('mahasiswa.consultations.store');
        Route::post('/mahasiswa/konsultasi/{consultation}/pesan', [PaMahasiswaController::class, 'storeMessage'])->name('mahasiswa.consultations.messages.store');
    });

    Route::get('/admin', [AdminController::class, 'dashboard'])->name('admin.dashboard');
    Route::get('/admin/profil', [AdminController::class, 'dashboard'])->name('admin.profile');
    Route::get('/admin/settings', [AdminSettingsController::class, 'edit'])->name('admin.settings');
    Route::put('/admin/settings', [AdminSettingsController::class, 'update'])->name('admin.settings.update');
    Route::get('/admin/export-report', [AdminReportController::class, 'export'])->name('admin.export-report');
    Route::get('/admin/update-kolektif', [KolektifUpdateController::class, 'index'])->name('admin.kolektif-update');
    Route::post('/admin/update-kolektif', [KolektifUpdateController::class, 'store'])->name('admin.kolektif-update.store');
    Route::post('/admin/pengajuan-ta/{guidanceRequest}/persetujuan', [AdminController::class, 'decideGuidanceRequest'])->name('admin.guidance-requests.decide');
    Route::get('/admin/database-ta', [AdminDatabaseTaController::class, 'index'])->name('admin.database-ta');
    Route::post('/admin/database-ta', [AdminDatabaseTaController::class, 'store'])->name('admin.database-ta.store');
    Route::get('/admin/database-ta/{record}/edit', [AdminDatabaseTaController::class, 'edit'])->name('admin.database-ta.edit');
    Route::put('/admin/database-ta/{record}', [AdminDatabaseTaController::class, 'update'])->name('admin.database-ta.update');
    Route::delete('/admin/database-ta/{record}', [AdminDatabaseTaController::class, 'destroy'])->name('admin.database-ta.destroy');

    Route::get('/admin/seminar-ujian', [AdminSeminarController::class, 'index'])->name('admin.seminars');
    Route::post('/admin/seminar-ujian', [AdminSeminarController::class, 'store'])->name('admin.seminars.store');
    Route::post('/admin/seminar-ujian/pengajuan/{seminarRequest}/persetujuan', [AdminSeminarController::class, 'decideSeminarRequest'])->name('admin.seminar-requests.decide');
    Route::get('/admin/seminar-ujian/{seminar}/edit', [AdminSeminarController::class, 'edit'])->name('admin.seminars.edit');
    Route::put('/admin/seminar-ujian/{seminar}', [AdminSeminarController::class, 'update'])->name('admin.seminars.update');
    Route::delete('/admin/seminar-ujian/{seminar}', [AdminSeminarController::class, 'destroy'])->name('admin.seminars.destroy');

    Route::get('/admin/bimbingan-pa', [AdminBimbinganPaController::class, 'index'])->name('admin.bimbingan-pa');
    Route::post('/admin/bimbingan-pa/assignments', [AdminBimbinganPaController::class, 'storeAssignment'])->name('admin.bimbingan-pa.assignments.store');
    Route::put('/admin/bimbingan-pa/assignments/{assignment}', [AdminBimbinganPaController::class, 'updateAssignment'])->name('admin.bimbingan-pa.assignments.update');
    Route::delete('/admin/bimbingan-pa/assignments/{assignment}', [AdminBimbinganPaController::class, 'destroyAssignment'])->name('admin.bimbingan-pa.assignments.destroy');
    Route::post('/admin/bimbingan-pa/records', [AdminBimbinganPaController::class, 'storeRecord'])->name('admin.bimbingan-pa.records.store');
    Route::put('/admin/bimbingan-pa/records/{record}', [AdminBimbinganPaController::class, 'updateRecord'])->name('admin.bimbingan-pa.records.update');
    Route::delete('/admin/bimbingan-pa/records/{record}', [AdminBimbinganPaController::class, 'destroyRecord'])->name('admin.bimbingan-pa.records.destroy');
    Route::post('/admin/bimbingan-pa/consultations', [AdminBimbinganPaController::class, 'storeConsultation'])->name('admin.bimbingan-pa.consultations.store');
    Route::put('/admin/bimbingan-pa/consultations/{consultation}', [AdminBimbinganPaController::class, 'updateConsultation'])->name('admin.bimbingan-pa.consultations.update');
    Route::delete('/admin/bimbingan-pa/consultations/{consultation}', [AdminBimbinganPaController::class, 'destroyConsultation'])->name('admin.bimbingan-pa.consultations.destroy');
});
