<?php

use App\Models\Biota;

test('daftar biota dipaginasi sesuai kontrak', function () {
    Biota::factory()->count(12)->create();

    $this->getJson('/api/v1/biota')
        ->assertOk()
        ->assertJsonCount(9, 'data')
        ->assertJsonStructure([
            'data' => ['*' => ['slug', 'nama', 'nama_latin', 'kategori', 'status_konservasi', 'gambar_url', 'deskripsi_singkat']],
            'links',
            'meta' => ['current_page', 'last_page', 'per_page', 'total'],
        ])
        ->assertJsonPath('meta.total', 12)
        ->assertJsonPath('meta.last_page', 2)
        ->assertJsonMissingPath('data.0.deskripsi');   // daftar tidak membawa teks lengkap
});

test('halaman kedua berisi sisa data', function () {
    Biota::factory()->count(12)->create();

    $this->getJson('/api/v1/biota?page=2')->assertOk()->assertJsonCount(3, 'data');
});

test('pencarian menyaring nama dan nama latin', function () {
    Biota::factory()->create(['nama' => 'Penyu Hijau', 'nama_latin' => 'Chelonia mydas', 'slug' => 'penyu-hijau']);
    Biota::factory()->create(['nama' => 'Ikan Badut', 'nama_latin' => 'Amphiprion ocellaris', 'slug' => 'ikan-badut']);

    $this->getJson('/api/v1/biota?q=penyu')->assertJsonCount(1, 'data')->assertJsonPath('data.0.slug', 'penyu-hijau');
    $this->getJson('/api/v1/biota?q=amphiprion')->assertJsonCount(1, 'data')->assertJsonPath('data.0.slug', 'ikan-badut');
});

test('filter kategori menyaring hasil', function () {
    Biota::factory()->create(['kategori' => 'Ikan', 'slug' => 'a']);
    Biota::factory()->create(['kategori' => 'Moluska', 'slug' => 'b']);

    $this->getJson('/api/v1/biota?kategori=Moluska')->assertJsonCount(1, 'data')->assertJsonPath('data.0.slug', 'b');
});

test('endpoint kategori tidak tertukar dengan slug', function () {
    Biota::factory()->create(['kategori' => 'Ikan', 'slug' => 'a']);
    Biota::factory()->create(['kategori' => 'Moluska', 'slug' => 'b']);

    $this->getJson('/api/v1/biota/kategori')->assertOk()->assertExactJson(['data' => ['Ikan', 'Moluska']]);
});

test('detail biota berisi data lengkap dengan koordinat berupa angka', function () {
    $biota = Biota::factory()->create(['slug' => 'penyu-hijau', 'latitude' => 2.2867, 'longitude' => 118.2425, 'gambar' => null]);

    $res = $this->getJson('/api/v1/biota/penyu-hijau')->assertOk();

    $res->assertJsonPath('data.id', $biota->id)
        ->assertJsonStructure(['data' => ['id', 'slug', 'nama', 'habitat', 'deskripsi', 'latitude', 'longitude', 'gambar_url']])
        ->assertJsonPath('data.gambar_url', null);

    expect($res->json('data.latitude'))->toBeFloat();
});

test('slug yang tidak ada menghasilkan 404 json', function () {
    $this->getJson('/api/v1/biota/tidak-ada')->assertNotFound();
});

test('parameter pencarian divalidasi', function () {
    $this->getJson('/api/v1/biota?page=0')->assertUnprocessable()->assertJsonValidationErrors('page');
});
