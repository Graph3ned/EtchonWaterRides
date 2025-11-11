<?php

namespace Database\Factories;

use App\Models\Classification;
use Illuminate\Database\Eloquent\Factories\Factory;

class RideFactory extends Factory
{
    public function definition(): array
    {
        return [
            'classification_id' => Classification::factory(),
            'identifier' => strtoupper(fake()->lexify('???-???')),
            'is_active' => 1,
            'image_path' => 'test-images/' . fake()->word() . '.jpg',
        ];
    }
}

