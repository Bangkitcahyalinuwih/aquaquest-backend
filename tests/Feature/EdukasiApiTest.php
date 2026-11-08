<?php

use App\Models\Biota;
use App\Models\KontenEdukasi;

test('daftar edukasi dipaginasi dengan ringkasan', function () {
    KontenEdukasi::factory()->count(10)->create();

    $this->getJson('/api/v1/edukasi')
        ->assertOk()
        ->assertJsonCount(9, 'data')
        ->assertJsonStructure(['data' => ['*' => ['slug', 'judul', 'ringkasan', 'gambar_url', 'created_at']], 'meta' => ['total']])
        ->assertJsonMissingPath('data.0.isi');
});

test('pencarian edukasi menyaring judul', function () {
    KontenEdukasi::factory()->create(['judul' => 'Tentang Lamun', 'slug' => 'tentang-lamun']);
    KontenEdukasi::factory()->create(['judul' => 'Tentang Plastik', 'slug' => 'tentang-plastik', 'isi' => 'abc']);

    $this->getJson('/api/v1/edukasi?q=lamun')->assertJsonCount(1, 'data')->assertJsonPath('data.0.slug', 'tentang-lamun');
});

test('detail edukasi umum memiliki biota null', function () {
    KontenEdukasi::factory()->create(['slug' => 'umum', 'biota_id' => null]);

    $this->getJson('/api/v1/edukasi/umum')->assertOk()->assertJsonPath('data.biota', null);
});

test('detail edukasi terkait biota memuat slug dan nama biota', function () {
    $biota = Biota::factory()->create(['slug' => 'penyu-hijau', 'nama' => 'Penyu Hijau']);
    KontenEdukasi::factory()->create(['slug' => 'tentang-penyu', 'biota_id' => $biota->id]);

    $this->getJson('/api/v1/edukasi/tentang-penyu')
        ->assertOk()
        ->assertJsonPath('data.biota.slug', 'penyu-hijau')
        ->assertJsonPath('data.biota.nama', 'Penyu Hijau')
        ->assertJsonStructure(['data' => ['slug', 'judul', 'isi']]);
});

test('edukasi yang tidak ada menghasilkan 404', function () {
    $this->getJson('/api/v1/edukasi/tidak-ada')->assertNotFound();
});
