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

    public function updatedRideTypeImage()
    {
        if ($this->rideTypeImage) {
            // Validate file type immediately
            $allowedMimes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp', 'image/svg+xml'];
            $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg'];
            
            $fileExtension = strtolower($this->rideTypeImage->getClientOriginalExtension());
            
            if (!in_array($this->rideTypeImage->getMimeType(), $allowedMimes) || !in_array($fileExtension, $allowedExtensions)) {
                $this->rideTypeImage = null;
                session()->flash('error', 'Please select a valid image file (JPEG, JPG, PNG, GIF, WebP, or SVG). DOCX and other document files are not allowed.');
                return;
            }
            
            // Validate file size
            if ($this->rideTypeImage->getSize() > 2048 * 1024) { // 2MB
                $this->rideTypeImage = null;
                session()->flash('error', 'File size must be less than 2MB.');
                return;
            }
        }
    }

    public function saveRideTypeImage()
    {
        $this->validate([
            'rideTypeImage' => 'nullable|image|mimes:jpeg,jpg,png,gif,webp,svg|max:2048',
        ], [
            // 'rideTypeImage.required' => 'Please select an image file.',
            'rideTypeImage.image' => 'The file must be an image.',
            'rideTypeImage.mimes' => 'The image must be a file of type: jpeg, jpg, png, gif, webp, svg.',
            'rideTypeImage.max' => 'The image may not be greater than 2MB.',
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
