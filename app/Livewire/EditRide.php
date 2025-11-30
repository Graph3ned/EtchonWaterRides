<?php

namespace App\Livewire;

use App\Models\Rental;
use App\Models\Ride;
use App\Models\RideType;
use App\Models\Classification;
use Livewire\Component;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;

class EditRide extends Component
{
    public $rentalId;
    public $rideTypeId = '';
    public $classificationId = '';
    public $rideId = '';
    public $life_jacket_quantity = 0;
    public $pricePerHour = 0;   
    public $duration;
    public $startAt;
    public $endAt;
    public $showCustomDuration = false;
    public $customDuration;
    public $extendDuration = 0; 
    public $computedTotal;
    public $rideTypes;
    public $classifications;
    public $rides;
    public $note;
    public $status = Rental::STATUS_ACTIVE;

    public function rules()
    {
        return [
            'rideTypeId' => 'required|exists:ride_types,id',
            'classificationId' => 'required|exists:classifications,id',
            'rideId' => [
                'required',
                'exists:rides,id',
                function ($attribute, $value, $fail) {
                    // Skip validation if rentalId is not set (during initial load)
                    if (!$this->rentalId) {
                        return;
                    }
                    
                    // Skip validation if rideId is empty (during form updates)
                    if (empty($value)) {
                        return;
                    }
                    
                    // Get the current rental to check its current ride
                    $currentRental = Rental::find($this->rentalId);
                    if (!$currentRental) {
                        return;
                    }
                    
                    // If the selected ride is the same as the current rental's ride, allow it
                    if ($currentRental->ride_id == $value) {
                        return;
                    }
                    
                    // Check if the ride is already being used by another active rental
                    $existingRental = Rental::where('ride_id', $value)
                        ->where('status', Rental::STATUS_ACTIVE)
                        ->where('id', '!=', $this->rentalId) // Exclude current rental
                        ->first();
                    
                    if ($existingRental) {
                        $fail('This ride is currently being used by another active rental.');
                    }
                }
            ],
            'life_jacket_quantity' => 'required|integer|min:0|max:5',
            'duration' => 'required|integer|min:1|max:500',
            'pricePerHour' => 'required|numeric|min:0.01',
            'computedTotal' => 'required|numeric|min:0',
            'startAt' => 'required|date_format:H:i:s',
            'endAt' => 'required|date_format:H:i:s',
            'note' => 'nullable|string|max:1000',
        ];
    }

    public function mount($rentalId)
    {
        // Load ride types
        $this->rideTypes = RideType::all();
        
        $this->rentalId = $rentalId;
        $this->loadRental();
    }

    public function updated($propertyName)
    {
        if ($propertyName === 'rideTypeId') {
            $this->classificationId = '';
            $this->rideId = '';
            $this->loadClassifications();
        }

        if ($propertyName === 'classificationId') {
            $this->rideId = '';
            $this->loadRides();
        }

        if (in_array($propertyName, ['rideTypeId', 'classificationId', 'rideId', 'extendDuration', 'showCustomDuration', 'customDuration'])) {
            $this->updateEndAt();
            $this->updateComputedTotal();
        }
    }

    private function loadRental()
    {
        $rental = Rental::with(['ride.classification.rideType'])->findOrFail($this->rentalId);

        $this->rideTypeId = $rental->ride->classification->rideType->id;
        $this->classificationId = $rental->ride->classification_id;
        $this->rideId = $rental->ride_id;
        $this->life_jacket_quantity = $rental->life_jacket_quantity;
        $this->pricePerHour = $rental->price_per_hour_at_time;
        $this->duration = $rental->duration_minutes;
        $this->startAt = $rental->start_at->format('H:i:s');
        $this->endAt = $rental->end_at->format('H:i:s');
        $this->computedTotal = $rental->computed_total;
        $this->note = $rental->note;
        $this->status = $rental->status;

        // Load classifications and rides for the selected ride type
        $this->loadClassifications();
        $this->loadRides();
    }

    private function loadClassifications()
    {
        if ($this->rideTypeId) {
            $this->classifications = Classification::where('ride_type_id', $this->rideTypeId)->get();
        } else {
            $this->classifications = collect();
        }
    }

