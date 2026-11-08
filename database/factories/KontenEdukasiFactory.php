<?php

namespace Database\Factories;

use App\Models\KontenEdukasi;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<KontenEdukasi>
 */
class KontenEdukasiFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $judul = fake()->sentence(4);

        return [
            'biota_id' => null,
            'judul' => $judul,
            'slug' => Str::slug($judul),
            'isi' => fake()->paragraphs(3, true),
            'gambar' => null,
        ];
    }
}
