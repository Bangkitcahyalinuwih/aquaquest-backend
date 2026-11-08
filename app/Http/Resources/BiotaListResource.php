<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Str;

class BiotaListResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'slug' => $this->slug,
            'nama' => $this->nama,
            'nama_latin' => $this->nama_latin,
            'kategori' => $this->kategori,
            'status_konservasi' => $this->status_konservasi,
            'gambar_url' => $this->gambar_url,   // accessor dari trait HasGambarUrl
            'deskripsi_singkat' => Str::limit($this->deskripsi, 90),
        ];
    }
}
