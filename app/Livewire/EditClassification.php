<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\RideType;
use App\Models\Classification;
use App\Models\Ride;

class EditClassification extends Component
{
    use WithFileUploads;
    public $classificationId;
    public $classification;
    public $rideType;
    
    // Single classification with identifiers (for editing)
    public $name;
    public $price_per_hour;
    public $identifiers = [];
    public $identifierStatus = []; // Track individual identifier status
    public $identifierInDatabase = []; // Track which identifiers are in database
    public $showDeleteModal = false;
    public $identifierToDelete;
    public $showImageModal = false;
    public $classificationImage;

    protected function rules()
    {
        return [
            'name' => [
                'required',
                'string',
                'max:255',
                'unique:classifications,name,' . $this->classificationId . ',id,ride_type_id,' . $this->classification->ride_type_id
            ],
            'price_per_hour' => 'required|numeric|min:0.01|max:999999.99',
            'identifiers' => 'required|array|min:1',
            'identifiers.*' => 'required|string|max:255',
        ];
    }

    protected function messages()
    {
        return [
            'name.required' => 'Classification name is required.',
            'name.max' => 'Classification name must not exceed 255 characters.',
            'name.unique' => 'A classification with this name already exists for this ride type.',
            'price_per_hour.required' => 'Classification price is required.',
            'price_per_hour.numeric' => 'Classification price must be a valid number.',
            'price_per_hour.min' => 'Classification price must be at least 0.01.',
            'price_per_hour.max' => 'Classification price must not exceed 999,999.99.',
            'identifiers.required' => 'Add at least one identifier.',
            'identifiers.*.required' => 'Identifier is required.',
            'identifiers.*.max' => 'Identifier must not exceed 255 characters.',
        ];
    }

    public function mount($classificationId)
    {
        $this->classificationId = $classificationId;
        $this->classification = Classification::with(['rideType'])->findOrFail($classificationId);
        $this->rideType = $this->classification->rideType;
        
        // Load only active (non-deleted) rides for display
        $activeRides = Ride::where('classification_id', $this->classificationId)->get();
        
        // Pre-populate the form with existing data
        $this->name = $this->classification->name;
        $this->price_per_hour = $this->classification->price_per_hour;
        $this->identifiers = $activeRides->pluck('identifier')->toArray();
        
        // Load individual ride statuses and track which are in database
        $this->identifierStatus = [];
        $this->identifierInDatabase = [];
        foreach ($activeRides as $ride) {
            $index = array_search($ride->identifier, $this->identifiers);
            if ($index !== false) {
                $this->identifierStatus[$index] = $ride->is_active;
                $this->identifierInDatabase[$index] = true;
            }
        }
    }

    public function addIdentifier()
    {
        $this->identifiers[] = '';
        $this->identifierStatus[] = true; // Default to active for new identifiers
        $this->identifierInDatabase[] = false; // Mark as not in database
    }

    private function reloadFormData()
    {
        // Reload the classification data
        $this->classification = Classification::with(['rideType', 'rides' => function($query) {
            $query->whereNull('deleted_at');
        }])->findOrFail($this->classification->id);
        
        // Reload active rides
        $activeRides = Ride::where('classification_id', $this->classificationId)->get();
        
        // Update form arrays with fresh data
        $this->identifiers = $activeRides->pluck('identifier')->toArray();
        
        // Update identifier statuses and database tracking
        $this->identifierStatus = [];
        $this->identifierInDatabase = [];
        foreach ($activeRides as $ride) {
            $index = array_search($ride->identifier, $this->identifiers);
            if ($index !== false) {
                $this->identifierStatus[$index] = $ride->is_active;
                $this->identifierInDatabase[$index] = true;
            }
        }
    }

