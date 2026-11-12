<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BerandaController;
use App\Http\Controllers\Api\BiotaController;
use App\Http\Controllers\Api\EdukasiController;
use App\Http\Controllers\Api\KuisController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    // ---- Auth (Bangkit) ----
    Route::post('/auth/register', [AuthController::class, 'register'])->middleware('throttle:10,1');
    Route::post('/auth/login', [AuthController::class, 'login'])->middleware('throttle:10,1');

    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/auth/me', [AuthController::class, 'me']);
        Route::post('/auth/logout', [AuthController::class, 'logout']);
    });

    // ---- Publik (Dillakuusayang) ----
    Route::get('/biota', [BiotaController::class, 'index']);
    Route::get('/biota/kategori', [BiotaController::class, 'kategori']);
    Route::get('/biota/{biota:slug}', [BiotaController::class, 'show']);
    Route::get('/edukasi', [EdukasiController::class, 'index']);
    Route::get('/edukasi/{konten:slug}', [EdukasiController::class, 'show']);
    Route::get('/beranda', [BerandaController::class, 'index']);

    // ---- Pengguna: kuis, dashboard, koleksi, profil (Bangkit) ----
    Route::middleware('auth:sanctum')->group(function () {
        // Pembatas laju 'kuis' (60 permintaan per menit per pengguna) didefinisikan di AppServiceProvider
        Route::middleware('throttle:kuis')->group(function () {
            Route::post('/kuis/mulai', [KuisController::class, 'mulai']);
            Route::post('/kuis/{game}/jawab', [KuisController::class, 'jawab']);
            Route::post('/kuis/{game}/selesai', [KuisController::class, 'selesai']);
        });

        // ---- Admin (Dillakuusayang) ----

        // dashboard (Dila, D4b)

        // koleksi dan profil (Bangkit, B6 dan B7)
    });
});
