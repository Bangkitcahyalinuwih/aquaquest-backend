<?php

use App\Models\Biota;
use App\Models\KontenEdukasi;
use App\Models\SoalKuis;
use App\Models\User;

test('beranda berisi statistik, tiga biota, dan tiga edukasi', function () {
    Biota::factory()->count(5)->create();
    KontenEdukasi::factory()->count(5)->create();
    SoalKuis::factory()->count(4)->create();
    User::factory()->count(2)->create();
    User::factory()->create(['role' => 'admin']);   // admin tidak dihitung sebagai pemain

    $this->getJson('/api/v1/beranda')
        ->assertOk()
        ->assertJsonPath('data.statistik.jumlah_biota', 5)
        ->assertJsonPath('data.statistik.jumlah_soal', 4)
        ->assertJsonPath('data.statistik.jumlah_pemain', 2)
        ->assertJsonCount(3, 'data.biota_pilihan')
        ->assertJsonCount(3, 'data.edukasi_terbaru')
        ->assertJsonStructure(['data' => ['biota_pilihan' => ['*' => ['slug', 'nama', 'gambar_url', 'deskripsi_singkat']]]]);
});
