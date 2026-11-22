<?php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AdminKartuResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'biota_id' => $this->biota_id,
            'biota' => $this->whenLoaded('biota', fn () => $this->biota
                ? ['id' => $this->biota->id, 'slug' => $this->biota->slug, 'nama' => $this->biota->nama]
                : null),
            'nama' => $this->nama,
            'xp_syarat' => $this->xp_syarat,
            'gambar_url' => $this->gambar_url,
        ];
    }
}
