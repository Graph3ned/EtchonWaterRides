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
        'ride_type' => 'required|string',
        'classification' => 'required|string',
        'price_per_hour' => 'required|integer|min:1',
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
