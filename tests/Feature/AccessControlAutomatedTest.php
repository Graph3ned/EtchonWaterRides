<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->admin = User::factory()->create(['userType' => 1]);
    $this->staff = User::factory()->create(['userType' => 0]);
});

// Test Case 12: Unauthorized Access Prevention
it('redirects unauthenticated users to login', function () {
    $protectedRoutes = [
        '/admin/sales',
        '/staff/dashboard',
        '/admin/staffs'
    ];
    
    foreach ($protectedRoutes as $route) {
        $response = $this->get($route);
        $response->assertRedirect('/login');
    }
});

// Test Case 13: Staff Access to Admin Functions
it('prevents staff from accessing admin functions', function () {
    $this->actingAs($this->staff);
    
    $adminRoutes = [
        '/admin/sales',
        '/admin/staffs',
        '/admin/logs'
    ];
    
    foreach ($adminRoutes as $route) {
        $response = $this->get($route);
        // Should redirect to staff dashboard or show 403
        expect($response->status())->toBeIn([302, 403]);
        
        if ($response->status() === 302) {
            $response->assertRedirect('/staff/dashboard');
        }
    }
});

it('allows admin to access admin functions', function () {
    $this->actingAs($this->admin);
    
    $response = $this->get('/admin/sales');
    $response->assertStatus(200);
});

it('allows staff to access staff functions', function () {
    $this->actingAs($this->staff);
    
    $response = $this->get('/staff/dashboard');
    $response->assertStatus(200);
});

