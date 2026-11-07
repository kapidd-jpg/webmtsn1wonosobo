<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\PortalController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AccountController;
use App\Http\Controllers\Api\AnnouncementController;
use App\Http\Controllers\Api\ExtracurricularController;
use App\Http\Controllers\Api\ScheduleController;
use App\Http\Controllers\Api\StelaController;
use App\Http\Controllers\Api\CounselingController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\KesiswaanController;
use App\Http\Controllers\Api\ExamController;
use App\Http\Controllers\Api\StudentController;
use App\Http\Controllers\Api\SchoolProfileController;

Route::get('/', [PortalController::class, 'index'])->name('portal.index');

// Halaman login berupa modal di portal. Route GET ini dipakai sebagai
// tujuan redirect saat pengguna yang belum login membuka halaman terkunci.
Route::get('/login', function () {
    return redirect()->route('portal.index')->withFragment('login');
})->name('login.show');

Route::post('/login', [AuthController::class, 'login'])->name('login');

// Token CSRF segar untuk halaman yang sudah lama terbuka. Dipanggil oleh
// script1.js secara berkala agar formulir tidak terkena 419 Page Expired.
Route::get('/csrf-token', function () {
    return response()
        ->json(['token' => csrf_token()])
        ->header('Cache-Control', 'no-store, no-cache, must-revalidate');
})->name('csrf.token');
Route::post('/logout', [AuthController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

// Data publik — siapa saja boleh baca
Route::get('/api/pengumuman', [AnnouncementController::class, 'index']);
Route::get('/api/ekstrakurikuler', [ExtracurricularController::class, 'index']);
Route::post('/api/stela', [StelaController::class, 'reply'])->middleware('throttle:20,1');
Route::post('/api/konseling', [CounselingController::class, 'store']);
Route::get('/api/kesiswaan', [KesiswaanController::class, 'index']);
Route::get('/api/profil-sekolah', [SchoolProfileController::class, 'show']);

// Data akademik bersifat personal (per kelas) — wajib login
Route::middleware('auth')->group(function () {
    Route::get('/api/me', [StudentController::class, 'me']);
    Route::get('/api/jadwal', [ScheduleController::class, 'index']);
    Route::get('/api/ujian', [ExamController::class, 'index']);

    // Ubah password akun sendiri
    Route::put('/api/akun/password', [AccountController::class, 'updatePassword'])
        ->middleware('throttle:6,1');
});

// Ringkasan dashboard guru kesiswaan & guru BK
Route::middleware(['auth', 'role:guru,guru_bk,admin'])->group(function () {
    Route::get('/api/dashboard-guru', [DashboardController::class, 'stats']);
});

// Data siswa untuk guru kesiswaan (hanya baca)
Route::middleware(['auth', 'role:guru'])->group(function () {
    Route::get('/api/siswa-kesiswaan', [StudentController::class, 'indexForGuru']);
    Route::get('/api/wali-kelas', [StudentController::class, 'waliKelasList']);
});

// Kelola konten portal — khusus admin
Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::post('/api/pengumuman', [AnnouncementController::class, 'store']);
    Route::put('/api/pengumuman/{announcement}', [AnnouncementController::class, 'update']);
    Route::delete('/api/pengumuman/{announcement}', [AnnouncementController::class, 'destroy']);

    Route::post('/api/ekstrakurikuler', [ExtracurricularController::class, 'store']);
    Route::put('/api/ekstrakurikuler/{extracurricular}', [ExtracurricularController::class, 'update']);
    Route::delete('/api/ekstrakurikuler/{extracurricular}', [ExtracurricularController::class, 'destroy']);
    Route::get('/api/ekstrakurikuler/gambar', [ExtracurricularController::class, 'images']);

    Route::post('/api/jadwal', [ScheduleController::class, 'store']);
    Route::put('/api/jadwal/{schedule}', [ScheduleController::class, 'update']);
    Route::delete('/api/jadwal/{schedule}', [ScheduleController::class, 'destroy']);

    Route::post('/api/kesiswaan', [KesiswaanController::class, 'store']);
    Route::put('/api/kesiswaan/{kesiswaanItem}', [KesiswaanController::class, 'update']);
    Route::delete('/api/kesiswaan/{kesiswaanItem}', [KesiswaanController::class, 'destroy']);

    Route::post('/api/ujian', [ExamController::class, 'store']);
    Route::put('/api/ujian/{exam}', [ExamController::class, 'update']);
    Route::delete('/api/ujian/{exam}', [ExamController::class, 'destroy']);

    Route::get('/api/siswa', [StudentController::class, 'index']);
    Route::put('/api/siswa/{user}', [StudentController::class, 'update']);

    // Kelola akun & reset password — khusus admin
    Route::get('/api/akun', [AccountController::class, 'index']);
    Route::put('/api/akun/{user}/password', [AccountController::class, 'resetPassword'])
        ->middleware('throttle:20,1');

    Route::put('/api/profil-sekolah', [SchoolProfileController::class, 'update']);
});

// Kelola Konseling — guru BK & admin
Route::middleware(['auth', 'role:guru_bk,admin'])->group(function () {
    Route::get('/api/konseling', [CounselingController::class, 'index']);
    Route::put('/api/konseling/{counselingSubmission}', [CounselingController::class, 'updateStatus']);
});

// Buat akun baru — khusus admin (maintenance)
Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::post('/api/siswa', [StudentController::class, 'store']);
});
