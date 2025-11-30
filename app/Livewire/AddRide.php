<?php

namespace App\Livewire;

use App\Models\Rental;
use App\Models\Ride;
use App\Models\RideType;
use App\Models\Classification;
use Livewire\Component;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Collection;

class AddRide extends Component
{
    public $rideTypeId = '';
    public $classificationId = '';
    public $rideId = '';
    public $duration = 60; // Default to 1 hour (predefined duration)
    public $pricePerHour = 0;
    public $computedTotal = 0;
    public $startAt;
    public $note = '';
    public $endAt;
    public $showCustomDuration = false; // Controls the visibility of custom duration
    public $customDuration; // For custom duration input
    public $life_jacket_quantity = 0;
    public Collection $rideTypes;
    public Collection $classifications;
    public Collection $rides;
    public $status = Rental::STATUS_ACTIVE;

    public function rules()
    {
        $rules = [
            'rideTypeId' => 'required|exists:ride_types,id',
            'classificationId' => 'required|exists:classifications,id',
            'rideId' => 'required|exists:rides,id',
            'note' => 'nullable|string',
            'duration' => 'required|integer|min:1|max:500',
            'pricePerHour' => 'required|numeric|min:1.00',
            'life_jacket_quantity' => 'required|integer|min:0',
            'computedTotal' => 'required|numeric',
            'startAt' => 'required|date_format:H:i:s',
            'endAt' => 'required|date_format:H:i:s',
            'status' => 'required|integer',
        ];

        if ($this->showCustomDuration) {
            $rules['customDuration'] = 'required|integer|min:1|max:500';
        }

        return $rules;
    }

    public function mount()
    {
        // Load ride types
        $this->rideTypes = RideType::all();
        
        // Set default values
        if ($this->rideTypes->count() > 0) {
            $this->rideTypeId = $this->rideTypes->first()->id;
            $this->loadClassifications();
        }

        // Set default times
        $this->startAt = Carbon::now('Asia/Manila')->format('H:i:s');
        $this->updateEndAt();
        $this->updatePricePerHour();
        $this->updateComputedTotal();
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

        if (in_array($propertyName, ['rideTypeId', 'classificationId', 'rideId', 'duration', 'customDuration', 'showCustomDuration'])) {
            $this->updatePricePerHour();
            $this->updateComputedTotal();
            $this->updateEndAt();
        }
    }

    private function loadClassifications()
    {
        if ($this->rideTypeId) {
            $this->classifications = Classification::where('ride_type_id', $this->rideTypeId)->get();
            if ($this->classifications->count() > 0) {
                $this->classificationId = $this->classifications->first()->id;
                $this->loadRides();
            }
        } else {
            $this->classifications = collect();
        }
    }

    private function loadRides()
    {
        if ($this->classificationId) {
            $this->rides = Ride::where('classification_id', $this->classificationId)
                ->where('is_active', Ride::STATUS_AVAILABLE)
                ->get();
            if ($this->rides->count() > 0) {
                $this->rideId = $this->rides->first()->id;
            }
        } else {
            $this->rides = collect();
        }
    }

    private function updatePricePerHour()
    {
        if ($this->classificationId) {
            $classification = Classification::find($this->classificationId);
            $this->pricePerHour = $classification ? $classification->price_per_hour : 0;
        } else {
            $this->pricePerHour = 0;
        }
    }

    private function updateComputedTotal()
    {
        // Calculate total price based on selected duration and pricePerHour
        $durationInMinutes = $this->showCustomDuration ? (int) $this->customDuration : (int) $this->duration;
        $pricePerMinute = $this->pricePerHour / 60;
        $this->computedTotal = round($pricePerMinute * $durationInMinutes, 2);
    }

    private function updateEndAt()
    {
        // Convert the start time to a Carbon instance and calculate the end time
        $startTime = Carbon::createFromFormat('H:i:s', $this->startAt, 'Asia/Manila');
        $durationInMinutes = $this->showCustomDuration ? (int) $this->customDuration : (int) $this->duration;

        // Calculate the end time by adding the duration
        $endTime = $startTime->copy()->addMinutes($durationInMinutes);

        // Set endAt to the formatted string in 24-hour format
        $this->endAt = $endTime->format('H:i:s');
    }

    public function submit()
    {
        // Get the currently logged-in user
        $user = Auth::user();
        
        // Validate the input fields
        $this->validate($this->rules());

        // Get the selected ride and classification
        $ride = Ride::find($this->rideId);
        $classification = Classification::with('rideType')->find($this->classificationId);

        if (!$ride || !$classification) {
            session()->flash('error', 'Selected ride or classification not found.');
            return;
        }

        // Create start and end datetime objects
        $today = Carbon::today('Asia/Manila');
        $startDateTime = $today->copy()->setTimeFromTimeString($this->startAt);
        
        // Calculate end datetime from start + duration to handle midnight crossover correctly
        $durationInMinutes = $this->showCustomDuration ? (int) $this->customDuration : (int) $this->duration;
        $endDateTime = $startDateTime->copy()->addMinutes($durationInMinutes);

        // Save the rental record to the database
        $rental = Rental::create([
            'user_id' => $user->id,
            'ride_id' => $this->rideId,
            'status' => $this->status,
            'start_at' => $startDateTime,
            'end_at' => $endDateTime,
            'duration_minutes' => $this->showCustomDuration ? $this->customDuration : $this->duration,
            'life_jacket_quantity' => $this->life_jacket_quantity,
            'note' => $this->note,
            'user_name_at_time' => $user->name,
            'ride_identifier_at_time' => $ride->identifier,
            'classification_name_at_time' => $classification->name,
            'ride_type_name_at_time' => optional($classification->rideType)->name ?? 'Unknown',
            'price_per_hour_at_time' => $this->pricePerHour,
            'computed_total' => $this->computedTotal,
        ]);

        // Update ride status to USED
        $ride->update(['is_active' => Ride::STATUS_USED]);

        // Flash success message and refresh the dashboard
        session()->flash('message', 'Ride rental created successfully!');
        $this->dispatch('rideCreated');
        $this->dispatch('refreshStaffDashboard');
        $this->resetForm();
    }

    private function resetForm()
    {
        $this->rideTypeId = $this->rideTypes->first()->id ?? '';
        $this->classificationId = '';
        $this->rideId = '';
        $this->note = '';
        $this->duration = 60;
        $this->life_jacket_quantity = 0;
        $this->startAt = Carbon::now('Asia/Manila')->format('H:i:s');
        $this->loadClassifications();
        $this->updateEndAt();
        $this->updatePricePerHour();
        $this->updateComputedTotal();
    }

    public function render()
    {
        return view('livewire.add-ride');
    }
}
