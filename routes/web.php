<?php

use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;
use Carbon\Carbon;
use App\Models\Rental;

// Route::view('/', 'welcome');
Route::get('/', function () {
    return redirect()->route('login');
});

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

Route::view('/admin/create', 'AddWaterRide')
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

Route::get('/admin/generate-report', function (\Illuminate\Http\Request $request) {
	$selectedUser = session('selected_staff', '');
	$selectedRideType = session('selected_ride_type', '');
	$classification = session('selected_classification', '');
	$dateRange = session('date_range', '');
	$startDate = session('start_date', '');
	$endDate = session('end_date', '');
	$selectedDay = session('selected_day', '');
	$selectedMonth = session('selected_month', '');

	$query = Rental::query();

	if ($selectedUser !== '') {
		$query->where('user', $selectedUser);
	}
	if ($selectedRideType !== '') {
		$query->where('rideType', $selectedRideType);
	}
	if ($classification !== '') {
		$query->where('classification', $classification);
	}

	switch ($dateRange) {
		case 'today':
			$query->whereDate('created_at', Carbon::today());
			break;
		case 'yesterday':
			$query->whereDate('created_at', Carbon::yesterday());
			break;
		case 'this_week':
			$query->whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]);
			break;
		case 'last_week':
			$query->whereBetween('created_at', [Carbon::now()->subWeek()->startOfWeek(), Carbon::now()->subWeek()->endOfWeek()]);
			break;
		case 'this_month':
			$query->whereYear('created_at', Carbon::now()->year)
				->whereMonth('created_at', Carbon::now()->month);
			break;
		case 'last_month':
			$query->whereYear('created_at', Carbon::now()->subMonth()->year)
				->whereMonth('created_at', Carbon::now()->subMonth()->month);
			break;
		case 'this_year':
			$query->whereYear('created_at', Carbon::now()->year);
			break;
		case 'last_year':
			$query->whereYear('created_at', Carbon::now()->subYear()->year);
			break;
		case 'select_day':
			if ($selectedDay) {
				$query->whereDate('created_at', $selectedDay);
			}
			break;
		case 'select_month':
			if ($selectedMonth) {
				try {
					$date = Carbon::parse($selectedMonth . '-01');
					$query->whereYear('created_at', $date->year)
						->whereMonth('created_at', $date->month);
				} catch (\Exception $e) {
					// ignore invalid month
				}
			}
			break;
		case 'custom':
			if ($startDate && $endDate) {
				$query->whereDate('created_at', '>=', $startDate)
					->whereDate('created_at', '<=', $endDate);
			}
			break;
	}

	// Get all columns from the rentals table
	$rides = $query->orderBy('created_at', 'desc')->get();

	$filename = 'complete_rentals_report_' . Carbon::now()->format('Ymd_His') . '.csv';

	return response()->streamDownload(function () use ($rides) {
		$handle = fopen('php://output', 'w');
		// UTF-8 BOM for Excel
		fprintf($handle, chr(0xEF) . chr(0xBB) . chr(0xBF));
		
		// CSV Headers - all columns from the table
		fputcsv($handle, [
			'No.',
			'Staff',
			'Ride Type',
			'Classification',
			'Duration (minutes)',
			'Life Jackets',
			'Total Price',
			'Start Time',
			'End Time',
			'Date',
			'Note',
			
		]);

		// CSV Data
		$counter = 1;
		foreach ($rides as $ride) {
			fputcsv($handle, [
				$counter++,
				$ride->user,
				str_replace('_', ' ', $ride->rideType),
				str_replace('_', ' ', $ride->classification),
				$ride->duration >= 60 
					? (intdiv($ride->duration, 60) . 'hr' . ($ride->duration % 60 > 0 ? ' ' . ($ride->duration % 60) . 'min' : ''))
					: $ride->duration . 'min',
				$ride->life_jacket_usage,
				number_format((float) $ride->totalPrice, 2, '.', ''),
				Carbon::parse($ride->timeStart)->format('h:i A'),
				Carbon::parse($ride->timeEnd)->format('h:i A'),
				Carbon::parse($ride->created_at)->format('M/d/Y'),
				$ride->note ?? '-',
			]);
		}
		fclose($handle);
	}, $filename, [
		'Content-Type' => 'text/csv; charset=UTF-8',
		'Cache-Control' => 'no-store, no-cache, must-revalidate',
	]);
})->middleware(['auth', 'verified', 'admin'])->name('admin.generate-report');

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');
require __DIR__.'/auth.php';
