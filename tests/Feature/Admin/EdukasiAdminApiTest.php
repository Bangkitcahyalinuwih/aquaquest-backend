<?php

use App\Models\Biota;
use App\Models\KontenEdukasi;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

const AKSEP_EDUKASI = ['Accept' => 'application/json'];

test('tamu 401 dan pengguna biasa 403', function () {
    $this->getJson('/api/v1/admin/edukasi')->assertUnauthorized();

    loginSebagaiUser();
    $this->getJson('/api/v1/admin/edukasi')->assertForbidden();
});

test('daftar edukasi memuat biota atau null', function () {
    $biota = Biota::factory()->create(['nama' => 'Dugong']);
    KontenEdukasi::factory()->create(['biota_id' => $biota->id]);
    KontenEdukasi::factory()->create(['biota_id' => null]);
    loginSebagaiAdmin();

    $res = $this->getJson('/api/v1/admin/edukasi')->assertOk()->assertJsonCount(2, 'data');

    expect(collect($res->json('data'))->pluck('biota')->filter()->first()['nama'])->toBe('Dugong');
});

test('admin dapat menambah artikel umum dengan biota_id kosong', function () {
    loginSebagaiAdmin();

    // Form multipart mengirim string kosong untuk kolom kosong; Laravel mengubahnya menjadi null.
    $this->post('/api/v1/admin/edukasi', ['judul' => 'Tentang Laut', 'isi' => 'Isi artikel', 'biota_id' => ''], AKSEP_EDUKASI)
        ->assertCreated()
        ->assertJsonPath('data.slug', 'tentang-laut')
        ->assertJsonPath('data.biota', null);
});

test('admin dapat menambah artikel terkait biota dengan gambar', function () {
    Storage::fake('public');
    $biota = Biota::factory()->create(['nama' => 'Dugong', 'slug' => 'dugong']);
    loginSebagaiAdmin();

    $this->post('/api/v1/admin/edukasi', [
        'judul' => 'Padang Lamun',
        'isi' => 'Isi',
        'biota_id' => $biota->id,
        'gambar' => UploadedFile::fake()->image('lamun.jpg'),
    ], AKSEP_EDUKASI)
        ->assertCreated()
        ->assertJsonPath('data.biota.slug', 'dugong');

    Storage::disk('public')->assertExists(KontenEdukasi::first()->gambar);
});

test('biota_id yang tidak ada dan judul kosong ditolak', function () {
    loginSebagaiAdmin();

    $this->postJson('/api/v1/admin/edukasi', ['judul' => '', 'isi' => 'x', 'biota_id' => 99999])
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['judul', 'biota_id']);
});

test('admin dapat mengubah dan menghapus artikel', function () {
    $konten = KontenEdukasi::factory()->create(['judul' => 'Lama', 'slug' => 'lama']);
    loginSebagaiAdmin();

    $this->putJson("/api/v1/admin/edukasi/{$konten->id}", ['judul' => 'Baru', 'isi' => 'Isi baru'])
        ->assertOk()
        ->assertJsonPath('data.judul', 'Baru')
        ->assertJsonPath('data.slug', 'lama');   // slug tidak berubah saat ubah

    $this->deleteJson("/api/v1/admin/edukasi/{$konten->id}")->assertNoContent();
    $this->assertDatabaseMissing('konten_edukasi', ['id' => $konten->id]);
});
