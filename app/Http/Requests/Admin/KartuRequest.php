<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class KartuRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $kartu = $this->route('kartu');   // null saat tambah

        return [
            // Satu biota hanya boleh punya satu kartu. Saat ubah, kartu itu sendiri dikecualikan dari pemeriksaan.
            'biota_id' => ['required', 'exists:biota,id', Rule::unique('kartu', 'biota_id')->ignore($kartu?->id)],
            'nama' => ['required', 'string', 'max:255'],
            'xp_syarat' => ['required', 'integer', 'min:0'],
            // Kolom gambar pada tabel kartu tidak boleh kosong: wajib saat tambah, opsional saat ubah.
            'gambar' => [$kartu ? 'nullable' : 'required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ];
    }

    public function attributes(): array
    {
        return ['biota_id' => 'biota', 'xp_syarat' => 'syarat XP'];
    }
}
