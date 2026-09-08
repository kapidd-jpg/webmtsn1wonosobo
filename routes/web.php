<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\PortalController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AnnouncementController;
use App\Http\Controllers\Api\ExtracurricularController;
use App\Http\Controllers\Api\ScheduleController;
use App\Http\Controllers\Api\StelaController;
use App\Http\Controllers\Api\CounselingController;
use App\Http\Controllers\Api\KesiswaanController;

Route::get('/', [PortalController::class, 'index'])->name('portal.index');

Route::post('/login', [AuthController::class, 'login'])->name('login');
Route::post('/logout', [AuthController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

// Data publik — siapa saja boleh baca
Route::get('/api/pengumuman', [AnnouncementController::class, 'index']);
Route::get('/api/ekstrakurikuler', [ExtracurricularController::class, 'index']);
Route::get('/api/jadwal', [ScheduleController::class, 'index']);
Route::post('/api/stela', [StelaController::class, 'reply']);
Route::post('/api/konseling', [CounselingController::class, 'store']);
Route::get('/api/kesiswaan', [KesiswaanController::class, 'index']);

// Kelola data — hanya guru yang login
Route::middleware(['auth', 'guru'])->group(function () {
    Route::post('/api/pengumuman', [AnnouncementController::class, 'store']);
    Route::put('/api/pengumuman/{announcement}', [AnnouncementController::class, 'update']);
    Route::delete('/api/pengumuman/{announcement}', [AnnouncementController::class, 'destroy']);

    Route::post('/api/ekstrakurikuler', [ExtracurricularController::class, 'store']);
    Route::put('/api/ekstrakurikuler/{extracurricular}', [ExtracurricularController::class, 'update']);
    Route::delete('/api/ekstrakurikuler/{extracurricular}', [ExtracurricularController::class, 'destroy']);

    Route::post('/api/jadwal', [ScheduleController::class, 'store']);
    Route::put('/api/jadwal/{schedule}', [ScheduleController::class, 'update']);
    Route::delete('/api/jadwal/{schedule}', [ScheduleController::class, 'destroy']);

    Route::get('/api/konseling', [CounselingController::class, 'index']);

    Route::post('/api/kesiswaan', [KesiswaanController::class, 'store']);
    Route::put('/api/kesiswaan/{kesiswaanItem}', [KesiswaanController::class, 'update']);
    Route::delete('/api/kesiswaan/{kesiswaanItem}', [KesiswaanController::class, 'destroy']);
});