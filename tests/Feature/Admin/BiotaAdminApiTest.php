<?php

use App\Models\Biota;
use App\Models\Kartu;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

function dataBiota(array $timpa = []): array
{
    return array_merge([
        'nama' => 'Penyu Uji',
        'nama_latin' => 'Testudo uji',
        'kategori' => 'Reptil Laut',
        'deskripsi' => 'Deskripsi uji',
        'latitude' => 2.2867,
        'longitude' => 118.2425,
    ], $timpa);
}

test('tamu 401 dan pengguna biasa 403', function () {
    $this->getJson('/api/v1/admin/biota')->assertUnauthorized();

    loginSebagaiUser();
    $this->getJson('/api/v1/admin/biota')->assertForbidden();
    $this->postJson('/api/v1/admin/biota', dataBiota())->assertForbidden();
});

test('daftar biota dipaginasi 10 dengan koordinat berupa angka', function () {
    Biota::factory()->count(12)->create();
    loginSebagaiAdmin();

    $res = $this->getJson('/api/v1/admin/biota')->assertOk()->assertJsonCount(10, 'data')->assertJsonPath('meta.total', 12);

    expect($res->json('data.0.latitude'))->toBeFloat();
});

test('opsi biota mengembalikan id dan nama semua biota urut nama', function () {
    Biota::factory()->create(['nama' => 'Zebra Laut']);
    Biota::factory()->create(['nama' => 'Anemon']);
    loginSebagaiAdmin();

    $this->getJson('/api/v1/admin/biota/opsi')
        ->assertOk()
        ->assertJsonCount(2, 'data')
        ->assertJsonPath('data.0.nama', 'Anemon')
        ->assertJsonStructure(['data' => ['*' => ['id', 'nama']]]);
});

test('opsi dengan tanpa_kartu hanya berisi biota yang belum punya kartu', function () {
    $punyaKartu = Biota::factory()->create(['nama' => 'Punya Kartu']);
    Kartu::factory()->create(['biota_id' => $punyaKartu->id]);
    Biota::factory()->create(['nama' => 'Belum Punya']);
    loginSebagaiAdmin();

    $this->getJson('/api/v1/admin/biota/opsi?tanpa_kartu=1')
        ->assertOk()
        ->assertJsonCount(1, 'data')
        ->assertJsonPath('data.0.nama', 'Belum Punya');
});

test('admin dapat menambah biota dengan gambar dan slug dibuat otomatis', function () {
    Storage::fake('public');
    loginSebagaiAdmin();

    $res = $this->post('/api/v1/admin/biota', dataBiota(['gambar' => UploadedFile::fake()->image('penyu.jpg', 600, 400)]), ['Accept' => 'application/json']);

    $res->assertCreated()->assertJsonPath('data.slug', 'penyu-uji');
    Storage::disk('public')->assertExists(Biota::first()->gambar);
    expect($res->json('data.gambar_url'))->toStartWith('http');
});

test('nama yang sama menghasilkan slug berbeda', function () {
    loginSebagaiAdmin();

    $this->postJson('/api/v1/admin/biota', dataBiota(['nama' => 'Ikan Sama']))->assertCreated();
    $this->postJson('/api/v1/admin/biota', dataBiota(['nama' => 'Ikan Sama']))->assertCreated();

    expect(Biota::pluck('slug')->sort()->values()->all())->toBe(['ikan-sama', 'ikan-sama-2']);
});

test('data tidak valid ditolak dengan galat per kolom', function () {
    loginSebagaiAdmin();

    $this->postJson('/api/v1/admin/biota', dataBiota(['nama' => '', 'latitude' => 200, 'kategori' => '']))
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['nama', 'latitude', 'kategori']);
});

test('file yang bukan gambar dan gambar terlalu besar ditolak', function () {
    loginSebagaiAdmin();

    $this->post('/api/v1/admin/biota', dataBiota(['gambar' => UploadedFile::fake()->create('virus.php', 10, 'application/x-php')]), ['Accept' => 'application/json'])
        ->assertJsonValidationErrors('gambar');

    $this->post('/api/v1/admin/biota', dataBiota(['gambar' => UploadedFile::fake()->image('besar.jpg')->size(3000)]), ['Accept' => 'application/json'])
        ->assertJsonValidationErrors('gambar');
});

test('ubah tanpa file mempertahankan gambar dan slug lama', function () {
    $biota = Biota::factory()->create(['slug' => 'slug-lama', 'gambar' => 'biota/lama.jpg']);
    loginSebagaiAdmin();

    $this->putJson("/api/v1/admin/biota/{$biota->id}", dataBiota(['nama' => 'Nama Baru']))
        ->assertOk()
        ->assertJsonPath('data.nama', 'Nama Baru');

    expect($biota->fresh()->slug)->toBe('slug-lama')->and($biota->fresh()->gambar)->toBe('biota/lama.jpg');
});

test('ubah dengan file baru lewat post dan _method put mengganti file lama', function () {
    Storage::fake('public');
    Storage::disk('public')->put('biota/lama.jpg', 'x');
    $biota = Biota::factory()->create(['gambar' => 'biota/lama.jpg']);
    loginSebagaiAdmin();

    $this->post("/api/v1/admin/biota/{$biota->id}", dataBiota(['_method' => 'PUT', 'gambar' => UploadedFile::fake()->image('baru.jpg')]), ['Accept' => 'application/json'])
        ->assertOk();

    Storage::disk('public')->assertMissing('biota/lama.jpg');
    Storage::disk('public')->assertExists($biota->fresh()->gambar);
});

test('hapus biota menghapus kartunya dan membersihkan file yang tidak lagi dipakai', function () {
    Storage::fake('public');
    Storage::disk('public')->put('biota/a.jpg', 'x');
    $biota = Biota::factory()->create(['gambar' => 'biota/a.jpg']);
    $kartu = Kartu::factory()->create(['biota_id' => $biota->id, 'gambar' => 'biota/a.jpg']);
    loginSebagaiAdmin();

    $this->deleteJson("/api/v1/admin/biota/{$biota->id}")->assertNoContent();

    $this->assertDatabaseMissing('biota', ['id' => $biota->id]);
    $this->assertDatabaseMissing('kartu', ['id' => $kartu->id]);
    Storage::disk('public')->assertMissing('biota/a.jpg');
});

test('id yang tidak ada menghasilkan 404 dan slug tidak dipakai sebagai kunci', function () {
    $biota = Biota::factory()->create(['slug' => 'penyu-hijau']);
    loginSebagaiAdmin();

    $this->getJson('/api/v1/admin/biota/99999')->assertNotFound();
    $this->getJson('/api/v1/admin/biota/penyu-hijau')->assertNotFound();   // rute admin memakai id, bukan slug
    $this->getJson("/api/v1/admin/biota/{$biota->id}")->assertOk()->assertJsonPath('data.slug', 'penyu-hijau');
});
