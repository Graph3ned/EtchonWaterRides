<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Rental;
use App\Models\Ride;
use App\Models\User;
use Illuminate\Support\Carbon;

class RentalsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Fetch the two staff users (userType = '0') added previously
        $staffUsers = User::whereIn('email', [
            'rodrigosarang@gmail.com',
            'jarviedanao@gmail.com',
            'edbertetchon2@gmail.com',
        ])->get();

        if ($staffUsers->count() === 0) {
            return; // No staff users to assign rentals
        }

        $rides = Ride::with(['classification.rideType'])->get();
        if ($rides->count() === 0) {
            return; // No rides to create rentals for
        }

        $faker = \Faker\Factory::create();
        $now = Carbon::now();
        $startWindow = $now->copy()->subYears(3);

        $batchSize = 500;
        $target = 2000;
        $created = 0;

        while ($created < $target) {
            $chunk = [];
            $limit = min($batchSize, $target - $created);

            for ($i = 0; $i < $limit; $i++) {
                $ride = $rides->random();
                $classification = $ride->classification;
                $rideType = $classification ? $classification->rideType : null;
                $staff = $staffUsers->random();

                // Random start time within 3-year window
                $startAt = Carbon::createFromTimestamp(
                    $faker->numberBetween($startWindow->timestamp, $now->timestamp)
                );

                // Duration between 30 minutes to 4 hours (in 15-min steps)
                $durationMinutes = $faker->randomElement([30, 45, 60, 75, 90, 105, 120, 135, 150, 165, 180, 195, 210, 225, 240]);
                $endAt = (clone $startAt)->addMinutes($durationMinutes);

                // All seeded rentals should be completed (no active)
                $status = Rental::STATUS_COMPLETED;

                // Ensure endAt/duration are always set for completed/cancelled
                // (Already computed above)

                $pricePerHour = $classification ? (float) $classification->price_per_hour : 0.0;
                $pricePerMinute = $pricePerHour / 60.0;
                $computedTotal = round($pricePerMinute * $durationMinutes, 2);

                $chunk[] = [
                    'user_id' => $staff->id,
                    'ride_id' => $ride->id,
                    'status' => $status,
                    'start_at' => $startAt,
                    'end_at' => $endAt,
                    'duration_minutes' => $durationMinutes,
                    'life_jacket_quantity' => $faker->numberBetween(0, 4),
                    'note' => $faker->optional(0.2)->sentence(),
                    'user_name_at_time' => $staff->name,
                    'ride_identifier_at_time' => $ride->identifier,
                    'classification_name_at_time' => $classification ? $classification->name : null,
                    'ride_type_name_at_time' => $rideType ? $rideType->name : null,
                    'price_per_hour_at_time' => $pricePerHour,
                    'computed_total' => $computedTotal,
                    'created_at' => $startAt,
                    'updated_at' => $endAt ?? $startAt,
                ];
            }

            // Insert in bulk for performance
            Rental::insert($chunk);
            $created += $limit;
        }
    }
}


