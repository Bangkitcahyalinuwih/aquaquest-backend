<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SoalKuisSesiResource extends JsonResource
{
    /**
     * Daftar putih (whitelist) field yang boleh sampai ke pemain.
     * jawaban_benar TIDAK ada di sini: lapis pertahanan kedua setelah select kolom di KuisService.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'tipe' => $this->tipe,
            'pertanyaan' => $this->pertanyaan,
            'gambar_url' => $this->gambar_url,
            'opsi_a' => $this->opsi_a,
            'opsi_b' => $this->opsi_b,
            'opsi_c' => $this->opsi_c,
            'opsi_d' => $this->opsi_d,
        ];
    }
}
