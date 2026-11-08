<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Str;

class EdukasiListResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'slug' => $this->slug,
            'judul' => $this->judul,
            'ringkasan' => Str::limit($this->isi, 130),
            'gambar_url' => $this->gambar_url,
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}
