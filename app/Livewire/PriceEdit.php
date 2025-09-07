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
        'classification' => 'required|string',
        'price_per_hour' => 'required|integer|min:1',
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

        $load = prices::find($this->id);

       $load->classification = $this->classification;
       $load->price_per_hour = $this->price_per_hour;
       $load->ride_type = $this->ride_type;
       $load->save();

       return redirect()->route('ViewDetails', ['ride_type' => $this->ride_type]);

    }

    public function render()
    {
        return view('livewire.price-edit');
    }
}
