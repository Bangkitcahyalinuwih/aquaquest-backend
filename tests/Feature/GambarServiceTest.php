<?php

use App\Models\Biota;
use App\Models\Kartu;
use App\Services\GambarService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

test('simpan menyimpan file di folder yang diminta', function () {
    Storage::fake('public');

    $path = app(GambarService::class)->simpan(UploadedFile::fake()->image('a.jpg'), 'biota');

    expect($path)->toStartWith('biota/');
    Storage::disk('public')->assertExists($path);
});

test('hapus tidak error untuk null dan menghapus file yang ada', function () {
    Storage::fake('public');
    Storage::disk('public')->put('biota/a.jpg', 'x');

    app(GambarService::class)->hapus(null);
    app(GambarService::class)->hapus('biota/a.jpg');

    Storage::disk('public')->assertMissing('biota/a.jpg');
});

test('file yang masih dipakai record lain tidak dihapus', function () {
    Storage::fake('public');
    Storage::disk('public')->put('biota/a.jpg', 'x');
    $biota = Biota::factory()->create(['gambar' => 'biota/a.jpg']);
    $kartu = Kartu::factory()->create(['biota_id' => $biota->id, 'gambar' => 'biota/a.jpg']);

    // Biota masih memakai file itu, jadi menghapus "milik kartu" tidak boleh menghapus file.
    app(GambarService::class)->hapusBilaTidakDipakai($kartu->gambar, $kartu);
    Storage::disk('public')->assertExists('biota/a.jpg');

    // Setelah biota tidak lagi memakainya, file boleh dihapus.
    $biota->update(['gambar' => null]);
    app(GambarService::class)->hapusBilaTidakDipakai($kartu->gambar, $kartu);
    Storage::disk('public')->assertMissing('biota/a.jpg');
});
