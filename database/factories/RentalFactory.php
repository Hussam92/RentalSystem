<?php

namespace Database\Factories;

use App\Models\Apartment;
use App\Models\Rental;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Rental>
 */
class RentalFactory extends Factory
{
    public function definition(): array
    {
        return [
            'begins_at' => $begin = now()->subDays(rand(-180, 180)),
            'ends_at' => $begin->clone()->addDays(rand(1, 30)),
            'price_per_day' => fake()->randomElement([50, 65, 95, 120, 160]),
            'apartment_id' => Apartment::all()->random()->id ?? Apartment::factory()->create()->id,
        ];
    }

    public function configure(): RentalFactory
    {
        return $this->afterMaking(function (Rental $rental) {
            if (! $rental->apartment_id) {
                $apartments = Apartment::all();
                $rental->apartment_id = $apartments->count() ? $apartments->random() : Apartment::factory()->create()->id;
            }
        })->afterCreating(function (Rental $rental) {
            //
        });
    }
}
