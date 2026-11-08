<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EdukasiResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'slug' => $this->slug,
            'judul' => $this->judul,
            'isi' => $this->isi,
            'gambar_url' => $this->gambar_url,
            'created_at' => $this->created_at?->toIso8601String(),
            // whenLoaded: hanya ikut jika relasi sudah dimuat (controller memanggil ->load('biota')). Null jika artikel umum.
            'biota' => $this->whenLoaded('biota', fn () => $this->biota
                ? ['slug' => $this->biota->slug, 'nama' => $this->biota->nama]
                : null),
        ];
    }
}
