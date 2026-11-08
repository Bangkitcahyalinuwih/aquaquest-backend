<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BiotaController;
use App\Http\Controllers\Api\EdukasiController;
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

    // ---- Pengguna: kuis, dashboard, koleksi, profil (Bangkit) ----

    // ---- Admin (Dillakuusayang) ----
});
