<?php

use App\Models\SoalKuis;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

function dataSoal(array $timpa = []): array
{
    return array_merge([
        'tipe' => 'teks',
        'pertanyaan' => 'Apa nama ilmiah penyu hijau?',
        'opsi_a' => 'Chelonia mydas',
        'opsi_b' => 'B',
        'opsi_c' => 'C',
        'opsi_d' => 'D',
        'jawaban_benar' => 'a',
        'poin' => 10,
    ], $timpa);
}

test('tamu 401 dan pengguna biasa 403', function () {
    $this->getJson('/api/v1/admin/soal')->assertUnauthorized();

    loginSebagaiUser();
    $this->getJson('/api/v1/admin/soal')->assertForbidden();
});

test('daftar soal dipaginasi 10 dan memuat kunci jawaban untuk admin', function () {
    SoalKuis::factory()->count(12)->create();
    loginSebagaiAdmin();

    $this->getJson('/api/v1/admin/soal')
        ->assertOk()
        ->assertJsonCount(10, 'data')
        ->assertJsonPath('meta.total', 12)
        ->assertJsonStructure(['data' => ['*' => ['id', 'biota_id', 'biota', 'tipe', 'pertanyaan', 'gambar_url', 'opsi_a', 'opsi_b', 'opsi_c', 'opsi_d', 'jawaban_benar', 'poin']]]);
});

test('admin dapat menambah soal teks', function () {
    loginSebagaiAdmin();

    $this->postJson('/api/v1/admin/soal', dataSoal())
        ->assertCreated()
        ->assertJsonPath('data.pertanyaan', 'Apa nama ilmiah penyu hijau?')
        ->assertJsonPath('data.gambar_url', null);

    $this->assertDatabaseHas('soal_kuis', ['pertanyaan' => 'Apa nama ilmiah penyu hijau?', 'jawaban_benar' => 'a']);
});

test('soal bertipe gambar wajib memiliki gambar', function () {
    loginSebagaiAdmin();

    $this->postJson('/api/v1/admin/soal', dataSoal(['tipe' => 'gambar']))
        ->assertUnprocessable()
        ->assertJsonValidationErrors('gambar');
});

test('soal tipe gambar dengan file tersimpan dan gambar_url terisi', function () {
    Storage::fake('public');
    loginSebagaiAdmin();

    $res = $this->post('/api/v1/admin/soal', dataSoal(['tipe' => 'gambar', 'gambar' => UploadedFile::fake()->image('hewan.jpg')]), ['Accept' => 'application/json']);

    $res->assertCreated();
    Storage::disk('public')->assertExists(SoalKuis::first()->gambar);
    expect($res->json('data.gambar_url'))->toStartWith('http');
});

test('file yang bukan gambar ditolak', function () {
    loginSebagaiAdmin();

    $this->post('/api/v1/admin/soal', dataSoal(['gambar' => UploadedFile::fake()->create('virus.php', 10, 'application/x-php')]), ['Accept' => 'application/json'])
        ->assertUnprocessable()
        ->assertJsonValidationErrors('gambar');
});

test('data tidak valid ditolak dengan galat per kolom', function () {
    loginSebagaiAdmin();

    $this->postJson('/api/v1/admin/soal', dataSoal(['jawaban_benar' => 'z', 'poin' => 500, 'opsi_a' => '']))
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['jawaban_benar', 'poin', 'opsi_a']);
});

test('ubah tanpa file baru mempertahankan gambar lama', function () {
    Storage::fake('public');
    Storage::disk('public')->put('soal/lama.jpg', 'x');
    $soal = SoalKuis::factory()->create(['tipe' => 'gambar', 'gambar' => 'soal/lama.jpg']);
    loginSebagaiAdmin();

    $this->putJson("/api/v1/admin/soal/{$soal->id}", dataSoal(['tipe' => 'gambar', 'pertanyaan' => 'Pertanyaan baru?']))->assertOk();

    expect($soal->fresh()->gambar)->toBe('soal/lama.jpg')->and($soal->fresh()->pertanyaan)->toBe('Pertanyaan baru?');
    Storage::disk('public')->assertExists('soal/lama.jpg');
});

test('ubah dengan file baru lewat post dan _method put mengganti file lama', function () {
    Storage::fake('public');
    Storage::disk('public')->put('soal/lama.jpg', 'x');
    $soal = SoalKuis::factory()->create(['tipe' => 'gambar', 'gambar' => 'soal/lama.jpg']);
    loginSebagaiAdmin();

    $this->post("/api/v1/admin/soal/{$soal->id}", dataSoal(['_method' => 'PUT', 'tipe' => 'gambar', 'gambar' => UploadedFile::fake()->image('baru.jpg')]), ['Accept' => 'application/json'])
        ->assertOk();

    Storage::disk('public')->assertMissing('soal/lama.jpg');
    Storage::disk('public')->assertExists($soal->fresh()->gambar);
});

test('hapus soal menghapus record dan filenya', function () {
    Storage::fake('public');
    Storage::disk('public')->put('soal/a.jpg', 'x');
    $soal = SoalKuis::factory()->create(['tipe' => 'gambar', 'gambar' => 'soal/a.jpg']);
    loginSebagaiAdmin();

    $this->deleteJson("/api/v1/admin/soal/{$soal->id}")->assertNoContent();

    $this->assertDatabaseMissing('soal_kuis', ['id' => $soal->id]);
    Storage::disk('public')->assertMissing('soal/a.jpg');
});

test('soal yang tidak ada menghasilkan 404', function () {
    loginSebagaiAdmin();

    $this->getJson('/api/v1/admin/soal/99999')->assertNotFound();
});
