<?php

use App\Http\Controllers\Api\AuthController;
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

    // ---- Pengguna: kuis, dashboard, koleksi, profil (Bangkit) ----

    // ---- Admin (Dillakuusayang) ----
});