    public function addIdentifierToDatabase($index)
    {
        $identifier = trim($this->identifiers[$index]);
        
        // Check if identifier is empty
        if (empty($identifier)) {
            session()->flash('error', 'Please enter an identifier name.');
            
            // Remove the empty identifier from the form array
            unset($this->identifiers[$index]);
            unset($this->identifierStatus[$index]);
            $this->identifiers = array_values($this->identifiers);
            $this->identifierStatus = array_values($this->identifierStatus);
            
            return;
        }
        
        // Check for existing identifier (including soft-deleted ones)
        $existingIdentifier = Ride::withTrashed()
            ->where('classification_id', $this->classification->id)
            ->where('identifier', $identifier)
            ->first();
            
        if ($existingIdentifier) {
            if ($existingIdentifier->trashed()) {
                // Restore the soft-deleted identifier
                $existingIdentifier->restore();
                
                // Update active status if changed
                if ($existingIdentifier->is_active != ($this->identifierStatus[$index] ?? true)) {
                    $existingIdentifier->update(['is_active' => $this->identifierStatus[$index] ?? true]);
                }
                
                session()->flash('success', "Identifier '{$identifier}' added successfully.");
                
                // Remove the identifier from the form array since it's now in the database
                unset($this->identifiers[$index]);
                unset($this->identifierStatus[$index]);
                $this->identifiers = array_values($this->identifiers);
                $this->identifierStatus = array_values($this->identifierStatus);
                
                // Reload the classification data to show the newly added identifier
                $this->classification = Classification::with(['rideType', 'rides' => function($query) {
                    $query->whereNull('deleted_at');
                }])->findOrFail($this->classification->id);
                
                // Reload form data to show the newly added identifier
                $this->reloadFormData();
            } else {
                // Active identifier already exists - clear it from form to prevent duplicate processing
                session()->flash('error', "Identifier '{$identifier}' already exists. Please choose a different name.");
                
                // Remove the problematic identifier from the form array
                unset($this->identifiers[$index]);
                unset($this->identifierStatus[$index]);
                $this->identifiers = array_values($this->identifiers);
                $this->identifierStatus = array_values($this->identifierStatus);
                
                return;
            }
        } else {
            // Create new identifier
            try {
                Ride::create([
                    'classification_id' => $this->classification->id,
                    'identifier' => $identifier,
                    'is_active' => $this->identifierStatus[$index] ?? true,
                ]);
                
                session()->flash('success', "Identifier '{$identifier}' added successfully.");
                
                // Remove the identifier from the form array since it's now in the database
                unset($this->identifiers[$index]);
                unset($this->identifierStatus[$index]);
                $this->identifiers = array_values($this->identifiers);
                $this->identifierStatus = array_values($this->identifierStatus);
                
                // Reload the classification data to show the newly added identifier
                $this->classification = Classification::with(['rideType', 'rides' => function($query) {
                    $query->whereNull('deleted_at');
                }])->findOrFail($this->classification->id);
                
                // Reload form data to show the newly added identifier
                $this->reloadFormData();
            } catch (\Exception $e) {
                session()->flash('error', 'Error adding identifier: ' . $e->getMessage());
                
                // Remove the problematic identifier from the form array
                unset($this->identifiers[$index]);
                unset($this->identifierStatus[$index]);
                $this->identifiers = array_values($this->identifiers);
                $this->identifierStatus = array_values($this->identifierStatus);
                
                return;
            }
        }
    }

    public function removeIdentifier($index)
    {
        unset($this->identifiers[$index]);
        unset($this->identifierStatus[$index]);
        $this->identifiers = array_values($this->identifiers);
        $this->identifierStatus = array_values($this->identifierStatus);
    }

    public function updatedName()
    {
        $this->validateOnly('name');
    }

    public function updatedPricePerHour()
    {
        $this->validateOnly('price_per_hour');
    }

    public function updatedIdentifiers()
    {
        $this->validateOnly('identifiers');
        
        // Check for duplicate identifiers in real-time
        $newIdentifiers = array_filter(array_map('trim', $this->identifiers));
        $duplicateIdentifiers = array_diff_assoc($newIdentifiers, array_unique($newIdentifiers));
        
        if (!empty($duplicateIdentifiers)) {
            $this->addError('identifiers', 'Duplicate identifiers found: ' . implode(', ', array_unique($duplicateIdentifiers)));
        }
    }

    public function confirmDeleteIdentifier($index)
    {
        $this->identifierToDelete = $index;
        $this->showDeleteModal = true;
    }

    public function closeDeleteModal()
    {
        $this->showDeleteModal = false;
        $this->identifierToDelete = null;
    }

