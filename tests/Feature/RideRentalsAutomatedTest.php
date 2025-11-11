<?php

use App\Models\User;
use App\Models\Ride;
use App\Models\Rental;
use App\Models\RideType;
use App\Models\Classification;
use App\Livewire\AddRide;
use App\Livewire\EditRide;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->staff = User::factory()->create(['userType' => 0]);
    
    // Create test data
    $this->rideType = RideType::create([
        'name' => 'Water Rides',
        'image_path' => 'test.jpg'
    ]);
    
    $this->classification = Classification::create([
        'ride_type_id' => $this->rideType->id,
        'name' => 'Kayak',
        'price_per_hour' => 300.00,
        'image_path' => 'kayak.jpg'
    ]);
    
    $this->ride = Ride::create([
        'classification_id' => $this->classification->id,
        'identifier' => 'KAY-001',
        'is_active' => 1
    ]);
});

// Test Case 1: Create New Rental Transaction
it('creates new rental transaction automatically', function () {
    $this->actingAs($this->staff);
    
    Livewire::test(AddRide::class)
        ->set('rideTypeId', $this->rideType->id)
        ->set('classificationId', $this->classification->id)
        ->set('rideId', $this->ride->id)
        ->set('duration', 60)
        ->set('life_jacket_quantity', 2)
        ->set('startAt', '09:00:00')
        ->set('endAt', '10:00:00')
        ->set('pricePerHour', 300.00)
        ->set('computedTotal', 300.00)
        ->call('startRental')
        ->assertHasNoErrors();
    
    // Verify rental was created
    expect(Rental::count())->toBe(1);
    
    $rental = Rental::first();
    expect($rental->user_id)->toBe($this->staff->id);
    expect($rental->ride_id)->toBe($this->ride->id);
    expect($rental->duration_minutes)->toBe(60);
    expect($rental->life_jacket_quantity)->toBe(2);
    expect($rental->status)->toBe(0); // Active
});

// Test Case 2: Edit Existing Rental
it('edits existing rental and records changes', function () {
    $this->actingAs($this->staff);
    
    // Create a rental first
    $rental = Rental::create([
        'user_id' => $this->staff->id,
        'ride_id' => $this->ride->id,
        'status' => 0, // Active
        'duration_minutes' => 60,
        'life_jacket_quantity' => 2,
        'start_at' => now(),
        'end_at' => now()->addMinutes(60),
        'price_per_hour_at_time' => 300.00,
        'computed_total' => 300.00
    ]);
    
    Livewire::test(EditRide::class, ['rentalId' => $rental->id])
        ->set('duration', 90)
        ->set('life_jacket_quantity', 3)
        ->set('computedTotal', 450.00)
        ->call('updateRental')
        ->assertHasNoErrors();
    
    $rental->refresh();
    expect($rental->duration_minutes)->toBe(90);
    expect($rental->life_jacket_quantity)->toBe(3);
    expect($rental->computed_total)->toBe(450.00);
});

// Test Case 3: Complete Rental Transaction
it('completes rental transaction with final calculations', function () {
    $this->actingAs($this->staff);
    
    $rental = Rental::create([
        'user_id' => $this->staff->id,
        'ride_id' => $this->ride->id,
        'status' => 0, // Active
        'duration_minutes' => 90,
        'life_jacket_quantity' => 2,
        'start_at' => now()->subMinutes(90),
        'end_at' => now(),
        'price_per_hour_at_time' => 300.00,
        'computed_total' => 450.00
    ]);
    
    Livewire::test(EditRide::class, ['rentalId' => $rental->id])
        ->call('endRental')
        ->assertHasNoErrors();
    
    $rental->refresh();
    expect($rental->status)->toBe(1); // Completed
    expect($rental->end_at)->not->toBeNull();
});

// Test Case 15: Required Custom Duration When Enabled
it('validates custom duration when enabled', function () {
    $this->actingAs($this->staff);
    
    Livewire::test(AddRide::class)
        ->set('rideTypeId', $this->rideType->id)
        ->set('classificationId', $this->classification->id)
        ->set('rideId', $this->ride->id)
        ->set('showCustomDuration', true)
        ->set('customDuration', '') // Empty custom duration
        ->call('startRental')
        ->assertHasErrors(['customDuration']);
});

// Test Case 16: Data Type Validation
it('validates data types for rental inputs', function () {
    $this->actingAs($this->staff);
    
    Livewire::test(AddRide::class)
        ->set('rideTypeId', $this->rideType->id)
        ->set('classificationId', $this->classification->id)
        ->set('rideId', $this->ride->id)
        ->set('showCustomDuration', true)
        ->set('customDuration', 'abc') // Invalid data type
        ->call('startRental')
        ->assertHasErrors(['customDuration']);
});

// Test Case 18: Data Validation - Invalid Duration
it('rejects invalid duration values', function () {
    $this->actingAs($this->staff);
    
    Livewire::test(AddRide::class)
        ->set('rideTypeId', $this->rideType->id)
        ->set('classificationId', $this->classification->id)
        ->set('rideId', $this->ride->id)
        ->set('showCustomDuration', true)
        ->set('customDuration', -30) // Negative duration
        ->call('startRental')
        ->assertHasErrors(['customDuration']);
});

// Test Case 19: Data Validation - Price Calculation
it('calculates total price automatically', function () {
    $this->actingAs($this->staff);
    
    $component = Livewire::test(AddRide::class)
        ->set('rideTypeId', $this->rideType->id)
        ->set('classificationId', $this->classification->id)
        ->set('duration', 90); // 90 minutes = 1.5 hours
    
    // Price should be calculated as 300 * 1.5 = 450
    expect($component->get('computedTotal'))->toBe(450.00);
});

// Test Case 20: Ride Reappears After Completion
it('hides ride during rental and shows after completion', function () {
    $this->actingAs($this->staff);
    
    // Initially ride should be available
    expect($this->ride->is_active)->toBe(1);
    
    // Create active rental
    $rental = Rental::create([
        'user_id' => $this->staff->id,
        'ride_id' => $this->ride->id,
        'status' => 0, // Active
        'duration_minutes' => 60,
        'start_at' => now(),
        'end_at' => now()->addMinutes(60),
        'price_per_hour_at_time' => 300.00,
        'computed_total' => 300.00
    ]);
    
    // Ride should not be available for new rentals
    $availableRides = Ride::where('is_active', 1)
        ->whereNotIn('id', function($query) {
            $query->select('ride_id')
                ->from('rentals')
                ->where('status', 0); // Active rentals
        })->get();
    
    expect($availableRides->contains($this->ride))->toBeFalse();
    
    // Complete the rental
    $rental->update(['status' => 1]); // Completed
    
    // Ride should be available again
    $availableRides = Ride::where('is_active', 1)
        ->whereNotIn('id', function($query) {
            $query->select('ride_id')
                ->from('rentals')
                ->where('status', 0); // Active rentals
        })->get();
    
    expect($availableRides->contains($this->ride))->toBeTrue();
});

