<?php

use App\Models\Biota;
use App\Models\Kartu;
use App\Models\KoleksiKartu;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

const AKSEP = ['Accept' => 'application/json'];

test('tamu 401 dan pengguna biasa 403', function () {
    $this->getJson('/api/v1/admin/kartu')->assertUnauthorized();

    loginSebagaiUser();
    $this->getJson('/api/v1/admin/kartu')->assertForbidden();
});

test('daftar kartu terurut menurut syarat xp dan memuat biota', function () {
    Kartu::factory()->create(['xp_syarat' => 300]);
    Kartu::factory()->create(['xp_syarat' => 50]);
    loginSebagaiAdmin();

    $this->getJson('/api/v1/admin/kartu')
        ->assertOk()
        ->assertJsonPath('data.0.xp_syarat', 50)
        ->assertJsonStructure(['data' => ['*' => ['id', 'biota_id', 'biota' => ['id', 'slug', 'nama'], 'nama', 'xp_syarat', 'gambar_url']]]);
});

test('admin dapat menambah kartu dengan gambar', function () {
    Storage::fake('public');
    $biota = Biota::factory()->create();
    loginSebagaiAdmin();

    $this->post('/api/v1/admin/kartu', ['biota_id' => $biota->id, 'nama' => 'Kartu Uji', 'xp_syarat' => 100, 'gambar' => UploadedFile::fake()->image('k.png')], AKSEP)
        ->assertCreated()
        ->assertJsonPath('data.nama', 'Kartu Uji');

    Storage::disk('public')->assertExists(Kartu::first()->gambar);
});

test('gambar wajib saat menambah kartu', function () {
    $biota = Biota::factory()->create();
    loginSebagaiAdmin();

    $this->postJson('/api/v1/admin/kartu', ['biota_id' => $biota->id, 'nama' => 'X', 'xp_syarat' => 100])
        ->assertUnprocessable()
        ->assertJsonValidationErrors('gambar');
});

test('satu biota hanya boleh memiliki satu kartu', function () {
    Storage::fake('public');
    $kartu = Kartu::factory()->create();
    loginSebagaiAdmin();

    $this->post('/api/v1/admin/kartu', ['biota_id' => $kartu->biota_id, 'nama' => 'Kedua', 'xp_syarat' => 10, 'gambar' => UploadedFile::fake()->image('k.png')], AKSEP)
        ->assertUnprocessable()
        ->assertJsonValidationErrors('biota_id');

    expect(Kartu::count())->toBe(1);
});

test('ubah kartu tanpa mengganti biota atau gambar berhasil', function () {
    $kartu = Kartu::factory()->create(['nama' => 'Lama', 'gambar' => 'kartu/lama.png']);
    loginSebagaiAdmin();

    $this->putJson("/api/v1/admin/kartu/{$kartu->id}", ['biota_id' => $kartu->biota_id, 'nama' => 'Baru', 'xp_syarat' => 250])
        ->assertOk()
        ->assertJsonPath('data.nama', 'Baru');

    expect($kartu->fresh()->xp_syarat)->toBe(250)->and($kartu->fresh()->gambar)->toBe('kartu/lama.png');
});

test('hapus kartu menghapus koleksi pemain tetapi tidak menghapus gambar yang dipakai biota', function () {
    Storage::fake('public');
    Storage::disk('public')->put('biota/a.jpg', 'x');
    $biota = Biota::factory()->create(['gambar' => 'biota/a.jpg']);
    $kartu = Kartu::factory()->create(['biota_id' => $biota->id, 'gambar' => 'biota/a.jpg']);
    $pemain = loginSebagaiAdmin();
    KoleksiKartu::create(['user_id' => $pemain->id, 'kartu_id' => $kartu->id, 'didapat_at' => now()]);

    $this->deleteJson("/api/v1/admin/kartu/{$kartu->id}")->assertNoContent();

    $this->assertDatabaseMissing('kartu', ['id' => $kartu->id]);
    $this->assertDatabaseMissing('koleksi_kartu', ['kartu_id' => $kartu->id]);
    Storage::disk('public')->assertExists('biota/a.jpg');
});
