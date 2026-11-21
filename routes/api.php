<?php

use App\Http\Controllers\Api\Admin\BiotaAdminController;
use App\Http\Controllers\Api\Admin\EdukasiAdminController;
use App\Http\Controllers\Api\Admin\SoalAdminController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BerandaController;
use App\Http\Controllers\Api\BiotaController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\EdukasiController;
use App\Http\Controllers\Api\KoleksiController;
use App\Http\Controllers\Api\KuisController;
use App\Http\Controllers\Api\LeaderboardController;
use App\Http\Controllers\Api\ProfilController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    // ---- Auth (Bangkit) ----
    Route::post('/auth/register', [AuthController::class, 'register'])->middleware('throttle:10,1');
    Route::post('/auth/login', [AuthController::class, 'login'])->middleware('throttle:10,1');

    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/auth/me', [AuthController::class, 'me']);
        Route::post('/auth/logout', [AuthController::class, 'logout']);
    });

    // ---- Publik (Dila) ----
    Route::get('/biota', [BiotaController::class, 'index']);
    Route::get('/biota/kategori', [BiotaController::class, 'kategori']);
    Route::get('/biota/{biota:slug}', [BiotaController::class, 'show']);
    Route::get('/edukasi', [EdukasiController::class, 'index']);
    Route::get('/edukasi/{konten:slug}', [EdukasiController::class, 'show']);
    Route::get('/beranda', [BerandaController::class, 'index']);
    Route::get('/leaderboard', [LeaderboardController::class, 'index']);

    // ---- Pengguna: kuis, dashboard, koleksi, profil (Bangkit dan Dila) ----
    Route::middleware('auth:sanctum')->group(function () {
        // Pembatas laju 'kuis' (60 permintaan per menit per pengguna) didefinisikan di AppServiceProvider
        Route::middleware('throttle:kuis')->group(function () {
            Route::post('/kuis/mulai', [KuisController::class, 'mulai']);
            Route::post('/kuis/{game}/jawab', [KuisController::class, 'jawab']);
            Route::post('/kuis/{game}/selesai', [KuisController::class, 'selesai']);
        });

        // dashboard (Dila, D4b)
        Route::get('/dashboard', [DashboardController::class, 'index']);

        // koleksi dan profil (Bangkit, B6 dan B7)
        Route::get('/koleksi', [KoleksiController::class, 'index']);
        Route::put('/profil', [ProfilController::class, 'update']);
    });

    // ---- Admin (Dila dan Bangkit) ----
    Route::middleware(['auth:sanctum', 'role:admin'])->prefix('admin')->group(function () {
        // dashboard (Dila, D6)

        // biota dan edukasi (Dila, D5)
        Route::get('/biota', [BiotaAdminController::class, 'index']);
        Route::get('/biota/opsi', [BiotaAdminController::class, 'opsi']);   // WAJIB sebelum /biota/{biota:id}
        Route::post('/biota', [BiotaAdminController::class, 'store']);
        Route::get('/biota/{biota:id}', [BiotaAdminController::class, 'show']);
        Route::put('/biota/{biota:id}', [BiotaAdminController::class, 'update']);   // dengan file: POST + _method=PUT
        Route::delete('/biota/{biota:id}', [BiotaAdminController::class, 'destroy']);

        Route::get('/edukasi', [EdukasiAdminController::class, 'index']);
        Route::post('/edukasi', [EdukasiAdminController::class, 'store']);
        Route::get('/edukasi/{konten:id}', [EdukasiAdminController::class, 'show']);
        Route::put('/edukasi/{konten:id}', [EdukasiAdminController::class, 'update']);
        Route::delete('/edukasi/{konten:id}', [EdukasiAdminController::class, 'destroy']);

        // soal (Bangkit, B9)
        Route::get('/soal', [SoalAdminController::class, 'index']);
        Route::post('/soal', [SoalAdminController::class, 'store']);
        Route::get('/soal/{soal:id}', [SoalAdminController::class, 'show']);
        Route::put('/soal/{soal:id}', [SoalAdminController::class, 'update']);   // klien dengan file: POST + _method=PUT
        Route::delete('/soal/{soal:id}', [SoalAdminController::class, 'destroy']);

        // kartu (Bangkit, B10)
    });
});
