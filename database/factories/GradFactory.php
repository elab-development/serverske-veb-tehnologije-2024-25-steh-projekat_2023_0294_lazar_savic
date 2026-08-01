<?php

namespace Database\Factories;

use App\Models\Grad;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Grad>
 */
class GradFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'naziv' => $this->faker->city(),
            'postanski_broj' => $this->faker->postcode()
        ];
    }
}
