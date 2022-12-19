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
        $days = rand(1, 30);

        return [
            'begins_at' => $begin = now()->startOfDay()->addDays(rand(0, 30)),
            'ends_at' => $begin->clone()->addDays($days),
            'price_per_day' => fake()->randomElement([50, 65, 95, 120, 160]),
        ];
    }

    public function configure(): RentalFactory
    {
        return $this->afterMaking(function (Rental $rental) {
            if (! $rental->apartment) {
                $apartments = Apartment::all();
                $rental->apartment_id = $apartments->count() ? $apartments->random() : Apartment::factory()->create()->id;
            }
        })->afterCreating(function (Rental $rental) {
            //
        });
    }
}
