<?php

namespace Database\Factories;

use App\Models\Location;
use Illuminate\Database\Eloquent\Factories\Factory;

class CarFactory extends Factory
{
    public function definition(): array
    {
        return [
            'brand' => $this->faker->randomElement(['BMW', 'Audi', 'Mercedes', 'Volkswagen', 'Toyota']),
            'model' => $this->faker->word(),
            'year' => $this->faker->numberBetween(2018, 2024),
            'price_per_day' => $this->faker->randomFloat(2, 30, 150),
            'is_available' => $this->faker->boolean(80),
            'license_plate' => strtoupper($this->faker->bothify('BG-###-??')),
            'location_id' => Location::factory(),
        ];
    }
}
