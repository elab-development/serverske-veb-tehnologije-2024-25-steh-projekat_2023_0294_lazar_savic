<?php

namespace Database\Factories;

use App\Models\Upit;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Upit>
 */
class UpitFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'poruka' => $this->faker->text(150),
            'kontakt_telefon' => $this->faker->phoneNumber(),
            'status_upita' => 'neobradjeno',
            'nekretnina_id' => \App\Models\Nekretnina::factory(),
            'korisnik_id' => \App\Models\User::factory(),
        ];
    }
}
