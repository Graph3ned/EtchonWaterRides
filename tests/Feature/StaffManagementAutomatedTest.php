<?php

use App\Models\User;
use App\Livewire\Staffs;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->admin = User::factory()->create(['userType' => 1]);
    $this->existingStaff = User::factory()->create([
        'userType' => 0,
        'email' => 'existing@example.com',
        'name' => 'Existing Staff'
    ]);
});

// Test Case 17: Duplicate Prevention
it('prevents duplicate email registration', function () {
    $this->actingAs($this->admin);
    
    // Try to create staff with existing email
    $response = $this->post('/register', [
        'name' => 'New Staff',
        'email' => 'existing@example.com', // Duplicate email
        'username' => 'newstaff',
        'password' => 'password123',
        'password_confirmation' => 'password123'
    ]);
    
    // Should have validation errors
    $response->assertSessionHasErrors(['email']);
    
    // Should not create duplicate user
    expect(User::where('email', 'existing@example.com')->count())->toBe(1);
});

it('prevents duplicate name registration', function () {
    $this->actingAs($this->admin);
    
    // Try to create staff with existing name
    $response = $this->post('/register', [
        'name' => 'Existing Staff', // Duplicate name
        'email' => 'new@example.com',
        'username' => 'newstaff',
        'password' => 'password123',
        'password_confirmation' => 'password123'
    ]);
    
    // Should have validation errors for name
    $response->assertSessionHasErrors(['name']);
    
    // Should not create duplicate user
    expect(User::where('name', 'Existing Staff')->count())->toBe(1);
});

it('allows staff creation with unique credentials', function () {
    $this->actingAs($this->admin);
    
    $response = $this->post('/register', [
        'name' => 'New Staff Member',
        'email' => 'newstaff@example.com',
        'username' => 'newstaff',
        'password' => 'password123',
        'password_confirmation' => 'password123'
    ]);
    
    $response->assertRedirect('/staff/dashboard');
    
    // Verify user was created
    expect(User::where('email', 'newstaff@example.com')->exists())->toBeTrue();
    expect(User::where('name', 'New Staff Member')->exists())->toBeTrue();
});

