<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class BiotaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;   // akses dijaga middleware auth:sanctum dan role:admin
    }

    public function rules(): array
    {
        return [
            'nama' => ['required', 'string', 'max:255'],
            'nama_latin' => ['nullable', 'string', 'max:255'],
            'kategori' => ['required', 'string', 'max:100'],
            'deskripsi' => ['required', 'string'],
            'habitat' => ['nullable', 'string', 'max:255'],
            'status_konservasi' => ['nullable', 'string', 'max:50'],
            'latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],
            // image + mimes memeriksa ISI file; skrip PHP yang diberi nama .jpg ditolak. Maksimal 2 MB (2048 KB).
            'gambar' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ];
    }

    public function attributes(): array
    {
        return ['nama_latin' => 'nama latin', 'status_konservasi' => 'status konservasi'];
    }
}
