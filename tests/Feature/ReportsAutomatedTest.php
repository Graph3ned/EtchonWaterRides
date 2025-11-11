<?php

use App\Models\User;
use App\Models\Rental;
use App\Livewire\Sales;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Carbon\Carbon;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->admin = User::factory()->create(['userType' => 1]);
    $this->staff = User::factory()->create(['userType' => 0]);
    
    // Create test rentals for different dates
    $this->createTestRentals();
});

function createTestRentals() {
    // January 2024 rentals
    Rental::factory()->create([
        'user_id' => $this->staff->id,
        'status' => 1, // Completed
        'computed_total' => 450.00,
        'created_at' => Carbon::parse('2024-01-15'),
        'updated_at' => Carbon::parse('2024-01-15'),
    ]);
    
    // March 2024 rentals
    Rental::factory()->create([
        'user_id' => $this->staff->id,
        'status' => 1, // Completed
        'computed_total' => 600.00,
        'created_at' => Carbon::parse('2024-03-10'),
        'updated_at' => Carbon::parse('2024-03-10'),
    ]);
    
    // Current month rental
    Rental::factory()->create([
        'user_id' => $this->staff->id,
        'status' => 1, // Completed
        'computed_total' => 300.00,
        'created_at' => now(),
        'updated_at' => now(),
    ]);
}

// Test Case 21: Generate Financial Report
it('generates financial report with accurate data', function () {
    $this->actingAs($this->admin);
    
    $component = Livewire::test(Sales::class)
        ->set('selectedDateRange', 'This Month')
        ->call('render');
    
    // Should show current month data
    expect($component->get('totalRevenue'))->toBeGreaterThan(0);
});

// Test Case 23: Filter Reports by Date Range
it('filters reports by custom date range', function () {
    $this->actingAs($this->admin);
    
    $component = Livewire::test(Sales::class)
        ->set('selectedDateRange', 'Custom')
        ->set('startDate', '2024-01-01')
        ->set('endDate', '2024-01-31')
        ->call('render');
    
    // Should only show January 2024 data
    $rentals = $component->get('rentals');
    foreach ($rentals as $rental) {
        expect($rental->created_at->month)->toBe(1);
        expect($rental->created_at->year)->toBe(2024);
    }
});

// Test Case 24: Filter Reports by Staff Member
it('filters reports by specific staff member', function () {
    $this->actingAs($this->admin);
    
    $component = Livewire::test(Sales::class)
        ->set('selectedStaff', $this->staff->id)
        ->call('render');
    
    $rentals = $component->get('rentals');
    foreach ($rentals as $rental) {
        expect($rental->user_id)->toBe($this->staff->id);
    }
});

// Test Case 26: Custom Date Range Reports (Single Day)
it('generates report for single day', function () {
    $this->actingAs($this->admin);
    
    $today = now()->format('Y-m-d');
    
    $component = Livewire::test(Sales::class)
        ->set('selectedDateRange', 'Select Day')
        ->set('selectedDay', $today)
        ->call('render');
    
    $rentals = $component->get('rentals');
    foreach ($rentals as $rental) {
        expect($rental->created_at->format('Y-m-d'))->toBe($today);
    }
});

// Test Case 27: Monthly Report Generation
it('generates monthly report', function () {
    $this->actingAs($this->admin);
    
    $component = Livewire::test(Sales::class)
        ->set('selectedDateRange', 'Select Month')
        ->set('selectedMonth', '2024-03')
        ->call('render');
    
    $rentals = $component->get('rentals');
    foreach ($rentals as $rental) {
        expect($rental->created_at->month)->toBe(3);
        expect($rental->created_at->year)->toBe(2024);
    }
});

// Test Case 28: Yearly Report Generation
it('generates yearly report', function () {
    $this->actingAs($this->admin);
    
    $component = Livewire::test(Sales::class)
        ->set('selectedDateRange', 'Select Year')
        ->set('selectedYear', '2024')
        ->call('render');
    
    $rentals = $component->get('rentals');
    foreach ($rentals as $rental) {
        expect($rental->created_at->year)->toBe(2024);
    }
});

