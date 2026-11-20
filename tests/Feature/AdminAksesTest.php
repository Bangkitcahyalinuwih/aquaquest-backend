<?php

use Illuminate\Support\Facades\Route;

// Route uji dipasang sementara hanya selama test ini berjalan.
beforeEach(function () {
    Route::middleware(['auth:sanctum', 'role:admin'])
        ->get('/api/v1/_uji-admin', fn () => response()->json(['ok' => true]));
});

test('tamu mendapat 401 pada route admin', function () {
    $this->getJson('/api/v1/_uji-admin')->assertUnauthorized();
});

test('pengguna biasa mendapat 403 pada route admin', function () {
    loginSebagaiUser();

    $this->getJson('/api/v1/_uji-admin')->assertForbidden();
});

test('admin dapat mengakses route admin', function () {
    loginSebagaiAdmin();

    $this->getJson('/api/v1/_uji-admin')->assertOk()->assertJson(['ok' => true]);
});
