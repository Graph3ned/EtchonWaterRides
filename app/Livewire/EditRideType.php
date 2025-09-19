<?php

namespace App\Livewire;

use App\Models\RideType;
use Livewire\Component;

class EditRideType extends Component
{
    public $rideTypeId;
    public $rideType;
    public $name;

    protected $rules = [
        'name' => 'required|string|max:255|unique:ride_types,name',
    ];

    protected $messages = [
        'name.required' => 'Ride type name is required.',
        'name.unique' => 'This ride type name already exists.',
    ];

    // Mount method to load the ride type data
    public function mount($rideTypeId)
    {
        $this->rideTypeId = $rideTypeId;
        $this->rideType = RideType::findOrFail($rideTypeId);
        $this->name = $this->rideType->name;
    }

    // Update the ride type name
    public function updateRideType()
    {
        // Temporarily ignore unique rule for current record
        $this->rules['name'] = 'required|string|max:255|unique:ride_types,name,' . $this->rideType->id;
        
        $this->validate();

        try {
            $this->rideType->update([
                'name' => trim($this->name),
            ]);

            session()->flash('success', 'Ride type updated successfully!');
            return redirect()->route('ViewDetails', ['rideTypeId' => $this->rideType->id]);

        } catch (\Exception $e) {
            session()->flash('error', 'Error updating ride type: ' . $e->getMessage());
        }
    }

    // Render the component view
    public function render()
    {
        return view('livewire.edit-ride-type');
    }
}
