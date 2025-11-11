<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class RideTypeFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => fake()->randomElement(['Water Rides', 'Adventure Rides', 'Family Rides']),
            'image_path' => 'test-images/' . fake()->word() . '.jpg',
        ];
    }
}

