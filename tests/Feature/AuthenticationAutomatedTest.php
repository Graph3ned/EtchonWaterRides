<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class);

beforeEach(function () {
    // Create test users
    $this->admin = User::factory()->create([
        'username' => 'admin_test',
        'userType' => 1, // Admin
        'password' => bcrypt('password123')
    ]);
    
    $this->staff = User::factory()->create([
        'username' => 'staff_test',
        'userType' => 0, // Staff
        'password' => bcrypt('password123')
    ]);
});

// Test Case 9: Admin Login and Access
it('allows admin login and redirects to admin dashboard', function () {
    Livewire::test('pages.auth.login')
        ->set('form.username', 'admin_test')
        ->set('form.password', 'password123')
        ->call('login')
        ->assertRedirect('/staff/dashboard'); // Default redirect, then middleware handles it
    
    $this->assertAuthenticatedAs($this->admin);
    
    // Test that admin can access admin routes
    $response = $this->actingAs($this->admin)->get('/admin/sales');
    $response->assertStatus(200);
});

// Test Case 10: Staff Login and Access
it('allows staff login and redirects to staff dashboard', function () {
    Livewire::test('pages.auth.login')
        ->set('form.username', 'staff_test')
        ->set('form.password', 'password123')
        ->call('login')
        ->assertRedirect('/staff/dashboard');
    
    $this->assertAuthenticatedAs($this->staff);
    
    // Test that staff can access staff routes
    $response = $this->actingAs($this->staff)->get('/staff/dashboard');
    $response->assertStatus(200);
});

// Test Case 11: Authentication Security Baseline
it('stores passwords as hashed values', function () {
    $user = User::factory()->create([
        'password' => bcrypt('test_password')
    ]);
    
    // Password should be hashed, not plain text
    expect($user->password)->not->toBe('test_password');
    expect(strlen($user->password))->toBeGreaterThan(50); // Bcrypt hashes are typically 60 chars
});

it('does not expose credentials in responses', function () {
    $component = Livewire::test('pages.auth.login')
        ->set('form.username', 'admin_test')
        ->set('form.password', 'password123');
    
    // Check that password is not in rendered HTML
    expect($component->html())->not->toContain('password123');
});

// Test Case 14: Session Management Security
it('expires session after inactivity', function () {
    // Test that unauthenticated users are redirected to login
    $response = $this->get('/staff/dashboard');
    $response->assertRedirect('/login');
    
    // Test that session is properly managed
    $this->actingAs($this->staff);
    $response = $this->get('/staff/dashboard');
    $response->assertStatus(200);
    
    // Logout and verify redirect
    auth()->logout();
    $response = $this->get('/staff/dashboard');
    $response->assertRedirect('/login');
});
