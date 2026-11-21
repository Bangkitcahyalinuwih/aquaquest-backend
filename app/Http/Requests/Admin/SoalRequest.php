<?php

namespace App\Http\Requests\Admin;

use App\Models\SoalKuis;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SoalRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;   // akses dijaga middleware auth:sanctum dan role:admin
    }

    public function rules(): array
    {
        /** @var SoalKuis|null $soal null saat tambah; model yang sedang diubah saat ubah (hasil route model binding) */
        $soal = $this->route('soal');

        // Soal bertipe "gambar" wajib punya gambar: saat tambah, atau saat ubah bila belum punya gambar.
        $wajibGambar = $this->input('tipe') === 'gambar' && (! $soal || ! $soal->gambar);

        return [
            'biota_id' => ['nullable', 'exists:biota,id'],
            'tipe' => ['required', Rule::in(['teks', 'gambar'])],
            'pertanyaan' => ['required', 'string'],
            'gambar' => [Rule::requiredIf($wajibGambar), 'nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'opsi_a' => ['required', 'string', 'max:255'],
            'opsi_b' => ['required', 'string', 'max:255'],
            'opsi_c' => ['required', 'string', 'max:255'],
            'opsi_d' => ['required', 'string', 'max:255'],
            'jawaban_benar' => ['required', Rule::in(['a', 'b', 'c', 'd'])],
            'poin' => ['required', 'integer', 'min:1', 'max:100'],
        ];
    }

    public function attributes(): array
    {
        return [
            'biota_id' => 'biota',
            'jawaban_benar' => 'jawaban benar',
            'opsi_a' => 'opsi A',
            'opsi_b' => 'opsi B',
            'opsi_c' => 'opsi C',
            'opsi_d' => 'opsi D',
        ];
    }
}
