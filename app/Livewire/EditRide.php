<?php

namespace App\Livewire;

use App\Models\Rental;
use Livewire\Component;
use App\Models\prices;
use Carbon\Carbon;

class EditRide extends Component
{
    public $rideId;
    public $rideType = '';
    public $classification = '';
    public $life_jacket_usage = 0;
    public $pricePerHour = 0;   
    public $duration;
    public $timeStart;
    public $timeEnd;
    public $showCustomDuration = false;
    public $customDuration;
    public $extendDuration = 0; 
    public $totalPrice;
    public $prices = [];
    public $note;
    public $status = '1';

    public function mount($rideId)
    {
        $this->prices = prices::all()
            ->groupBy('ride_type')
            ->map(function ($group) {
                return $group->pluck('price_per_hour', 'classification')->toArray();
            })
            ->toArray();

        $this->rideId = $rideId;
        $this->loadRide();
    }

    public function updated($propertyName)
    {
        if ($propertyName === 'rideType') {
            $this->classification = array_key_first($this->prices[$this->rideType]) ?? '';
        }

        if (in_array($propertyName, ['rideType', 'classification', 'extendDuration', 'showCustomDuration', 'customDuration'])) {
            $this->updateTimeEnd();
            $this->updateTotalPrice();
        }
    }

    private function loadRide()
    {
        $ride = Rental::findOrFail($this->rideId);

        $this->rideType = $ride->rideType;
        $this->classification = $ride->classification;
        $this->life_jacket_usage = $ride->life_jacket_usage;
        $this->pricePerHour = $ride->pricePerHour;
        $this->duration = $ride->duration;
        $this->timeStart = $ride->timeStart;
        $this->timeEnd = $ride->timeEnd;
        $this->totalPrice = $ride->totalPrice;
        $this->note = $ride->note;
    }

    private function updateTimeEnd()
    {
        $startTime = Carbon::createFromFormat('H:i:s', $this->timeStart);
        $extensionMinutes = $this->showCustomDuration ? (int)$this->customDuration : (int)$this->extendDuration;
        $newEndTime = $startTime->copy()->addMinutes($this->duration + $extensionMinutes);
        $this->timeEnd = $newEndTime->format('H:i:s');
    }

    private function updateTotalPrice()
    {
        $extensionMinutes = $this->showCustomDuration ? (int)$this->customDuration : (int)$this->extendDuration;
        $totalMinutes = $this->duration + $extensionMinutes;
        $pricePerMinute = $this->pricePerHour / 60;
        $this->totalPrice = (int)round($pricePerMinute * $totalMinutes);
    }


    public function updateRides()
    {
        $ride = Rental::findOrFail($this->rideId);

        $extensionMinutes = $this->showCustomDuration ? (int)$this->customDuration : (int)$this->extendDuration;
        $newDuration = $this->duration + $extensionMinutes;

        $ride->rideType = $this->rideType;
        $ride->classification = $this->classification;
        $ride->life_jacket_usage = $this->life_jacket_usage;
        $ride->duration = $newDuration;
        $ride->timeEnd = $this->timeEnd;
        $ride->totalPrice = $this->totalPrice;
        $ride->note = $this->note;
        $ride->save();

        session()->flash('message', 'Ride updated successfully!');
        return redirect()->route('dashboard');
    }

    public function updateStatus()
    {
        $ride = Rental::findOrFail($this->rideId);

        $ride->status = $this->status;
        $ride->save();

        session()->flash('message', 'Ride updated successfully!');
        return redirect()->route('dashboard');
    }

    public function render()
    {
        return view('livewire.edit-ride');
    }
}
