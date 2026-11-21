<?php

use App\Models\Biota;
use App\Models\GameHistory;
use App\Models\Kartu;
use App\Models\KontenEdukasi;
use App\Models\SoalKuis;
use App\Models\User;

test('tamu 401 dan pengguna biasa 403', function () {
    $this->getJson('/api/v1/admin/dashboard')->assertUnauthorized();

    loginSebagaiUser();
    $this->getJson('/api/v1/admin/dashboard')->assertForbidden();
});

test('dashboard admin berisi statistik, pemain teratas, dan sesi terbaru', function () {
    Biota::factory()->count(3)->create();
    KontenEdukasi::factory()->count(2)->create();
    SoalKuis::factory()->count(4)->create();
    Kartu::factory()->count(1)->create();

    $a = User::factory()->create(['name' => 'Juara']);
    User::factory()->create(['name' => 'Biasa']);
    User::whereKey($a->id)->update(['total_xp' => 300, 'level' => 4]);

    GameHistory::create([
        'user_id' => $a->id,
        'mulai_at' => now(),
        'deadline_at' => now(),
        'selesai_at' => now(),
        'status' => 'selesai',
        'soal_ids' => [],
        'jawaban' => [],
        'jumlah_benar' => 4,
    ]);
    loginSebagaiAdmin();   // admin tidak dihitung sebagai pemain

    $this->getJson('/api/v1/admin/dashboard')
        ->assertOk()
        ->assertJsonPath('data.jumlah_edukasi', 2)
        ->assertJsonPath('data.jumlah_soal', 4)
        ->assertJsonPath('data.jumlah_pemain', 2)
        ->assertJsonPath('data.sesi_selesai', 1)
        ->assertJsonPath('data.pemain_teratas.0.name', 'Juara')
        ->assertJsonPath('data.sesi_terbaru.0.pemain', 'Juara')
        ->assertJsonPath('data.sesi_terbaru.0.jumlah_benar', 4)
        ->assertJsonStructure(['data' => ['jumlah_biota', 'jumlah_kartu', 'pemain_teratas' => ['*' => ['id', 'name', 'level', 'total_xp']], 'sesi_terbaru' => ['*' => ['pemain', 'jumlah_benar', 'selesai_at']]]]);
});