    private function loadRides()
    {
        if ($this->classificationId) {
            // Get rides that are either available or currently used by this rental
            $this->rides = Ride::where('classification_id', $this->classificationId)
                ->where(function($query) {
                    $query->where('is_active', Ride::STATUS_AVAILABLE)
                          ->orWhere(function($subQuery) {
                              $subQuery->where('is_active', Ride::STATUS_USED)
                                       ->whereHas('rentals', function($rentalQuery) {
                                           $rentalQuery->where('id', $this->rentalId)
                                                      ->where('status', Rental::STATUS_ACTIVE);
                                       });
                          });
                })
                ->get();
        } else {
            $this->rides = collect();
        }
    }

    private function updateEndAt()
    {
        $startTime = Carbon::createFromFormat('H:i:s', $this->startAt);
        $extensionMinutes = $this->showCustomDuration ? (int)$this->customDuration : (int)$this->extendDuration;
        $newEndTime = $startTime->copy()->addMinutes($this->duration + $extensionMinutes);
        $this->endAt = $newEndTime->format('H:i:s');
    }

    private function updateComputedTotal()
    {
        $extensionMinutes = $this->showCustomDuration ? (int)$this->customDuration : (int)$this->extendDuration;
        $totalMinutes = $this->duration + $extensionMinutes;
        $pricePerMinute = $this->pricePerHour / 60;
        $this->computedTotal = round($pricePerMinute * $totalMinutes, 2);
    }


    public function updateRides()
    {
        // Validate the form data
        $this->validate($this->rules());

        $rental = Rental::findOrFail($this->rentalId);

        $extensionMinutes = $this->showCustomDuration ? (int)$this->customDuration : (int)$this->extendDuration;
        $newDuration = $this->duration + $extensionMinutes;

        // Create new end datetime
        $today = Carbon::today('Asia/Manila');
        $startDateTime = $today->copy()->setTimeFromTimeString($this->startAt);
        
        // Calculate end datetime from start + duration to handle midnight crossover correctly
        $extensionMinutes = $this->showCustomDuration ? (int)$this->customDuration : (int)$this->extendDuration;
        $totalDuration = $this->duration + $extensionMinutes;
        $endDateTime = $startDateTime->copy()->addMinutes($totalDuration);

        // Get current data for _at_time columns
        $ride = Ride::with('classification.rideType')->find($this->rideId);
        $user = Auth::user();

        $rental->update([
            'ride_id' => $this->rideId,
            'life_jacket_quantity' => $this->life_jacket_quantity,
            'duration_minutes' => $newDuration,
            'start_at' => $startDateTime,
            'end_at' => $endDateTime,
            'computed_total' => $this->computedTotal,
            'note' => $this->note,
            // Update _at_time columns with current data
            'user_name_at_time' => $user->name,
            'ride_identifier_at_time' => $ride->identifier ?? 'Unknown',
            'classification_name_at_time' => $ride->classification->name ?? 'Unknown',
            'ride_type_name_at_time' => optional($ride->classification->rideType)->name ?? 'Unknown',
            'price_per_hour_at_time' => $this->pricePerHour,
        ]);

        session()->flash('message', 'Rental updated successfully!');
        $this->dispatch('rideUpdated');
        $this->dispatch('refreshStaffDashboard');
    }

    public function updateStatus()
    {
        $rental = Rental::findOrFail($this->rentalId);

        // Get current data for _at_time columns
        $ride = Ride::with('classification.rideType')->find($rental->ride_id);
        $user = Auth::user();

        $rental->update([
            'status' => Rental::STATUS_COMPLETED,
            // Update _at_time columns with current data
            'user_name_at_time' => $user->name,
            'ride_identifier_at_time' => $ride->identifier ?? 'Unknown',
            'classification_name_at_time' => $ride->classification->name ?? 'Unknown',
            'ride_type_name_at_time' => optional($ride->classification->rideType)->name ?? 'Unknown',
            'price_per_hour_at_time' => $rental->price_per_hour_at_time, // Keep original price
        ]);

        // Update ride status back to AVAILABLE
        if ($ride) {
            $ride->update(['is_active' => Ride::STATUS_AVAILABLE]);
        }

        session()->flash('message', 'Rental completed successfully!');
        $this->dispatch('rideUpdated');
        $this->dispatch('refreshStaffDashboard');
    }

    public function render()
    {
        return view('livewire.edit-ride');
    }
}
