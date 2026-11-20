<?php

namespace Database\Seeders\Concerns;

use Illuminate\Support\Facades\Storage;

trait SalinGambar
{
    /** Salin database/seeders/images/{folder}/{slug}.(jpg|jpeg|png|webp) ke disk gambar. Null bila berkasnya belum ada. */
    protected function salinGambar(string $folder, string $slug): ?string
    {
        foreach (['jpg', 'jpeg', 'png', 'webp'] as $ext) {
            $sumber = database_path("seeders/images/{$folder}/{$slug}.{$ext}");

            if (is_file($sumber)) {
                $tujuan = "{$folder}/{$slug}.{$ext}";
                Storage::disk(config('aquaquest.gambar_disk'))->put($tujuan, file_get_contents($sumber));

                return $tujuan;
            }
        }

        return null;
    }
}
