<?php

namespace App\Models\Concerns;

/**
 * @property string|null $gambar
 */
trait HasGambarUrl
{
    /** Pemakaian di Blade: $biota->gambar_url (null jika belum ada gambar). */
    public function getGambarUrlAttribute(): ?string
    {
        return $this->gambar ? asset('storage/'.$this->gambar) : null;
    }
}
