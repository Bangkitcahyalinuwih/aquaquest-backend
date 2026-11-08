<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BiotaResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'slug' => $this->slug,
            'nama' => $this->nama,
            'nama_latin' => $this->nama_latin,
            'kategori' => $this->kategori,
            'status_konservasi' => $this->status_konservasi,
            'habitat' => $this->habitat,
            'deskripsi' => $this->deskripsi,
            // Kolom decimal dibaca sebagai string oleh database; kontrak menjanjikan angka.
            'latitude' => $this->latitude !== null ? (float) $this->latitude : null,
            'longitude' => $this->longitude !== null ? (float) $this->longitude : null,
            'gambar_url' => $this->gambar_url,
        ];
    }
}