    public function deleteIdentifier()
    {
        if ($this->identifierToDelete !== null) {
            // Check if there's only one non-empty identifier remaining
            $nonEmptyIdentifiers = array_filter($this->identifiers, function($identifier) {
                return !empty(trim($identifier));
            });
            
            if (count($nonEmptyIdentifiers) <= 1) {
                session()->flash('error', 'Cannot delete the last identifier. A classification must have at least one identifier.');
                $this->closeDeleteModal();
                return;
            }
            
            $identifierToDelete = $this->identifiers[$this->identifierToDelete];
            
            // If it's an existing identifier (not empty), soft delete it from database
            if (!empty(trim($identifierToDelete))) {
                $existingRide = Ride::where('classification_id', $this->classification->id)
                    ->where('identifier', $identifierToDelete)
                    ->first();
                    
                if ($existingRide) {
                    // Block deletion if ride is currently USED (is_active = 2)
                    if ($existingRide->is_active === Ride::STATUS_USED) {
                        session()->flash('error', "Cannot delete identifier '{$identifierToDelete}' while it is currently in use.");
                        $this->closeDeleteModal();
                        return;
                    }
                    $existingRide->delete(); // Soft delete
                    session()->flash('success', "Identifier '{$identifierToDelete}' deleted successfully.");
                }
            }
            
            // Remove from form arrays
            unset($this->identifiers[$this->identifierToDelete]);
            unset($this->identifierStatus[$this->identifierToDelete]);
            $this->identifiers = array_values($this->identifiers);
            $this->identifierStatus = array_values($this->identifierStatus);
            
            // If no identifiers left, add an empty one
            if (empty($this->identifiers)) {
                $this->identifiers[] = '';
                $this->identifierStatus[] = true;
            }
        }
        
        $this->closeDeleteModal();
    }

