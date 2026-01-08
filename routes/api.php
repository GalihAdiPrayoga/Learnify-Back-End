<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\KelasController;
use App\Http\Controllers\MateriController;
use App\Http\Controllers\SoalController;
use App\Http\Controllers\HasilUjianController;
use App\Http\Controllers\JawabanUserController;
use App\Http\Controllers\ProfilController;

// Public routes
Route::post('/registrasi', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {

    Route::post('/logout', [AuthController::class, 'logout']);

    // Progress routes - HARUS DI ATAS RESOURCE ROUTES
    Route::prefix('kelas')->group(function () {
        Route::get('my-courses', [KelasController::class, 'myCourses']);
        Route::post('{kelas}/start', [KelasController::class, 'startCourse']);
        Route::delete('{kelas}/cancel', [KelasController::class, 'cancelCourse']);
        Route::post('{kelas}/toggle-material', [KelasController::class, 'toggleMaterial']);
    });

    // Admin routes
    Route::middleware('role:Admin')->group(function () {
        Route::apiResource('profil', ProfilController::class);
        Route::apiResource('kelas', KelasController::class)->parameters(['kelas' => 'kelas']);
        Route::apiResource('materi', MateriController::class);
        Route::apiResource('soal', SoalController::class);
        Route::apiResource('hasil-ujian', HasilUjianController::class);

        // Admin dapat mengakses materi by kelas untuk form soal
        Route::get('/materi/kelas/{kelas_id}', [MateriController::class, 'getByKelas']);
    });

    // User routes
    Route::middleware('role:User')->prefix('user')->group(function () {
        // User mengelola profil sendiri
        Route::get('/profil', [ProfilController::class, 'showOwn']);
        Route::put('/profil', [ProfilController::class, 'updateOwn']);

        // User melihat kelas & materi
        Route::get('/kelas', [KelasController::class, 'index']);
        Route::get('/materi/kelas/{kelas_id}', [MateriController::class, 'getByKelas']);
        Route::get('/materi/{id}', [MateriController::class, 'getDetailForUser']);
        Route::get('/soal/materi/{materi_id}', [SoalController::class, 'getByMateri']);

        // Ujian flow
        Route::post('/hasil-ujian/start', [HasilUjianController::class, 'start']);
        Route::post('/jawaban', [JawabanUserController::class, 'store']);
        Route::post('/hasil-ujian/finish', [HasilUjianController::class, 'finish']);
    });
});
