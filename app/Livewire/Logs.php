<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\StaffLog;
use Livewire\WithPagination;
use Carbon\Carbon;

class Logs extends Component
{
    use WithPagination;

    public $logFilter = 'all';

    public function updatedLogFilter()
    {
        $this->resetPage();
    }

    protected function formatChanges($oldValues, $newValues)
    {
        $details = [];
        $changes = [];

        $formatTime = function ($timeValue) {
            if (!$timeValue) return $timeValue;
            try {
                return Carbon::createFromFormat('H:i:s', $timeValue)->format('h:i A');
            } catch (\Exception $e) {
                return $timeValue;
            }
        };

        if (!$newValues) {
            // Format details for delete operation
            if (isset($oldValues['rideType'])) {
                $details[] = [
                    'label' => 'Ride Type',
                    'value' => $oldValues['rideType']
                ];
            }
            if (isset($oldValues['classification'])) {
                $details[] = [
                    'label' => 'Classification',
                    'value' => $oldValues['classification']
                ];
            }
            if (isset($oldValues['timeStart'])) {
                $details[] = [
                    'label' => 'Time Start',
                    'value' => $formatTime($oldValues['timeStart'])
                ];
            }
            if (isset($oldValues['timeEnd'])) {
                $details[] = [
                    'label' => 'Time End',
                    'value' => $formatTime($oldValues['timeEnd'])
                ];
            }
        } elseif (!$oldValues && $newValues) {
            // Format details for create operation
            if (isset($newValues['rideType'])) {
                $details[] = [
                    'label' => 'Ride Type',
                    'value' => $newValues['rideType']
                ];
            }
            if (isset($newValues['classification'])) {
                $details[] = [
                    'label' => 'Classification',
                    'value' => $newValues['classification']
                ];
            }
            if (isset($newValues['timeStart'])) {
                $details[] = [
                    'label' => 'Time Start',
                    'value' => $formatTime($newValues['timeStart'])
                ];
            }
            if (isset($newValues['timeEnd'])) {
                $details[] = [
                    'label' => 'Time End',
                    'value' => $formatTime($newValues['timeEnd'])
                ];
            }
            // No field-by-field changes list needed for create
        } else {
            // Format details for edit operation
            if (isset($oldValues['rideType'])) {
                $details[] = [
                    'label' => 'Ride Type',
                    'value' => $oldValues['rideType']
                ];
            }
            if (isset($oldValues['classification'])) {
                $details[] = [
                    'label' => 'Classification',
                    'value' => $oldValues['classification']
                ];
            }

            // Format changes for edit operation
            foreach ($newValues as $key => $newValue) {
                if ($key === 'updated_at' || $key === 'pricePerHour') continue;
                
                $oldValue = $oldValues[$key] ?? null;
                
                if ($key === 'status') {
                    $oldStatus = $oldValue == 1 ? 'Completed' : 'Ongoing';
                    $newStatus = $newValue == 1 ? 'Completed' : 'Ongoing';
                    $changes[] = [
                        'label' => 'Status',
                        'value' => "Changed from {$oldStatus} to {$newStatus}"
                    ];
                } elseif ($key === 'timeStart' || $key === 'timeEnd') {
                    $changes[] = [
                        'label' => $key === 'timeStart' ? 'Time Start' : 'Time End',
                        'value' => 'Changed from "' . $formatTime($oldValue) . '" to "' . $formatTime($newValue) . '"'
                    ];
                } else {
                    $changes[] = [
                        'label' => ucfirst($key),
                        'value' => "Changed from \"{$oldValue}\" to \"{$newValue}\""
                    ];
                }
            }
        }

        return [
            'formatted_details' => $details,
            'formatted_changes' => $changes
        ];
    }

    public function render()
    {
        $query = StaffLog::with('user')->latest();

        if ($this->logFilter !== 'all') {
            $query->where('action', $this->logFilter);
        }

        $logs = $query->paginate(20);

        $logs->getCollection()->transform(function ($log) {
            $formatted = $this->formatChanges($log->old_values, $log->new_values);
            $log->formatted_details = $formatted['formatted_details'];
            $log->formatted_changes = $formatted['formatted_changes'];
            return $log;
        });

        return view('livewire.logs', [
            'logs' => $logs
        ]);
    }
}
