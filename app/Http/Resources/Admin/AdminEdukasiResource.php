<?php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AdminEdukasiResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'slug' => $this->slug,
            'judul' => $this->judul,
            'isi' => $this->isi,
            'biota_id' => $this->biota_id,
            'biota' => $this->whenLoaded('biota', fn () => $this->biota
                ? ['id' => $this->biota->id, 'slug' => $this->biota->slug, 'nama' => $this->biota->nama]
                : null),
            'gambar_url' => $this->gambar_url,
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}
