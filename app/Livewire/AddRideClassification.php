<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\prices;

class AddRideClassification extends Component
{
    public $ride_type;
    public $classification;
    public $price_per_hour;
    public $rideTypes = []; // To store the list of ride types

    protected $rules = [
        'ride_type' => 'required|string|max:255',
        'classification' => 'required|string|max:255',
        'price_per_hour' => 'required|numeric|min:0.01|max:999999.99',
    ];

    protected $messages = [
        'ride_type.required' => 'Ride type is required.',
        'ride_type.string' => 'Ride type must be text.',
        'ride_type.max' => 'Ride type cannot exceed 255 characters.',
        'classification.required' => 'Classification is required.',
        'classification.string' => 'Classification must be text.',
        'classification.max' => 'Classification cannot exceed 255 characters.',
        'price_per_hour.required' => 'Price per hour is required.',
        'price_per_hour.numeric' => 'Price per hour must be a number.',
        'price_per_hour.min' => 'Price per hour must be greater than 0.',
        'price_per_hour.max' => 'Price per hour cannot exceed 999,999.99.',
    ];

    public function mount($ride_type)
    {
        // Set the ride_type from the route parameter
        $this->ride_type = $ride_type;

    }

    public function submit()
    {
        $this->validate();

        // Capitalize the first letter of every word
        $this->classification = ucwords($this->classification);

        // Replace spaces with underscores in both ride_type and classification
        $this->classification = str_replace(' ', '_', $this->classification);

        // Save the data to the database
        prices::create([
            'ride_type' => $this->ride_type,
            'classification' => $this->classification,
            'price_per_hour' => $this->price_per_hour,
        ]);

        // Redirect with a success message
        session()->flash('success', 'Price classification added successfully!');
        return redirect()->route('ViewDetails', ['ride_type' => $this->ride_type]);

    }

    public function render()
    {
        return view('livewire.add-ride-classification');
    }
}
