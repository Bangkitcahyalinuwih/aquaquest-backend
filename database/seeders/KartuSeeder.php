<?php

namespace Database\Seeders;

use App\Models\Biota;
use App\Models\Kartu;
use Illuminate\Database\Seeder;

class KartuSeeder extends Seeder
{
    public function run(): void
    {

        $ambang = [50, 100, 150, 200, 300, 400, 500, 650, 800, 1000];

        foreach (Biota::orderBy('id')->get() as $i => $biota) {
            Kartu::updateOrCreate(
                ['biota_id' => $biota->id],
                [
                    'nama' => 'Kartu ' . $biota->nama,
                    'gambar' => $biota->gambar ?? '',
                    'xp_syarat' => $ambang[$i] ?? 1000,
                ]
            );
        }
    }
}
