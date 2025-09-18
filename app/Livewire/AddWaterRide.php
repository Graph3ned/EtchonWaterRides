<?php
namespace App\Livewire;

use Livewire\Component;
use App\Models\RideType;
use App\Models\Classification;
use App\Models\Ride;
use Livewire\WithFileUploads;

class AddWaterRide extends Component
{
    use WithFileUploads;
    // Step 1: Ride Type Creation (only new)
    public $newRideTypeName = '';
    public $rideTypeImage;
    
    // Step 2: Multiple classifications with identifiers
    public $classificationsInput = [
        [
            'name' => '',
            'price_per_hour' => '',
            'identifiers' => [''],
        ],
    ];
    public $isActive = true;
    
    // UI State
    public $currentStep = 1;
    public $maxSteps = 2;

    protected function rules()
    {
        return [
            'newRideTypeName' => [
                'required',
                'string',
                'max:255',
                function ($attribute, $value, $fail) {
                    $activeRideType = RideType::where('name', $value)->first();
                    if ($activeRideType) {
                        $fail('This ride type already exists.');
                    }
                }
            ],
            'rideTypeImage' => 'nullable|image|mimes:jpeg,jpg,png,gif,webp,svg|max:2048',
            'classificationsInput' => 'required|array|min:1',
            'classificationsInput.*.name' => 'required|string|max:255',
            'classificationsInput.*.price_per_hour' => 'required|numeric|min:0.01|max:999999.99',
            'classificationsInput.*.identifiers' => 'required|array|min:1',
            'classificationsInput.*.identifiers.*' => 'required|string|max:255',
            'isActive' => 'boolean',
            
        ];
    }

    protected $messages = [
        'newRideTypeName.required' => 'Ride type name is required.',
        'newRideTypeName.unique' => 'This ride type already exists.',
        'rideTypeImage.image' => 'The file must be an image.',
        'rideTypeImage.mimes' => 'The image must be a file of type: jpeg, jpg, png, gif, webp, svg.',
        'rideTypeImage.max' => 'The image may not be greater than 2MB.',
        'classificationsInput.required' => 'Add at least one classification.',
        'classificationsInput.*.name.required' => 'Classification name is required.',
        'classificationsInput.*.price_per_hour.required' => 'Classification price is required.',
        'classificationsInput.*.identifiers.required' => 'Add at least one identifier per classification.',
        'classificationsInput.*.identifiers.*.required' => 'Identifier is required.',
    ];

