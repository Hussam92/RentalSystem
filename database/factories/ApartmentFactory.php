<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Apartment>
 */
class ApartmentFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => fake()->numberBetween(1, 9).fake()->regexify('[A-Z]{1}'),
            'street' => fake()->streetName,
            'zip' => fake()->regexify('[0-9]{5}'),
            'bed_count' => fake()->numberBetween(1, 5),
        ];
    }
}
