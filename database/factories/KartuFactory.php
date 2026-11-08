<?php

namespace Database\Factories;

use App\Models\Biota;
use App\Models\Kartu;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Kartu>
 */
class KartuFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'biota_id' => Biota::factory(),
            'nama' => 'Kartu '.fake()->words(2, true),
            'gambar' => $this->faker->imageUrl(640, 480, 'animals', true),
            'xp_syarat' => fake()->randomElement([50, 100, 150, 200, 250]),
        ];
    }
}
