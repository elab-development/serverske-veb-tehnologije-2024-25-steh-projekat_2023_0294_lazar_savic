<?php

namespace Database\Factories;

use App\Models\Model;
use Illuminate\Database\Eloquent\Factories\Factory;
/**
 * @extends Factory<\Illuminate\Database\Eloquent\Model>
 */
class NekretninaFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'naslov' => $this->faker->sentence(4),
            'opis' => $this->faker->paragraph(3),
            'cena' => $this->faker->numberBetween('30000', '300000'),
            'kvadratura' => $this->faker->numberBetween(30, 200),
            'adresa' => $this->faker->streetAddress(),
            'tip' => $this->faker->randomElement(['stan', 'kuca', 'poslovni_prostor', 'zemljiste']),
            'status' => $this->faker->randomElement(['prodaja', 'izdavanje']),
            'is_istaknuto' => $this->faker->boolean(20),
            'slika_putanja' => null,
            'grad_id' => \App\Models\Grad::factory(),
            'korisnik_id' => \App\Models\User::factory(),
        ];
    }
}
