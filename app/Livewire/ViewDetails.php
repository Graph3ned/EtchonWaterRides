<?php
namespace App\Livewire;

use Livewire\Component;
use App\Models\RideType;
use App\Models\Classification;
use App\Models\Ride;
use Livewire\WithFileUploads;

class ViewDetails extends Component
{
    use WithFileUploads;
    public $rideTypeId;
    public $rideType;
    public $classifications = [];
    public $classificationShowModal = false;
    public $classificationModalDetails;
    public $classificationToDelete;
    public $alertMessage = null; 
    public $showModal = false;
    public $modalDetails;
    public $rideTypeToDelete;
    public $rideTypeImage;
    public $showImageModal = false;
    
    public function classificationConfirmDelete($id)
    {
        $this->classificationToDelete = $id;
        $this->classificationModalDetails = "Are you sure you want to delete this classification?";
        $this->classificationShowModal = true;
    }

    public function classificationCloseModal()
    {
        $this->classificationShowModal = false;
    }
    public function confirmDelete($rideTypeId)
    {
        $this->rideTypeToDelete = $rideTypeId;
        $this->modalDetails = "Are you sure you want to delete this ride type? You will also delete all of its classifications and rides.";
        $this->showModal = true;
    }
    
    public function closeModal()
    {
        $this->showModal = false;
    }
    
    public function delete()
    {
        // Soft delete the ride type (this will cascade soft delete classifications and rides)
        $rideType = RideType::findOrFail($this->rideTypeToDelete);
        $rideTypeName = $rideType->name;
        $classificationCount = $rideType->classifications()->count();
        $rideCount = $rideType->classifications()->withCount('rides')->get()->sum('rides_count');
        
        $rideType->delete();

        // Show success message
        session()->flash('success', "Successfully deleted ride type '{$rideTypeName}' with {$classificationCount} classification(s) and {$rideCount} ride(s).");

        // Reload the page to reflect the changes
        return redirect()->route('RidesRate');
    }
    
    public function mount($rideTypeId)
    {
        $this->rideTypeId = $rideTypeId;
        
        // Load ride type with its classifications and rides
        $this->rideType = RideType::with(['classifications.rides'])->findOrFail($rideTypeId);
        $this->classifications = $this->rideType->classifications;
        $this->rideTypeImage = $this->rideType->image_path;
    }

    public function saveRideTypeImage()
    {
        $this->validate([
            'rideTypeImage' => 'required|image|max:2048',
        ]);

        $path = $this->rideTypeImage->store('ride-types', 'public');
        $this->rideType->update(['image_path' => $path]);

        $this->rideTypeImage = null;
        $this->rideType = $this->rideType->fresh();
        $this->showImageModal = false;
        session()->flash('success', 'Ride type picture updated.');
    }

    public function render()
    {
        return view('livewire.view-details'); // This will render your Livewire view
    }

    public function deleteClassification()
    {
        // Soft delete the classification (this will cascade soft delete rides)
        $classification = Classification::findOrFail($this->classificationToDelete);
        $classificationName = $classification->name;
        $rideCount = $classification->rides()->count();
        
        $classification->delete();

        // Show success message
        session()->flash('success', "Successfully deleted classification '{$classificationName}' with {$rideCount} ride(s).");

        // After deleting, close the modal and refresh the data
        $this->classificationShowModal = false;

        // Refresh classifications list to reflect the deleted record
        $this->classifications = $this->rideType->fresh()->classifications;
    }
}
