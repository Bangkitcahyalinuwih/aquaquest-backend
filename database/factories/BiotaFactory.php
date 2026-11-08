<?php

namespace Database\Factories;

use App\Models\Biota;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Biota>
 */
class BiotaFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $nama = fake()->unique()->words(2, true);   // 2 kata acak, misal "terumbu biru"

        return [
            'nama' => ucwords($nama),
            'nama_latin' => fake()->words(2, true),
            'slug' => Str::slug($nama),   // "terumbu biru" jadi "terumbu-biru"
            'kategori' => fake()->randomElement(['Ikan', 'Mamalia Laut', 'Terumbu Karang', 'Moluska']),
            'deskripsi' => fake()->paragraph(),
            'habitat' => fake()->randomElement(['Laut dangkal', 'Laut dalam', 'Terumbu karang', 'Pesisir']),
            'status_konservasi' => fake()->randomElement(['LC', 'VU', 'EN', 'CR']),
            'gambar' => null,
            'latitude' => fake()->latitude(-8, -5),     // sekitar perairan Indonesia
            'longitude' => fake()->longitude(110, 115),
        ];
    }
}
