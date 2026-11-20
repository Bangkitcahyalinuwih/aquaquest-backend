<?php

namespace App\Models\Concerns;

use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Support\Facades\Storage;

/**
 * @property string|null $gambar
 */
trait HasGambarUrl
{
    /** Pemakaian: $model->gambar_url (null bila belum ada gambar). */
    public function getGambarUrlAttribute(): ?string
    {
        if (! $this->gambar) {
            return null;
        }

        $disk = config('aquaquest.gambar_disk', 'public');

        if ($disk === 'public') {
            return asset('storage/'.$this->gambar);
        }

        /** @var FilesystemAdapter $storage */
        $storage = Storage::disk($disk);

        return $storage->url($this->gambar);
    }
}