    public function mount()
    {
        $this->loadRideTypes();
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

    public function loadRideTypes() {}

    public function resetClassificationFields()
    {
        $this->classificationsInput = [
            [
                'name' => '',
                'price_per_hour' => '',
                'identifiers' => [''],
            ],
        ];
        $this->isActive = true;
    }

    public function nextStep()
    {
        if ($this->currentStep === 1) {
            $this->validateStep1();
        } elseif ($this->currentStep === 2) {
            $this->validateStep2();
        }
        
        if ($this->currentStep < $this->maxSteps) {
            $this->currentStep++;
        }
    }

    public function previousStep()
    {
        if ($this->currentStep > 1) {
            $this->currentStep--;
        }
    }

    protected function validateStep1()
    {
        $this->validate([
            'newRideTypeName' => [
                'required',
                'string',
                'max:255',
                function ($attribute, $value, $fail) {
                    $activeRideType = RideType::where('name', $value)->first();
                    if ($activeRideType) {
                        $fail('This ride type already exists.');
                    }
                }
            ],
            'rideTypeImage' => 'nullable|image|max:2048',
        ]);
    }

    protected function validateStep2()
    {
        $this->validate([
            'classificationsInput' => 'required|array|min:1',
            'classificationsInput.*.name' => 'required|string|max:255',
            'classificationsInput.*.price_per_hour' => 'required|numeric|min:0.01|max:999999.99',
            'classificationsInput.*.identifiers' => 'required|array|min:1',
            'classificationsInput.*.identifiers.*' => 'required|string|max:255',
        ]);
    }

    public function addClassification()
    {
        $this->classificationsInput[] = [
            'name' => '',
            'price_per_hour' => '',
            'identifiers' => [''],
        ];
    }

    public function removeClassification($index)
    {
        unset($this->classificationsInput[$index]);
        $this->classificationsInput = array_values($this->classificationsInput);
    }

    public function addIdentifier($classificationIndex)
    {
        $this->classificationsInput[$classificationIndex]['identifiers'][] = '';
    }

    public function removeIdentifier($classificationIndex, $identifierIndex)
    {
        unset($this->classificationsInput[$classificationIndex]['identifiers'][$identifierIndex]);
        $this->classificationsInput[$classificationIndex]['identifiers'] = array_values($this->classificationsInput[$classificationIndex]['identifiers']);
    }

    public function submit()
    {
        $this->validate();
        
        try {
            // Step 1: Check for soft deleted ride type with same name and restore it
            $existingRideType = RideType::withTrashed()->where('name', $this->newRideTypeName)->first();
            
            if ($existingRideType && $existingRideType->trashed()) {
                // Restore the soft deleted ride type
                $existingRideType->restore();
                $rideType = $existingRideType;
                // Optionally update image if a new one is uploaded
                if ($this->rideTypeImage) {
                    $imagePath = $this->rideTypeImage->store('ride-types', 'public');
                    $rideType->update(['image_path' => $imagePath]);
                }
                
                // Get all existing classifications and rides (including soft deleted)
                $allClassifications = Classification::withTrashed()->where('ride_type_id', $rideType->id)->get();
                $allRides = Ride::withTrashed()->whereIn('classification_id', $allClassifications->pluck('id'))->get();
                
                $restoredClassifications = 0;
                $restoredRides = 0;
                $createdClassifications = 0;
                $createdRides = 0;
                $softDeletedClassifications = 0;
                $softDeletedRides = 0;
                
                // Process each classification from the form
                foreach ($this->classificationsInput as $c) {
                    $classificationName = trim($c['name']);
                    $existingClassification = $allClassifications->where('name', $classificationName)->first();
                    
                    if ($existingClassification) {
                        // Classification exists, restore if soft deleted and update
                        if ($existingClassification->trashed()) {
                            $existingClassification->restore();
                            $restoredClassifications++;
                        }
                        
                        // Update price if changed
                        if ($existingClassification->price_per_hour != $c['price_per_hour']) {
                            $existingClassification->update(['price_per_hour' => $c['price_per_hour']]);
                        }
                        
                        $classification = $existingClassification;
                    } else {
                        // Create new classification
                        $classification = Classification::create([
                            'ride_type_id' => $rideType->id,
                            'name' => $classificationName,
                            'price_per_hour' => $c['price_per_hour'],
                        ]);
                        $createdClassifications++;
                    }
                    
                    // Process identifiers for this classification
                    $newIdentifiers = array_filter(array_map('trim', $c['identifiers']));
                    $classificationRides = $allRides->where('classification_id', $classification->id);
                    $existingIdentifiers = $classificationRides->pluck('identifier')->toArray();
                    
                    // Process each identifier
                    foreach ($newIdentifiers as $identifier) {
                        $existingRide = $classificationRides->where('identifier', $identifier)->first();
                        
                        if ($existingRide) {
                            // Ride exists, restore if soft deleted and update status
                            if ($existingRide->trashed()) {
                                $existingRide->restore();
                                $restoredRides++;
                            }
                            
                            // Update active status if changed
                            if ($existingRide->is_active != $this->isActive) {
                                $existingRide->update(['is_active' => $this->isActive]);
                            }
                        } else {
                            // Create new ride
                            Ride::create([
                                'classification_id' => $classification->id,
                                'identifier' => $identifier,
                                'is_active' => $this->isActive,
                            ]);
                            $createdRides++;
                        }
                    }
                    
                    // Soft delete rides that are no longer in the identifiers list
                    foreach ($classificationRides as $ride) {
                        if (!in_array($ride->identifier, $newIdentifiers) && !$ride->trashed()) {
                            $ride->delete();
                            $softDeletedRides++;
                        }
                    }
                }
                
                // Soft delete classifications that are no longer in the form
                $formClassificationNames = array_map(function($c) { return trim($c['name']); }, $this->classificationsInput);
                foreach ($allClassifications as $classification) {
                    if (!in_array($classification->name, $formClassificationNames) && !$classification->trashed()) {
                        $classification->delete();
                        $softDeletedClassifications++;
                    }
                }
                
                $totalAdded = $restoredClassifications + $createdClassifications + $restoredRides + $createdRides;
                $totalRemoved = $softDeletedClassifications + $softDeletedRides;
                
                $message = 'Successfully added ride type "' . $this->newRideTypeName . '"';
                if ($totalAdded > 0) $message .= " with {$totalAdded} item(s)";
                if ($totalRemoved > 0) $message .= " (replaced {$totalRemoved} old item(s))";
                $message .= ".";
                
                session()->flash('success', $message);
            } else {
                // Create new ride type
                $imagePath = null;
                if ($this->rideTypeImage) {
                    $imagePath = $this->rideTypeImage->store('ride-types', 'public');
                }
                $rideType = RideType::create(['name' => $this->newRideTypeName, 'image_path' => $imagePath]);
                
                $createdRides = 0;
                
                // Create classifications and their rides
                foreach ($this->classificationsInput as $c) {
                    $classification = Classification::create([
                        'ride_type_id' => $rideType->id,
                        'name' => trim($c['name']),
                        'price_per_hour' => $c['price_per_hour'],
                    ]);

                    foreach ($c['identifiers'] as $identifier) {
                        if (!empty(trim($identifier))) {
                            Ride::create([
                                'classification_id' => $classification->id,
                                'identifier' => trim($identifier),
                                'is_active' => $this->isActive,
                            ]);
                            $createdRides++;
                        }
                    }
                }
                
                session()->flash('success', 'Successfully added ride type "' . $this->newRideTypeName . '" with ' . count($this->classificationsInput) . ' classification(s) and ' . $createdRides . ' ride(s).');
            }
            
            return redirect()->route('RidesRate');

        } catch (\Exception $e) {
            session()->flash('error', 'Error creating rides: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.add-water-ride');
    }
}
