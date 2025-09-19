<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\RideType;
use App\Models\Classification;
use App\Models\Ride;
use App\Models\Rental;
use Carbon\Carbon;

class GuestRideAvailability extends Component
{
    public $selectedRideType = '';
    public $selectedClassification = '';

    public function updatedSelectedRideType()
    {
        $this->selectedClassification = '';
    }

    public function clearFilters()
    {
        $this->selectedRideType = '';
        $this->selectedClassification = '';
    }

    private function formatTimeLeft(int $minutes): string
    {
        if ($minutes <= 0) {
            return 'Overdue';
        }

        $hours = intval($minutes / 60);
        $remainingMinutes = $minutes % 60;

        return $hours > 0
            ? $hours . 'h ' . $remainingMinutes . 'm left'
            : $remainingMinutes . 'm left';
    }

    public function render()
    {
        $rideTypes = RideType::with(['classifications.rides'])
            ->whereHas('classifications.rides', function($query) {
                $query->whereIn('is_active', [Ride::STATUS_AVAILABLE, Ride::STATUS_USED]);
            })
            ->get();

        $classifications = [];
        if ($this->selectedRideType) {
            $classifications = Classification::where('ride_type_id', $this->selectedRideType)
                ->whereHas('rides', function($query) {
                    $query->whereIn('is_active', [Ride::STATUS_AVAILABLE, Ride::STATUS_USED]);
                })
                ->get();
        }

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

        foreach ($rides as $ride) {
            if ($ride->is_active === Ride::STATUS_USED) {
                $activeRental = Rental::where('ride_id', $ride->id)
                    ->where('status', Rental::STATUS_ACTIVE)
                    ->orderByDesc('start_at')
                    ->first();

                if ($activeRental) {
                    $ride->rental_start_time = Carbon::parse($activeRental->start_at)->format('H:i');
                    $ride->rental_duration = $activeRental->duration_minutes;

                    $now = Carbon::now('Asia/Manila');
                    $startTime = Carbon::parse($activeRental->start_at, 'Asia/Manila');
                    $endTime = $startTime->copy()->addMinutes((int)($activeRental->duration_minutes ?? 0));
                    $timeLeft = $now->diffInMinutes($endTime, false);

                    if ($timeLeft > 0) {
                        $ride->time_left_minutes = $timeLeft;
                        $ride->time_left_formatted = $this->formatTimeLeft($timeLeft);
                        $ride->is_overdue = false;
                    } else {
                        $overdueMinutes = abs($timeLeft);
                        $hours = intval($overdueMinutes / 60);
                        $remainingMinutes = $overdueMinutes % 60;
                        $formatted = ($hours > 0 ? ($hours . 'h ') : '') . $remainingMinutes . 'm';
                        $ride->time_left_minutes = 0;
                        $ride->time_left_formatted = 'Overdue by ' . $formatted;
                        $ride->is_overdue = true;
                    }
                }
            }
        }

        $availableRides = $rides->filter(fn($r) => $r->is_active === Ride::STATUS_AVAILABLE);
        $usedRides = $rides->filter(fn($r) => $r->is_active === Ride::STATUS_USED)
            ->sortBy(fn($r) => $r->time_left_minutes ?? 999999);

        return view('livewire.guest-ride-availability', [
            'rideTypes' => $rideTypes,
            'classifications' => $classifications,
            'availableRides' => $availableRides,
            'usedRides' => $usedRides,
        ]);
    }
}


