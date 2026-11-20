<?php

namespace App\Services;

use App\Models\Biota;
use App\Models\Kartu;
use App\Models\KontenEdukasi;
use App\Models\SoalKuis;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class GambarService
{
    private function disk(): Filesystem
    {
        return Storage::disk(config('aquaquest.gambar_disk'));
    }

    /** Simpan file ke {disk}/{folder} dan kembalikan path relatif untuk kolom `gambar`. */
    public function simpan(UploadedFile $file, string $folder): string
    {
        return $file->store($folder, config('aquaquest.gambar_disk'));
    }

    /** Hapus file (aman dipanggil dengan null). */
    public function hapus(?string $path): void
    {
        if ($path) {
            $this->disk()->delete($path);
        }
    }

    /**
     * Hapus file HANYA bila tidak dipakai record lain. Seeder kita membuat kartu yang memakai file gambar yang sama dengan
     * biotanya; menghapus kartu tidak boleh menghapus gambar yang masih dipakai biota (dan sebaliknya).
     * $kecuali = record yang sedang diubah atau dihapus (tidak dihitung sebagai "pemakai lain").
     */
    public function hapusBilaTidakDipakai(?string $path, Model $kecuali): void
    {
        if (! $path) {
            return;
        }

        $dipakaiLain = collect([Biota::class, KontenEdukasi::class, Kartu::class, SoalKuis::class])
            ->contains(fn (string $model) => $model::query()
                ->where('gambar', $path)
                ->when($kecuali instanceof $model, fn ($query) => $query->whereKeyNot($kecuali->getKey()))
                ->exists());

        if (! $dipakaiLain) {
            $this->hapus($path);
        }
    }
}
