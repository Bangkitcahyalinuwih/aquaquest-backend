<?php

namespace App\Models;

use App\Models\Concerns\HasGambarUrl;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Biota extends Model
{
    use HasFactory, HasGambarUrl;   // supaya bisa pakai Biota::factory() untuk data contoh

    // Kolom yang boleh diisi lewat Biota::create([...]). Ini pengaman: kolom di luar
    // daftar ini akan DITOLAK walau ada di request, mencegah orang iseng mengisi kolom lain.
    protected $table = 'biota';

    protected $fillable = [
        'nama',
        'nama_latin',
        'slug',
        'kategori',
        'deskripsi',
        'habitat',
        'status_konservasi',
        'gambar',
        'latitude',
        'longitude',
    ];

    // Laravel biasanya cari data lewat kolom "id" di URL (/biota/5). Baris ini mengubahnya
    // jadi cari lewat kolom "slug" (/biota/penyu-hijau) — URL jadi lebih enak dibaca dan SEO-friendly.
    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    // Relasi: satu biota bisa punya BANYAK artikel edukasi.
    // Setelah ini, kamu bisa tulis $biota->kontenEdukasi untuk ambil semua artikel terkait biota itu.
    public function kontenEdukasi(): HasMany
    {
        return $this->hasMany(KontenEdukasi::class, 'biota_id');  // foreign key di tabel konten_edukasi
    }
}
