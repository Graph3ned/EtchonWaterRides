<?php

namespace App\Livewire;

use App\Models\Rental;
use Livewire\Component;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use App\Models\prices;

class AddRide extends Component
{
    public $rideType = '';
    public $classification = '';
    public $duration = 60; // Default to 1 hour (predefined duration)
    public $pricePerHour = 0;
    public $totalPrice = 0;
    public $timeStart;
    public $note= '';
    public $timeEnd;
    public $showCustomDuration = false; // Controls the visibility of custom duration
    public $customDuration; // For custom duration input
    public $life_jacket_usage = 0;
    public $prices = [];
    public $status = '0';

    public function rules()
    {
        $rules = [
            'rideType' => 'required|string',
            'classification' => 'required|string',
            'note' => 'nullable|string',
            'duration' => 'required|integer|min:1|max:500',
            'pricePerHour' => 'required|numeric|min:1.00',
            'life_jacket_usage' => 'required|integer|min:0',
            'totalPrice' => 'required|integer',
            'timeStart' => 'required|date_format:H:i:s',
            'timeEnd' => 'required|date_format:H:i:s',
            'status' => 'required|string',
        ];

        if ($this->showCustomDuration) {
            $rules['customDuration'] = 'required|integer|min:1|max:500';
        }

        return $rules;
    }

    public function mount()
    {
        // Fetch prices from the database and group by ride_type
        $this->prices = prices::all()
            ->groupBy('ride_type')
            ->map(function ($group) {
                return $group->pluck('price_per_hour', 'classification')->toArray();
            })
            ->toArray();

        // Set default values for rideType and classification
        $this->rideType = array_key_first($this->prices);
        $this->classification = array_key_first($this->prices[$this->rideType]);

        // Set default times
        $this->timeStart = Carbon::now('Asia/Manila')->format('H:i:s');  // Set default timeStart to 24-hour format
        $this->updateTimeEnd();
        $this->updatePricePerHour();
        $this->updateTotalPrice();
    }

    public function updated($propertyName)
    {
        // Log property updates for debugging
        logger()->info('Property updated:', [
            'property' => $propertyName,
            'value' => $this->$propertyName
        ]);

        if ($propertyName === 'rideType') {
            $this->classification = array_key_first($this->prices[$this->rideType]) ?? '';
        }

        if (in_array($propertyName, ['rideType', 'classification', 'duration', 'customDuration', 'showCustomDuration'])) {
            $this->updatePricePerHour();
            $this->updateTotalPrice();
            $this->updateTimeEnd();
        }
    }

    private function updatePricePerHour()
    {
        // Update pricePerHour based on selected rideType and classification
        if ($this->rideType && isset($this->prices[$this->rideType]) && isset($this->prices[$this->rideType][$this->classification])) {
            $this->pricePerHour = $this->prices[$this->rideType][$this->classification];
        } else {
            $this->pricePerHour = 0;
        }
    }

    private function updateTotalPrice()
    {
        // Calculate total price based on selected duration and pricePerHour
        $durationInMinutes = $this->showCustomDuration ? (int) $this->customDuration : (int) $this->duration;
        $pricePerMinute = $this->pricePerHour / 60;
        $this->totalPrice = (int) round($pricePerMinute * $durationInMinutes, 2);
    }

    private function updateTimeEnd()
    {
        // Convert the start time to a Carbon instance and calculate the end time
        $startTime = Carbon::createFromFormat('H:i:s', $this->timeStart, 'Asia/Manila');
        $durationInMinutes = $this->showCustomDuration ? (int) $this->customDuration : (int) $this->duration;

        // Calculate the end time by adding the duration
        $endTime = $startTime->copy()->addMinutes($durationInMinutes);

        // Set timeEnd to the formatted string in 24-hour format
        $this->timeEnd = $endTime->format('H:i:s');
    }

    public function submit()
    {
        // Get the currently logged-in user
        $user = Auth::user();
        
        // Validate the input fields
        $this->validate($this->rules());

        // Save the rental record to the database
        Rental::create([
            'rideType' => $this->rideType,
            'classification' => $this->classification,
            'note' => $this->note,
            'duration' => $this->showCustomDuration ? $this->customDuration : $this->duration,
            'pricePerHour' => $this->pricePerHour,
            'life_jacket_usage' => $this->life_jacket_usage,
            'totalPrice' => $this->totalPrice,
            'timeStart' => $this->timeStart,
            'timeEnd' => $this->timeEnd,
            'user' => $user->name,
            'status' => $this->status,
            'created_at' => now(),
        ]);

        // Flash success message and redirect to the dashboard
        session()->flash('message', 'Ride created successfully!');
        return redirect()->route('dashboard');
    }

    public function render()
    {
        // Return the view for this component
        return view('livewire.add-ride');
    }
}
