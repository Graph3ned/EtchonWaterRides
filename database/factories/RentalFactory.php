<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Ride;
use Illuminate\Database\Eloquent\Factories\Factory;

class RentalFactory extends Factory
{
    public function definition(): array
    {
        $startTime = fake()->dateTimeBetween('-1 month', 'now');
        $duration = fake()->numberBetween(30, 180); // 30-180 minutes
        $endTime = (clone $startTime)->modify("+{$duration} minutes");
        $pricePerHour = fake()->randomFloat(2, 200, 500);
        $computedTotal = ($duration / 60) * $pricePerHour;

        return [
            'user_id' => User::factory(),
            'ride_id' => Ride::factory(),
            'status' => fake()->randomElement([0, 1, 2]), // Active, Completed, Cancelled
            'start_at' => $startTime,
            'end_at' => $endTime,
            'duration_minutes' => $duration,
            'life_jacket_quantity' => fake()->numberBetween(0, 5),
            'note' => fake()->optional()->sentence(),
            'user_name_at_time' => fake()->name(),
            'ride_identifier_at_time' => strtoupper(fake()->lexify('???-???')),
            'classification_name_at_time' => fake()->randomElement(['Kayak', 'Jet Ski', 'Boat']),
            'ride_type_name_at_time' => fake()->randomElement(['Water Rides', 'Adventure Rides']),
            'price_per_hour_at_time' => $pricePerHour,
            'computed_total' => round($computedTotal, 2),
        ];
    }
}

