<?php

use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;
use Carbon\Carbon;
use App\Models\Rental;

// Route::view('/', 'welcome');
Route::get('/', function () {
    return redirect()->route('login');
});
Route::get('/guest', \App\Livewire\GuestRideAvailability::class)
    ->middleware('guest')
    ->name('guest');

// Password reset routes
Route::get('/forgot-password', \App\Livewire\ForgotPassword::class)
    ->middleware('guest')
    ->name('password.request');

Route::get('/reset-password/{token}/{username}', \App\Livewire\ResetPassword::class)
    ->middleware('guest')
    ->name('password.reset');

Route::view('/staff/dashboard', 'staff-dashboard')
    ->middleware(['auth', 'verified', 'staff'])
    ->name('dashboard');

Route::view('/staff/create', 'create')
    ->middleware(['auth', 'verified', 'staff'])
    ->name('create');

Route::view('/staff/{rideId}/edit', 'edit')
    ->middleware(['auth', 'verified', 'staff'])
    ->name('edit-ride');

Route::view('/admin/sales', 'sales')
    ->middleware(['auth', 'verified', 'admin'])
    ->name('sales');

Route::view('/admin/rides-rate', 'RidesRate')
    ->middleware(['auth', 'verified', 'admin'])
    ->name('RidesRate');

Route::view('/admin/ride-availability', 'RideAvailability')
    ->middleware(['auth', 'verified'])
    ->name('RideAvailability');

Route::view('/admin/add-ride', 'AddWaterRide')
    ->middleware(['auth', 'verified', 'admin'])
    ->name('AddWaterRide');

Route::view('/admin/edit-ride-type/{rideTypeId}', 'EditRideType')
    ->middleware(['auth', 'verified', 'admin'])
    ->name('EditRideType');

Route::view('/admin/{id}/sales-edit', 'sales-edit')
    ->middleware(['auth', 'verified', 'admin'])
    ->name('sales-edit');

Route::view('/admin/staffs', 'staffs')
    ->middleware(['auth', 'verified', 'admin'])
    ->name('staffs');

// Route::view('/admin/updateStaff/{staffId}', 'updateStaff')
//     ->middleware(['auth', 'verified', 'admin'])
//     ->name('updateStaff');

Route::view('/admin/staff-edit/{id}', 'staffEdit')
    ->middleware(['auth', 'verified', 'admin'])
    ->name('staffEdit');
Route::middleware(['auth', 'verified', 'admin'])->group(function () {
        Volt::route('/admin/staff-register', 'pages.auth.register')
            ->name('register');    
    });

Route::view('/admin/view-details/{rideTypeId}', 'ViewDetails')
    ->middleware(['auth', 'verified', 'admin'])
    ->name('ViewDetails');

Route::view('/admin/add-ride-classification/{rideTypeId}', 'AddRideClassification')
    ->middleware(['auth', 'verified', 'admin'])
    ->name('AddRideClassification');

Route::view('/admin/edit-ride-type/{rideTypeId}', 'EditRideType')
    ->middleware(['auth', 'verified', 'admin'])
    ->name('EditRideType');

Route::get('/admin/edit-classification/{classificationId}', function ($classificationId) {
    return view('EditClassification', ['classificationId' => $classificationId]);
})->middleware(['auth', 'verified', 'admin'])
    ->name('EditClassification');

Route::view('/admin/priceEdit/{id}', 'priceEdit')
    ->middleware(['auth', 'verified', 'admin'])
    ->name('priceEdit');

Route::view('/admin/logs', 'logs')
    ->middleware(['auth', 'verified', 'admin'])
    ->name('logs');


// Report Generation Routes
Route::post('/admin/reports/generate', [App\Http\Controllers\ReportController::class, 'generate'])
    ->middleware(['auth', 'verified', 'admin'])
    ->name('reports.generate');

Route::get('/admin/reports/export/{type}', [App\Http\Controllers\ReportController::class, 'export'])
    ->middleware(['auth', 'verified', 'admin'])
    ->name('reports.export');

// Reports Dashboard Route
Route::get('/admin/reports', function () {
    return view('reports');
})->middleware(['auth', 'verified', 'admin'])->name('admin.reports');

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');
require __DIR__.'/auth.php';
