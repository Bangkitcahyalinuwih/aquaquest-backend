<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class KartuKoleksiResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'nama' => $this->nama,
            'xp_syarat' => $this->xp_syarat,
            'gambar_url' => $this->gambar_url,
            'biota' => $this->biota ? ['slug' => $this->biota->slug, 'nama' => $this->biota->nama] : null,
            'terbuka' => (bool) $this->terbuka,   // atribut tambahan yang diisi controller
        ];
    }
}