    public function saveClassificationImage()
    {
        try {
            $this->validate([
                'classificationImage' => 'required|image|mimes:jpeg,jpg,png,gif,webp,svg|max:2048',
            ]);

            $path = $this->classificationImage->store('classification-images', 'public');
            $this->classification->update(['image_path' => $path]);
            $this->classificationImage = null;
            $this->showImageModal = false;

            // Refresh classification model for UI
            $this->classification = Classification::find($this->classificationId);
            session()->flash('success', 'Classification image updated successfully.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            session()->flash('error', 'Please select a valid image (max 2MB).');
        } catch (\Exception $e) {
            session()->flash('error', 'Error saving image: ' . $e->getMessage());
        }
    }

    public function toggleIdentifierStatus($index)
    {
        if (isset($this->identifierStatus[$index]) && isset($this->identifierInDatabase[$index]) && $this->identifierInDatabase[$index]) {
            // Toggle the status in the form array
            $this->identifierStatus[$index] = !$this->identifierStatus[$index];
            
            // Find the corresponding ride in the database and update it
            $identifier = trim($this->identifiers[$index]);
            if (!empty($identifier)) {
                $ride = Ride::where('classification_id', $this->classificationId)
                           ->where('identifier', $identifier)
                           ->first();
                
                if ($ride) {
                    // Prevent deactivating if currently USED (2)
                    if ($ride->is_active === Ride::STATUS_USED && $this->identifierStatus[$index] === false) {
                        // Revert toggle
                        $this->identifierStatus[$index] = true;
                        session()->flash('error', "Cannot deactivate identifier '{$identifier}' while it is currently in use.");
                        return;
                    }

                    // Map boolean to status codes: true => AVAILABLE(1), false => INACTIVE(0)
                    $newStatus = $this->identifierStatus[$index] ? Ride::STATUS_AVAILABLE : Ride::STATUS_INACTIVE;
                    $ride->update(['is_active' => $newStatus]);
                    
                    // Show success message
                    $status = $this->identifierStatus[$index] ? 'activated' : 'deactivated';
                    session()->flash('success', "Identifier '{$identifier}' has been {$status}.");
                }
            }
        }
    }

    public function submit()
    {
        try {
            $this->validate();
        } catch (\Illuminate\Validation\ValidationException $e) {
            session()->flash('error', 'Please fix the validation errors below.');
            return;
        }
        
        try {
            // Check for duplicate identifiers within the same classification
            $newIdentifiers = array_filter(array_map('trim', $this->identifiers));
            $duplicateIdentifiers = array_diff_assoc($newIdentifiers, array_unique($newIdentifiers));
            
            if (!empty($duplicateIdentifiers)) {
                session()->flash('error', 'Duplicate identifiers found: ' . implode(', ', array_unique($duplicateIdentifiers)) . '. Each identifier must be unique within this classification.');
                return;
            }
            
            // Update the classification
            $this->classification->update([
                'name' => trim($this->name),
                'price_per_hour' => $this->price_per_hour,
            ]);

            // Get all rides including soft deleted ones
            $allRides = Ride::withTrashed()->where('classification_id', $this->classification->id)->get();
            $existingIdentifiers = $allRides->pluck('identifier')->toArray();
            
            // Safety check: Don't delete all rides if new identifiers is empty
            if (empty($newIdentifiers)) {
                session()->flash('error', 'At least one identifier is required.');
                return;
            }
            
            $createdRides = 0;
            $restoredRides = 0;
            $softDeletedRides = 0;
            $errors = [];
            
            // Process each new identifier
            foreach ($newIdentifiers as $index => $identifier) {
                try {
                    $existingRide = $allRides->where('identifier', $identifier)->first();
                    $isActive = $this->identifierStatus[$index] ?? true;
                    
                    if ($existingRide) {
                        // Ride exists, restore if soft deleted and update status
                        if ($existingRide->trashed()) {
                            $existingRide->restore();
                            $restoredRides++;
                        }
                        
                        // Update active status if changed
                        if ($existingRide->is_active != $isActive) {
                            $existingRide->update(['is_active' => $isActive]);
                        }
                    } else {
                        // Create new ride
                        Ride::create([
                            'classification_id' => $this->classification->id,
                            'identifier' => $identifier,
                            'is_active' => $isActive,
                        ]);
                        $createdRides++;
                    }
                } catch (\Illuminate\Database\QueryException $e) {
                    if ($e->getCode() == 23000) { // Integrity constraint violation
                        $errors[] = "Identifier '{$identifier}' already exists in this classification.";
                    } else {
                        $errors[] = "Error processing identifier '{$identifier}': " . $e->getMessage();
                    }
                } catch (\Exception $e) {
                    $errors[] = "Error processing identifier '{$identifier}': " . $e->getMessage();
                }
            }
            
            // If there were errors processing identifiers, show them and return
            if (!empty($errors)) {
                session()->flash('error', implode(' ', $errors));
                return;
            }
            
            // Soft delete rides that are no longer in the identifiers list
            foreach ($allRides as $ride) {
                if (!in_array($ride->identifier, $newIdentifiers) && !$ride->trashed()) {
                    try {
                        $ride->delete();
                        $softDeletedRides++;
                    } catch (\Exception $e) {
                        $errors[] = "Error removing identifier '{$ride->identifier}': " . $e->getMessage();
                    }
                }
            }

            // If there were errors during deletion, show them
            if (!empty($errors)) {
                session()->flash('error', implode(' ', $errors));
                return;
            }

            $message = 'Classification updated successfully.';
            if ($createdRides > 0) $message .= " Added {$createdRides} ride(s).";
            if ($restoredRides > 0) $message .= " Restored {$restoredRides} ride(s).";
            if ($softDeletedRides > 0) $message .= " Removed {$softDeletedRides} ride(s).";
            
            session()->flash('success', $message);
            return redirect()->route('ViewDetails', ['rideTypeId' => $this->rideType->id]);

        } catch (\Illuminate\Database\QueryException $e) {
            if ($e->getCode() == 23000) { // Integrity constraint violation
                if (str_contains($e->getMessage(), 'classifications_name_ride_type_id_unique')) {
                    session()->flash('error', 'A classification with this name already exists for this ride type.');
                } elseif (str_contains($e->getMessage(), 'rides_classification_id_identifier_unique')) {
                    session()->flash('error', 'One or more identifiers already exist in this classification.');
                } else {
                    session()->flash('error', 'Database constraint violation. Please check your data and try again.');
                }
            } else {
                session()->flash('error', 'Database error: ' . $e->getMessage());
            }
        } catch (\Exception $e) {
            session()->flash('error', 'An unexpected error occurred: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.edit-classification');
    }
}
