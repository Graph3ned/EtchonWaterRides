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
        'rideUpdated' => 'handleRideUpdated'
    ];
    
    
    public function confirmDelete($rideId)
    {
        $this->rideToDelete = $rideId;
        $this->modalDetails = "Are you sure you want to delete ride?";
        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
    }
    public function mount()
    {
        $this->refreshRides();
    }

    private function refreshRides()
    {

        $this->rides = Rental::whereDate('created_at', Carbon::today())->get();
        $this->totalPrice = Rental::whereDate('created_at', Carbon::today())->sum('totalPrice');
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
        $currentTime = Carbon::now()->setTimezone('Asia/Manila')->format('H:i:s');
        
        return collect($this->rides)->filter(function($ride) use ($currentTime) {
            $timeEnd = Carbon::parse($ride->timeEnd)->format('H:i:s');
            
            return match($this->rideFilter) {
                'ongoing' => $ride->status == '0',
                'ended' => $currentTime >= $timeEnd || $ride->status == '1',
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
        Rental::find($this->rideToDelete)->delete();
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
        // Add any success message or refresh logic
    }

    public function handleRideUpdated()
    {
        $this->showEditModal = false;
        $this->editingRideId = null;
        // Add any success message or refresh logic
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
