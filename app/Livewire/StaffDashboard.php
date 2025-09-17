<?php
namespace App\Livewire;

use App\Models\Rental;
use Livewire\Component;
use Carbon\Carbon;

class StaffDashboard extends Component
{
    public $rides = [];
    public $totalPrice = 0;
    public $alertMessage = null; 
    public $showModal = false;
    public $modalDetails;
    public $rideToDelete;
    public $showAddRides = false;
    public $showEditModal = false;
    public $editingRideId = null;
    public $rideFilter = 'ongoing';
    public $markedAsDone = [];
    //public $status = '0';

    protected $listeners = [
        'rideCreated' => 'handleRideCreated',
        'rideUpdated' => 'handleRideUpdated',
        'closeModal' => 'closeModal',
        'refreshStaffDashboard' => 'refreshData'
    ];
    
    
    public function confirmDelete($rideId)
    {
        $this->rideToDelete = $rideId;
        $this->modalDetails = "Are you sure you want to delete ride?";
        $this->showModal = true;
    }

    public function mount()
    {
        $this->refreshRides();
    }

    private function refreshRides()
    {
        $this->rides = Rental::with(['ride.classification.rideType'])
            ->whereDate('created_at', Carbon::today())
            ->get();
        $this->totalPrice = Rental::whereDate('created_at', Carbon::today())->sum('computed_total');
    }

//     private function refreshRides()
// {
//     // Get current user
//     $user = auth()->user();

//     // Determine the date to use
//     $targetDate = $user->userType == 0 || 2 ? Carbon::today() : Carbon::tomorrow();

//     // Fetch rides for the target date
//     $this->rides = Rental::whereDate('created_at', $targetDate)->get();
//     $this->totalPrice = Rental::whereDate('created_at', $targetDate)->sum('totalPrice');
// }


    public function updatedRideFilter()
    {
        // This will automatically run when the filter changes
        $this->refreshRides();
    }

    public function getFilteredRidesProperty()
    {
        $currentTime = Carbon::now()->setTimezone('Asia/Manila');
        
        return collect($this->rides)->filter(function($ride) use ($currentTime) {
            return match($this->rideFilter) {
                'ongoing' => $ride->status == Rental::STATUS_ACTIVE,
                'ended' => $ride->status == Rental::STATUS_COMPLETED || ($ride->end_at && $currentTime >= $ride->end_at),
                'all' => true
            };
        })->values();
    }

    public function render()
    {
        return view('livewire.staff-dashboard', [
            'totalPrice' => $this->totalPrice,
            'filteredRides' => $this->getFilteredRidesProperty()
        ]);
    }

    public function deleteRide()
    {
        $rental = Rental::find($this->rideToDelete);
        
        if ($rental) {
            // Get the ride before deleting the rental
            $ride = $rental->ride;
            
            // Delete the rental
            $rental->delete();
            
            // Update ride status back to AVAILABLE
            if ($ride) {
                $ride->update(['is_active' => \App\Models\Ride::STATUS_AVAILABLE]);
            }
        }
        
        $this->refreshRides();
        $this->closeModal();
    }

    public function editRide($rideId)
    {
        $this->editingRideId = $rideId;
        $this->showEditModal = true;
    }

    public function handleRideCreated()
    {
        $this->showAddRides = false;
        // Refresh the data to show the new rental
        $this->refreshData();
    }

    public function handleRideUpdated()
    {
        $this->showEditModal = false;
        $this->editingRideId = null;
        // Add any success message or refresh logic
    }

    public function closeModal()
    {
        $this->showAddRides = false;
        $this->showEditModal = false;
        $this->editingRideId = null;
        $this->showModal = false;
    }

    public function refreshData()
    {
        // Force refresh of the component data by clearing computed properties
        unset($this->totalPrice);
        unset($this->filteredRides);
        
        // Recalculate totals and refresh the view
        $this->refreshRides();
    }

    public function toggleMarkAsDone($rideId)
    {
        if (isset($this->markedAsDone[$rideId])) {
            unset($this->markedAsDone[$rideId]);
        } else {
            $this->markedAsDone[$rideId] = true;
        }
    }
}
