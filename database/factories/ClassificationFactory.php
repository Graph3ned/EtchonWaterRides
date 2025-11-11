<?php

namespace Database\Factories;

use App\Models\RideType;
use Illuminate\Database\Eloquent\Factories\Factory;

class ClassificationFactory extends Factory
{
    public function definition(): array
    {
        return [
            'ride_type_id' => RideType::factory(),
            'name' => fake()->randomElement(['Kayak', 'Jet Ski', 'Boat', 'Canoe']),
            'price_per_hour' => fake()->randomFloat(2, 100, 500),
            'image_path' => 'test-images/' . fake()->word() . '.jpg',
        ];
    }
}

