<?php

namespace Database\Factories;

use App\Models\Biota;
use App\Models\SoalKuis;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<SoalKuis>
 */
class SoalKuisFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'biota_id' => Biota::inRandomOrder()->first()?->id,
            'tipe' => 'teks',
            'pertanyaan' => fake()->sentence().'?',
            'gambar' => null,
            'opsi_a' => fake()->word(),
            'opsi_b' => fake()->word(),
            'opsi_c' => fake()->word(),
            'opsi_d' => fake()->word(),
            'jawaban_benar' => 'a',
            'poin' => 10,
        ];
    }
}
