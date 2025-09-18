<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\RideType;
use App\Models\Classification;
use App\Models\Ride;

class AddRideClassification extends Component
{
    public $rideTypeId;
    public $rideType;
    
    // Multiple classifications with identifiers
    public $classificationsInput = [
        [
            'name' => '',
            'price_per_hour' => '',
            'identifiers' => [''],
        ],
    ];
    public $isActive = true;

    protected function rules()
    {
        return [
            'classificationsInput' => 'required|array|min:1',
            // 'classificationsInput.*.name' => 'required|string|max:255|unique:classifications,name,NULL,id,ride_type_id,' . $this->rideTypeId . ',deleted_at,NULL',
            'classificationsInput.*.name' => [
                'required',
                'string',
                'max:255',
                'unique:classifications,name,NULL,id,ride_type_id,' . $this->rideTypeId . ',deleted_at,NULL',
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
            'classificationsInput.*.identifiers.*' => [
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
                        return strtolower(trim($identifier)) === strtolower(trim($value));
                    });
                    
                    if (count($duplicates) > 1) {
                        $fail('Duplicate identifiers are not allowed within the same classification.');
                    }
                }
            ],
            'isActive' => 'boolean',
        ];
    }

    protected $messages = [
        'classificationsInput.required' => 'Add at least one classification.',
        'classificationsInput.*.name.required' => 'Classification name is required.',
        'classificationsInput.*.name.unique' => 'This classification name already exists for this ride type.',
        'classificationsInput.*.price_per_hour.required' => 'Classification price is required.',
        'classificationsInput.*.identifiers.required' => 'Add at least one identifier per classification.',
        'classificationsInput.*.identifiers.*.required' => 'Identifier is required.',
    ];

    public function mount($rideTypeId)
    {
        $this->rideTypeId = $rideTypeId;
        $this->rideType = RideType::findOrFail($rideTypeId);
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
            $createdRides = 0;
            $restoredRides = 0;
            $restoredClassifications = 0;
            $createdClassifications = 0;

            // Load all classifications and rides for this ride type (including soft-deleted)
            $allClassifications = Classification::withTrashed()
                ->where('ride_type_id', $this->rideType->id)
                ->get();
            $allRides = Ride::withTrashed()
                ->whereIn('classification_id', $allClassifications->pluck('id'))
                ->get();

            // Process each classification
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
                        'ride_type_id' => $this->rideType->id,
                        'name' => $classificationName,
                        'price_per_hour' => $c['price_per_hour'],
                    ]);
                    $createdClassifications++;
                }

                // Process identifiers for this classification
                $newIdentifiers = array_filter(array_map('trim', $c['identifiers']));
                $classificationRides = $allRides->where('classification_id', $classification->id);
                $existingIdentifiers = $classificationRides->pluck('identifier')->toArray();
                
                // Soft delete rides that are no longer in the form
                foreach ($classificationRides as $existingRide) {
                    if (!in_array($existingRide->identifier, $newIdentifiers)) {
                        $existingRide->delete();
                    }
                }
                
                // Create or restore rides
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
            }

            // Build success message
            $totalClassifications = $restoredClassifications + $createdClassifications;
            $totalRides = $createdRides + $restoredRides;
            
            $message = "Successfully added {$totalClassifications} classification(s)";
            if ($totalRides > 0) {
                $message .= " with {$totalRides} ride(s)";
            }
            $message .= ".";
            
            session()->flash('success', $message);
            return redirect()->route('ViewDetails', ['rideTypeId' => $this->rideType->id]);

        } catch (\Exception $e) {
            session()->flash('error', 'Error creating classifications: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.add-ride-classification');
    }
}
