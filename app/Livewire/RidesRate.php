<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\RideType;
use Illuminate\Support\Facades\DB;
class RidesRate extends Component
{

    public $rideTypes = [];
    public $showModal = false;
    public $modalDetails;
    public $rideToDelete;
    public $showAddNewRide = false;
    public $viewDetailsModal = false;

    public $rideId;
    public function mount(){
        $this->rideTypes = RideType::with(['classifications.rides'])->orderBy('name')->get();
    }
    public function confirmDelete($ride_type)
    {
        $this->rideToDelete = $ride_type;
        $this->modalDetails = "Are you sure you want to delete this ride type? It will also delete its classifications. Rides with rentals cannot be removed.";
        $this->showModal = true;
    }
    public function closeModal()
    {
        $this->showModal = false;
    }
    public function deleteRide()
    {
        // Attempt to delete the ride type by name
        $rideType = RideType::where('name', $this->rideToDelete)->first();
        if (!$rideType) {
            $this->showModal = false;
            return;
        }

        // Prevent deletion if any rides under its classifications have rentals
        $hasRentals = DB::table('rentals')
            ->join('rides', 'rentals.ride_id', '=', 'rides.id')
            ->join('classifications', 'rides.classification_id', '=', 'classifications.id')
            ->where('classifications.ride_type_id', $rideType->id)
            ->exists();

        if ($hasRentals) {
            session()->flash('success', 'Cannot delete ride type with existing rentals.');
            $this->showModal = false;
            return redirect()->route('RidesRate');
        }

        // Safe to delete (will cascade to classifications as per FK)
        $rideType->delete();

        session()->flash('success', 'Ride type deleted.');
        $this->showModal = false;
        return redirect()->route('RidesRate');
    }

    public function showViewDetails($rideId)
    {
        $this->rideId = $rideId;
        $this->viewDetailsModal = true;
    }
    public function render()
    {
        // Refresh list each render to reflect changes
        $this->rideTypes = RideType::with(['classifications.rides'])->orderBy('name')->get();
        return view('livewire.rides-rate', [
            'rideTypes' => $this->rideTypes,
        ]);
    }

    public function getRideCounts($rideType)
    {
        $activeCount = 0;
        $inactiveCount = 0;
        
        foreach ($rideType->classifications as $classification) {
            $activeCount += $classification->rides->where('is_active', true)->count();
            $inactiveCount += $classification->rides->where('is_active', false)->count();
        }
        
        return [
            'active' => $activeCount,
            'inactive' => $inactiveCount,
            'total' => $activeCount + $inactiveCount
        ];
    }
}
