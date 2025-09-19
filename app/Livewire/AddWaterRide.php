<?php
namespace App\Livewire;

use Livewire\Component;
use App\Models\RideType;
use App\Models\Classification;
use App\Models\Ride;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;

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
            'identifiers' => [['name' => '', 'image' => null]],
            'image' => null,
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
            
            'classificationsInput.*.name' => [
                'required',
                'string',
                'max:255',
                function ($attribute, $value, $fail) {
                    // Check for duplicates within the same form submission
                    $duplicates = array_filter($this->classificationsInput, function($classification) use ($value) {
                        return strtolower(trim($classification['name'])) === strtolower(trim($value));
                    });
                    
                    if (count($duplicates) > 1) {
                        $fail('Duplicate classification names are not allowed.');
                    }
                } 
            ],
            'classificationsInput.*.price_per_hour' => 'required|numeric|min:0.01|max:999999.99',
            'classificationsInput.*.identifiers' => 'required|array|min:1',
            'classificationsInput.*.identifiers.*.name' => [
                'required',
                'string',
                'max:255',
                function ($attribute, $value, $fail) {
                    // Get the classification index from the attribute path
                    $pathParts = explode('.', $attribute);
                    $classificationIndex = $pathParts[1];
                    
                    // Check for duplicates within the same classification
                    $identifiers = $this->classificationsInput[$classificationIndex]['identifiers'];
                    $duplicates = array_filter($identifiers, function($identifier) use ($value) {
                        return strtolower(trim($identifier['name'])) === strtolower(trim($value));
                    });
                    
                    if (count($duplicates) > 1) {
                        $fail('Duplicate identifiers are not allowed within the same classification.');
                    }
                }
            ],
            'classificationsInput.*.identifiers.*.image' => 'nullable|image|mimes:jpeg,jpg,png,gif,webp,svg|max:2048',
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
        'classificationsInput.*.name.duplicate' => 'Duplicate classification names are not allowed.',
        'classificationsInput.*.price_per_hour.required' => 'Classification price is required.',
        'classificationsInput.*.identifiers.required' => 'Add at least one identifier per classification.',
        'classificationsInput.*.identifiers.*.name.required' => 'Identifier is required.',
        'classificationsInput.*.identifiers.*.name.duplicate' => 'Duplicate identifiers are not allowed within the same classification.',
        'classificationsInput.*.identifiers.*.image.image' => 'The file must be an image.',
        'classificationsInput.*.identifiers.*.image.mimes' => 'The image must be a file of type: jpeg, jpg, png, gif, webp, svg.',
        'classificationsInput.*.identifiers.*.image.max' => 'The image may not be greater than 2MB.',
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

    /**
     * Generate a unique image name based on the ride type name
     */
    private function generateRideTypeImageName($rideTypeName, $extension)
    {
        $cleanName = strtolower(preg_replace('/[^a-zA-Z0-9]/', '_', $rideTypeName));
        return $cleanName . '_' . time() . '.' . $extension;
    }

    /**
     * Generate a unique image name based on the identifier name
     */
    private function generateIdentifierImageName($identifierName, $extension)
    {
        $cleanName = strtolower(preg_replace('/[^a-zA-Z0-9]/', '_', $identifierName));
        return $cleanName . '_' . time() . '.' . $extension;
    }

    /**
     * Store image with custom naming
     */
    private function storeImageWithCustomName($file, $baseName, $directory = 'public')
    {
        $extension = $file->getClientOriginalExtension();
        $customName = $this->generateIdentifierImageName($baseName, $extension);
        return $file->storeAs($directory, $customName, 'public');
    }

    /**
     * Store ride type image with custom naming and handle overwrite
     */
    private function storeRideTypeImageWithOverwrite($file, $rideTypeName, $existingImagePath = null)
    {
        $extension = $file->getClientOriginalExtension();
        $customName = $this->generateRideTypeImageName($rideTypeName, $extension);
        
        // If there's an existing image, delete it first
        if ($existingImagePath && Storage::disk('public')->exists($existingImagePath)) {
            Storage::disk('public')->delete($existingImagePath);
        }
        
        return $file->storeAs('ride-types', $customName, 'public');
    }

    public function resetClassificationFields()
    {
        $this->classificationsInput = [
            [
                'name' => '',
                'price_per_hour' => '',
                'identifiers' => [['name' => '', 'image' => null]],
                'image' => null,
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
            'classificationsInput.*.identifiers.*.name' => 'required|string|max:255',
        ]);
    }

    public function addClassification()
    {
        $this->classificationsInput[] = [
            'name' => '',
            'price_per_hour' => '',
            'identifiers' => [['name' => '', 'image' => null]],
            'image' => null,
        ];
    }

    public function removeClassification($index)
    {
        unset($this->classificationsInput[$index]);
        $this->classificationsInput = array_values($this->classificationsInput);
    }

    public function addIdentifier($classificationIndex)
    {
        $this->classificationsInput[$classificationIndex]['identifiers'][] = ['name' => '', 'image' => null];
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
                    $existingImagePath = $rideType->image_path;
                    $imagePath = $this->storeRideTypeImageWithOverwrite($this->rideTypeImage, $this->newRideTypeName, $existingImagePath);
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
                        $updateData = [];
                        if ($existingClassification->price_per_hour != $c['price_per_hour']) {
                            $updateData['price_per_hour'] = $c['price_per_hour'];
                        }
                        if (!empty($updateData)) {
                            $existingClassification->update($updateData);
                        }
                        
                        $classification = $existingClassification;
                    } else {
                        // Create new classification
                        $createData = [
                            'ride_type_id' => $rideType->id,
                            'name' => $classificationName,
                            'price_per_hour' => $c['price_per_hour'],
                        ];
                        $classification = Classification::create($createData);
                        $createdClassifications++;
                    }
                    
                    // Process identifiers for this classification
                    $newIdentifiers = array_filter($c['identifiers'], function($identifier) {
                        return !empty(trim($identifier['name']));
                    });
                    $classificationRides = $allRides->where('classification_id', $classification->id);
                    $existingIdentifiers = $classificationRides->pluck('identifier')->toArray();
                    
                    // Process each identifier
                    foreach ($newIdentifiers as $identifierData) {
                        $identifier = trim($identifierData['name']);
                        $existingRide = $classificationRides->where('identifier', $identifier)->first();
                        
                        $rideData = [
                            'classification_id' => $classification->id,
                            'identifier' => $identifier,
                            'is_active' => $this->isActive,
                        ];
                        
                        // Handle image upload
                        if (!empty($identifierData['image'])) {
                            $imagePath = $this->storeImageWithCustomName($identifierData['image'], $identifier, 'ride-images');
                            $rideData['image_path'] = $imagePath;
                        }
                        
                        if ($existingRide) {
                            // Ride exists, restore if soft deleted and update status
                            if ($existingRide->trashed()) {
                                $existingRide->restore();
                                $restoredRides++;
                            }
                            
                            // Update data if changed
                            $updateData = [];
                            if ($existingRide->is_active != $this->isActive) {
                                $updateData['is_active'] = $this->isActive;
                            }
                            if (isset($rideData['image_path'])) {
                                $updateData['image_path'] = $rideData['image_path'];
                            }
                            if (!empty($updateData)) {
                                $existingRide->update($updateData);
                            }
                        } else {
                            // Create new ride
                            Ride::create($rideData);
                            $createdRides++;
                        }
                    }
                    
                    // Soft delete rides that are no longer in the identifiers list
                    $newIdentifierNames = array_map(function($identifier) {
                        return trim($identifier['name']);
                    }, $newIdentifiers);
                    
                    foreach ($classificationRides as $ride) {
                        if (!in_array($ride->identifier, $newIdentifierNames) && !$ride->trashed()) {
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
                    $imagePath = $this->storeRideTypeImageWithOverwrite($this->rideTypeImage, $this->newRideTypeName);
                }
                $rideType = RideType::create(['name' => $this->newRideTypeName, 'image_path' => $imagePath]);
                
                $createdRides = 0;
                
                // Create classifications and their rides
                foreach ($this->classificationsInput as $c) {
                    $createData = [
                        'ride_type_id' => $rideType->id,
                        'name' => trim($c['name']),
                        'price_per_hour' => $c['price_per_hour'],
                    ];
                    $classification = Classification::create($createData);

                    foreach ($c['identifiers'] as $identifierData) {
                        if (!empty(trim($identifierData['name']))) {
                            $rideData = [
                                'classification_id' => $classification->id,
                                'identifier' => trim($identifierData['name']),
                                'is_active' => $this->isActive,
                            ];
                            
                            // Handle image upload
                            if (!empty($identifierData['image'])) {
                                $imagePath = $this->storeImageWithCustomName($identifierData['image'], trim($identifierData['name']), 'ride-images');
                                $rideData['image_path'] = $imagePath;
                            }
                            
                            Ride::create($rideData);
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
