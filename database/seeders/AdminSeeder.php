<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\RideType;
use App\Models\Classification;
use App\Models\Ride;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create admin user
        User::create([
            'name' => 'Admin User',
            'email' => 'edbertetchon@gmail.com',
            'username' => 'admin',
            'userType' => '1', // 1 = Admin
            'password' => 'asdfasdf', // Laravel will hash this automatically
        ]);

        // Create staff users
        User::create([
            'name' => 'Rodrigo Sarang',
            'email' => 'rodrigosarang@gmail.com',
            'username' => 'rodrigo',
            'userType' => '0', // Staff
            'password' => 'asdfasdf',
        ]);

        User::create([
            'name' => 'Jarvie Danao',
            'email' => 'jarviedanao@gmail.com',
            'username' => 'jarvie',
            'userType' => '0', // Staff
            'password' => 'asdfasdf',
        ]);

        User::create([
            'name' => 'Edbert Etchon',
            'email' => 'edbertetchon2@gmail.com',
            'username' => 'edbert',
            'userType' => '0', // Staff
            'password' => 'asdfasdf',
        ]);

        // Create sample ride types
        $paddleBoard = RideType::create([
            'name' => 'Paddle Board',
            'image_path' => 'ride-types/paddle_board_1758257830.jpg'
        ]);
        $boat = RideType::create([
            'name' => 'Boat',
            'image_path' => 'ride-types/boat_1758257801.jpg'
        ]);
        $waterBike = RideType::create([
            'name' => 'Water Bike',
            'image_path' => 'ride-types/water_bike_1758257844.jpg'
        ]);
        $clearKayak = RideType::create([
            'name' => 'Clear Kayak',
            'image_path' => 'ride-types/clear_kayak_1758257818.jpg'
        ]);

        // Create Paddle Board classifications
        $paddleBoardSmall = Classification::create([
            'ride_type_id' => $paddleBoard->id,
            'name' => 'Small',
            'price_per_hour' => 100.00,
        ]);

        $paddleBoardBig = Classification::create([
            'ride_type_id' => $paddleBoard->id,
            'name' => 'Big',
            'price_per_hour' => 200.00,
        ]);

        $paddleBoardRubber = Classification::create([
            'ride_type_id' => $paddleBoard->id,
            'name' => 'Rubber',
            'price_per_hour' => 200.00,
        ]);

        // Create Boat classifications
        $boatBig = Classification::create([
            'ride_type_id' => $boat->id,
            'name' => 'Big',
            'price_per_hour' => 300.00,
        ]);

        $boatSmall = Classification::create([
            'ride_type_id' => $boat->id,
            'name' => 'Small',
            'price_per_hour' => 200.00,
        ]);

        // Create Water Bike classifications
        $waterBikeWithPropeller = Classification::create([
            'ride_type_id' => $waterBike->id,
            'name' => 'With Propeller',
            'price_per_hour' => 200.00,
        ]);

        $waterBikeWithoutPropeller = Classification::create([
            'ride_type_id' => $waterBike->id,
            'name' => 'Without Propeller',
            'price_per_hour' => 200.00,
        ]);

        // Create Clear Kayak classifications
        $clearKayakDouble = Classification::create([
            'ride_type_id' => $clearKayak->id,
            'name' => 'Double',
            'price_per_hour' => 300.00,
        ]);

        // Create Paddle Board rides
        // Small: yellow, blue, gray
        Ride::create([
            'classification_id' => $paddleBoardSmall->id, 
            'identifier' => 'Yellow',
            'image_path' => 'ride-images/yellow_1758256871.jpg',
            'is_active' => 1
        ]);
        Ride::create([
            'classification_id' => $paddleBoardSmall->id, 
            'identifier' => 'Blue',
            'image_path' => 'ride-images/blue_1758256831.webp',
            'is_active' => 1
        ]);
        Ride::create([
            'classification_id' => $paddleBoardSmall->id, 
            'identifier' => 'Gray',
            'image_path' => 'ride-images/gray_1758256863.jpg',
            'is_active' => 1
        ]);

        // Big: Yellow
        Ride::create([
            'classification_id' => $paddleBoardBig->id, 
            'identifier' => 'Yellow',
            'image_path' => 'ride-images/yellow_1758256899.jpg',
            'is_active' => 1
        ]);

        // Rubber: Blue
        Ride::create([
            'classification_id' => $paddleBoardRubber->id, 
            'identifier' => 'Blue',
            'image_path' => 'ride-images/blue_1758256934.jpg',
            'is_active' => 1
        ]);

        // Create Boat rides
        // Big: Blue
        Ride::create([
            'classification_id' => $boatBig->id, 
            'identifier' => 'Blue',
            'image_path' => 'ride-images/blue_1758256615.jpg',
            'is_active' => 1
        ]);

        // Small: Pink
        Ride::create([
            'classification_id' => $boatSmall->id, 
            'identifier' => 'Pink',
            'image_path' => 'ride-images/pink_1758256649.jpg',
            'is_active' => 1
        ]);

        // Create Water Bike rides
        // With Propeller: yellow, blue, red
        Ride::create([
            'classification_id' => $waterBikeWithPropeller->id, 
            'identifier' => 'Yellow',
            'image_path' => 'ride-images/yellow_1758257010.jpg',
            'is_active' => 1
        ]);
        Ride::create([
            'classification_id' => $waterBikeWithPropeller->id, 
            'identifier' => 'Blue',
            'image_path' => 'ride-images/blue_1758257072.jpg',
            'is_active' => 1
        ]);
        Ride::create([
            'classification_id' => $waterBikeWithPropeller->id, 
            'identifier' => 'Red',
            'image_path' => 'ride-images/red_1758257046.jpg',
            'is_active' => 1
        ]);

        // Without Propeller: orange, green
        Ride::create([
            'classification_id' => $waterBikeWithoutPropeller->id, 
            'identifier' => 'Orange',
            'image_path' => 'ride-images/orange_1758257177.jpg',
            'is_active' => 1
        ]);
        Ride::create([
            'classification_id' => $waterBikeWithoutPropeller->id, 
            'identifier' => 'Green',
            'image_path' => 'ride-images/green_1758257260.jpg',
            'is_active' => 1
        ]);

        // Create Clear Kayak rides
        // Double: Orange Paddle, Black Paddle
        Ride::create([
            'classification_id' => $clearKayakDouble->id, 
            'identifier' => 'Orange Paddle',
            'image_path' => 'ride-images/orange_paddle_1758256718.jpg',
            'is_active' => 1
        ]);
        Ride::create([
            'classification_id' => $clearKayakDouble->id, 
            'identifier' => 'Black Paddle',
            'image_path' => 'ride-images/black_paddle_1758256708.webp',
            'is_active' => 1
        ]);
    }
} 