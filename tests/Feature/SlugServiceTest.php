<?php

use App\Models\Biota;
use App\Services\SlugService;

test('slug dibuat dari teks dan dibuat unik bila bentrok', function () {
    $service = app(SlugService::class);

    expect($service->unik(Biota::class, 'Penyu Hijau'))->toBe('penyu-hijau');

    Biota::factory()->create(['slug' => 'penyu-hijau']);
    Biota::factory()->create(['slug' => 'penyu-hijau-2']);

    expect($service->unik(Biota::class, 'Penyu Hijau'))->toBe('penyu-hijau-3');
});

test('teks tanpa huruf tetap menghasilkan slug', function () {
    expect(app(SlugService::class)->unik(Biota::class, '!!!'))->toBe('item');
});
