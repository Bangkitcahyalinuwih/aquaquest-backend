<?php

use App\Models\Kartu;
use App\Models\KoleksiKartu;
use App\Models\User;
use Laravel\Sanctum\Sanctum;

test('koleksi menandai kartu terbuka dari tabel koleksi dan terurut menurut xp', function () {
    $user = User::factory()->create();
    $murah = Kartu::factory()->create(['xp_syarat' => 50, 'nama' => 'Kartu Murah']);
    Kartu::factory()->create(['xp_syarat' => 100, 'nama' => 'Kartu Mahal']);
    KoleksiKartu::create(['user_id' => $user->id, 'kartu_id' => $murah->id, 'didapat_at' => now()]);
    Sanctum::actingAs($user);

    $this->getJson('/api/v1/koleksi')
        ->assertOk()
        ->assertJsonStructure(['data' => ['total_xp', 'kartu' => ['*' => ['id', 'nama', 'xp_syarat', 'gambar_url', 'biota', 'terbuka']]]])
        ->assertJsonPath('data.kartu.0.nama', 'Kartu Murah')
        ->assertJsonPath('data.kartu.0.terbuka', true)
        ->assertJsonPath('data.kartu.1.terbuka', false);
});

test('koleksi milik pemain lain tidak ikut terhitung', function () {
    $user = User::factory()->create();
    $lain = User::factory()->create();
    $kartu = Kartu::factory()->create();
    KoleksiKartu::create(['user_id' => $lain->id, 'kartu_id' => $kartu->id, 'didapat_at' => now()]);
    Sanctum::actingAs($user);

    $this->getJson('/api/v1/koleksi')->assertJsonPath('data.kartu.0.terbuka', false);
});

test('tamu tidak dapat melihat koleksi', function () {
    $this->getJson('/api/v1/koleksi')->assertUnauthorized();
});
