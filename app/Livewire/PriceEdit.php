<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\prices;
class PriceEdit extends Component
{
    public $ride_type;
    public $id;
    public $classification;
    public $price_per_hour;
    // public $rideTypes = []; 

    protected $rules = [
        'classification' => 'required|string|max:255',
        'price_per_hour' => 'required|numeric|min:0.01|max:999999.99',
    ];

    protected $messages = [
        'classification.required' => 'Classification is required.',
        'classification.string' => 'Classification must be text.',
        'classification.max' => 'Classification cannot exceed 255 characters.',
        'price_per_hour.required' => 'Price per hour is required.',
        'price_per_hour.numeric' => 'Price per hour must be a number.',
        'price_per_hour.min' => 'Price per hour must be greater than 0.',
        'price_per_hour.max' => 'Price per hour cannot exceed 999,999.99.',
    ];

    public function mount()
    {
       $load = prices::find($this->id);

       $this->classification = $load->classification;
       $this->price_per_hour = $load->price_per_hour;
       $this->ride_type = $load->ride_type;

    }

    public function update()
    {
        // Validate the input data
        $this->validate();

        $load = prices::find($this->id);

        $load->classification = $this->classification;
        $load->price_per_hour = $this->price_per_hour;
        $load->ride_type = $this->ride_type;
        $load->save();

        session()->flash('message', 'Price updated successfully!');
        return redirect()->route('ViewDetails', ['ride_type' => $this->ride_type]);
    }

    public function render()
    {
        return view('livewire.price-edit');
    }
}
