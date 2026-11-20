<?php

namespace App\Services;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class SlugService
{
    /**
     * Buat slug unik. Bila "penyu-hijau" sudah ada, hasilnya "penyu-hijau-2", dst.
     * Tanpa ini, dua biota bernama sama membuat database error (kolom slug unik).
     *
     * @param  class-string<Model>  $model
     */
    public function unik(string $model, string $teks): string
    {
        $dasar = Str::slug($teks) ?: 'item';
        $slug = $dasar;
        $urut = 2;

        while ($model::query()->where('slug', $slug)->exists()) {
            $slug = $dasar.'-'.$urut++;
        }

        return $slug;
    }
}
