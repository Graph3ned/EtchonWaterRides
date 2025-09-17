<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\RideType;
use App\Models\Classification;
use App\Models\Ride;
use App\Models\Rental;
use Carbon\Carbon;

class RideAvailability extends Component
{
    public $selectedRideType = '';
    public $selectedClassification = '';

    public function updatedSelectedRideType()
    {
        $this->selectedClassification = '';
    }

    public function refreshData($showMessage = false)
    {
        // Fix any inconsistent ride statuses
        $this->fixInconsistentRideStatuses();
        
        // Only show success message if explicitly requested (manual refresh)
        if ($showMessage) {
            session()->flash('success', 'Ride availability data refreshed successfully.');
        }
    }

    private function fixInconsistentRideStatuses()
    {
        // Find rides marked as used but have no active rentals
        $inconsistentRides = Ride::where('is_active', Ride::STATUS_USED)
            ->whereDoesntHave('rentals', function($query) {
                $query->where('status', Rental::STATUS_ACTIVE);
            })
            ->get();

        // Update them to available
        foreach ($inconsistentRides as $ride) {
            $ride->update(['is_active' => Ride::STATUS_AVAILABLE]);
        }

        if ($inconsistentRides->count() > 0) {
            session()->flash('success', "Fixed {$inconsistentRides->count()} inconsistent ride statuses.");
        }
    }

    private function formatTimeLeft($minutes)
    {
        if ($minutes <= 0) {
            return 'Overdue';
        }

        $hours = intval($minutes / 60);
        $remainingMinutes = $minutes % 60;

        if ($hours > 0) {
            return $hours . 'h ' . $remainingMinutes . 'm left';
        } else {
            return $remainingMinutes . 'm left';
        }
    }

    public function render()
    {
        // Get ride types for filtering
        $rideTypes = RideType::with(['classifications.rides'])
            ->whereHas('classifications.rides', function($query) {
                $query->whereIn('is_active', [Ride::STATUS_AVAILABLE, Ride::STATUS_USED]);
            })
            ->get();

        // Get classifications for filtering
        $classifications = [];
        if ($this->selectedRideType) {
            $classifications = Classification::where('ride_type_id', $this->selectedRideType)
                ->whereHas('rides', function($query) {
                    $query->whereIn('is_active', [Ride::STATUS_AVAILABLE, Ride::STATUS_USED]);
                })
                ->get();
        }

        // Build rides query
        $ridesQuery = Ride::with(['classification.rideType'])
            ->whereIn('is_active', [Ride::STATUS_AVAILABLE, Ride::STATUS_USED])
            ->whereHas('classification')
            ->whereHas('classification.rideType');

        if ($this->selectedRideType) {
            $ridesQuery->whereHas('classification.rideType', function($q) {
                $q->where('id', $this->selectedRideType);
            });
        }

        if ($this->selectedClassification) {
            $ridesQuery->where('classification_id', $this->selectedClassification);
        }

        $rides = $ridesQuery->get();

        // Add rental info for used rides and verify they actually have active rentals
        foreach ($rides as $ride) {
            if ($ride->is_active === Ride::STATUS_USED) {
                $activeRental = Rental::where('ride_id', $ride->id)
                    ->where('status', Rental::STATUS_ACTIVE)
                    ->first();
                
                if ($activeRental) {
                    $ride->rental_start_time = Carbon::parse($activeRental->start_at)->format('H:i');
                    $ride->rental_duration = $activeRental->duration_minutes;
                    $ride->rental_staff = $activeRental->user_name_at_time;
                    $ride->rental_note = $activeRental->note;
                    
                    // Calculate time left
                    $now = Carbon::now('Asia/Manila');
                    $endTime = Carbon::parse($activeRental->end_at);
                    $timeLeft = $now->diffInMinutes($endTime, false);
                    
                    if ($timeLeft > 0) {
                        $ride->time_left_minutes = $timeLeft;
                        $ride->time_left_formatted = $this->formatTimeLeft($timeLeft);
                        $ride->is_overdue = false;
                    } else {
                        $ride->time_left_minutes = 0;
                        $ride->time_left_formatted = 'Overdue';
                        $ride->is_overdue = true;
                    }
                } else {
                    // If ride is marked as used but has no active rental, fix the status
                    $ride->update(['is_active' => Ride::STATUS_AVAILABLE]);
                    $ride->is_active = Ride::STATUS_AVAILABLE; // Update the current object
                }
            }
        }

        // Filter rides
        $availableRides = $rides->filter(function($ride) {
            return $ride->is_active === Ride::STATUS_AVAILABLE;
        });

        $usedRides = $rides->filter(function($ride) {
            return $ride->is_active === Ride::STATUS_USED;
        })->sortBy(function($ride) {
            // Sort by time left (ascending - least time first)
            // Overdue rides (negative time) will come first
            // Then rides with least time remaining
            return $ride->time_left_minutes ?? 999999;
        });

        // Calculate ride type stats
        $rideTypeStats = [];
        foreach ($rideTypes as $rideType) {
            if (!$rideType || !$rideType->classifications) {
                continue;
            }
            
            $totalRides = $rideType->classifications->sum(function($classification) {
                if (!$classification || !$classification->rides) {
                    return 0;
                }
                return $classification->rides->whereIn('is_active', [Ride::STATUS_AVAILABLE, Ride::STATUS_USED])->count();
            });
            
            $usedRidesCount = $rideType->classifications->sum(function($classification) {
                if (!$classification || !$classification->rides) {
                    return 0;
                }
                return $classification->rides->where('is_active', Ride::STATUS_USED)->count();
            });
            
            $availableRidesCount = $totalRides - $usedRidesCount;
            
            $rideTypeStats[] = [
                'ride_type' => $rideType,
                'total' => $totalRides,
                'used' => $usedRidesCount,
                'available' => $availableRidesCount,
                'usage_percentage' => $totalRides > 0 ? round(($usedRidesCount / $totalRides) * 100, 1) : 0
            ];
        }

        return view('livewire.ride-availability', [
            'rideTypes' => $rideTypes,
            'classifications' => $classifications,
            'availableRides' => $availableRides,
            'usedRides' => $usedRides,
            'rideTypeStats' => $rideTypeStats
        ]);
    }
}
