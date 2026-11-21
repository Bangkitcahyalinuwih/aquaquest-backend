<?php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AdminSoalResource extends JsonResource
{
    /** Resource ADMIN: memuat jawaban_benar (admin yang membuat dan mengubahnya). Resource untuk pemain (SoalKuisSesiResource) tidak pernah memuatnya. */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'biota_id' => $this->biota_id,
            'biota' => $this->whenLoaded('biota', fn () => $this->biota
                ? ['id' => $this->biota->id, 'slug' => $this->biota->slug, 'nama' => $this->biota->nama]
                : null),
            'tipe' => $this->tipe,
            'pertanyaan' => $this->pertanyaan,
            'gambar_url' => $this->gambar_url,
            'opsi_a' => $this->opsi_a,
            'opsi_b' => $this->opsi_b,
            'opsi_c' => $this->opsi_c,
            'opsi_d' => $this->opsi_d,
            'jawaban_benar' => $this->jawaban_benar,
            'poin' => $this->poin,
        ];
    }
}
