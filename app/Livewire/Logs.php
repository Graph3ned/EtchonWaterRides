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

        $formatDateTime = function ($value) {
            if (!$value) return $value;
            try {
                return Carbon::parse($value)->timezone('Asia/Manila')->format('h:i A');
            } catch (\Exception $e) {
                try {
                    return Carbon::createFromFormat('H:i:s', $value)->format('h:i A');
                } catch (\Exception $e2) {
                    return $value;
                }
            }
        };

        $resolve = function ($source, array $keys) {
            foreach ($keys as $key) {
                if (isset($source[$key]) && $source[$key] !== null && $source[$key] !== '') {
                    return $source[$key];
                }
            }
            return null;
        };

        if (!$newValues) {
            // Format details for delete operation
            if ($resolve($oldValues, ['rideType', 'ride_type_name_at_time'])) {
                $details[] = [
                    'label' => 'Ride Type',
                    'value' => $resolve($oldValues, ['rideType', 'ride_type_name_at_time'])
                ];
            }
            if ($resolve($oldValues, ['classification', 'classification_name_at_time'])) {
                $details[] = [
                    'label' => 'Classification',
                    'value' => $resolve($oldValues, ['classification', 'classification_name_at_time'])
                ];
            }
            if ($resolve($oldValues, ['identifier', 'ride_identifier_at_time'])) {
                $details[] = [
                    'label' => 'Identification',
                    'value' => $resolve($oldValues, ['identifier', 'ride_identifier_at_time'])
                ];
            }
            if (isset($oldValues['delete_reason']) && $oldValues['delete_reason'] !== '') {
                $details[] = [
                    'label' => 'Delete Reason',
                    'value' => $oldValues['delete_reason']
                ];
            }
            if (isset($oldValues['timeStart']) || isset($oldValues['start_at'])) {
                $details[] = [
                    'label' => 'Time Start',
                    'value' => $formatDateTime($oldValues['timeStart'] ?? $oldValues['start_at'])
                ];
            }
            if (isset($oldValues['timeEnd']) || isset($oldValues['end_at'])) {
                $details[] = [
                    'label' => 'Time End',
                    'value' => $formatDateTime($oldValues['timeEnd'] ?? $oldValues['end_at'])
                ];
            }
        } elseif (!$oldValues && $newValues) {
            // Format details for create operation
            if ($resolve($newValues, ['rideType', 'ride_type_name_at_time'])) {
                $details[] = [
                    'label' => 'Ride Type',
                    'value' => $resolve($newValues, ['rideType', 'ride_type_name_at_time'])
                ];
            }
            if ($resolve($newValues, ['classification', 'classification_name_at_time'])) {
                $details[] = [
                    'label' => 'Classification',
                    'value' => $resolve($newValues, ['classification', 'classification_name_at_time'])
                ];
            }
            if ($resolve($newValues, ['identifier', 'ride_identifier_at_time'])) {
                $details[] = [
                    'label' => 'Identification',
                    'value' => $resolve($newValues, ['identifier', 'ride_identifier_at_time'])
                ];
            }
            if (isset($newValues['timeStart']) || isset($newValues['start_at'])) {
                $details[] = [
                    'label' => 'Time Start',
                    'value' => $formatDateTime($newValues['timeStart'] ?? $newValues['start_at'])
                ];
            }
            if (isset($newValues['timeEnd']) || isset($newValues['end_at'])) {
                $details[] = [
                    'label' => 'Time End',
                    'value' => $formatDateTime($newValues['timeEnd'] ?? $newValues['end_at'])
                ];
            }
            // No field-by-field changes list needed for create
        } else {
            // Format details for edit operation
            if ($resolve($oldValues, ['rideType', 'ride_type_name_at_time'])) {
                $details[] = [
                    'label' => 'Ride Type',
                    'value' => $resolve($oldValues, ['rideType', 'ride_type_name_at_time'])
                ];
            }
            if ($resolve($oldValues, ['classification', 'classification_name_at_time'])) {
                $details[] = [
                    'label' => 'Classification',
                    'value' => $resolve($oldValues, ['classification', 'classification_name_at_time'])
                ];
            }
            if ($resolve($oldValues, ['identifier', 'ride_identifier_at_time'])) {
                $details[] = [
                    'label' => 'Identification',
                    'value' => $resolve($oldValues, ['identifier', 'ride_identifier_at_time'])
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
                } elseif (in_array($key, ['timeStart', 'timeEnd', 'start_at', 'end_at'])) {
                    $changes[] = [
                        'label' => in_array($key, ['timeStart', 'start_at']) ? 'Time Start' : 'Time End',
                        'value' => 'Changed from "' . $formatDateTime($oldValue) . '" to "' . $formatDateTime($newValue) . '"'
                    ];
                } elseif (in_array($key, ['rideType', 'ride_type_name_at_time'])) {
                    $changes[] = [
                        'label' => 'Ride Type',
                        'value' => 'Changed from "' . ($oldValue ?? 'Unknown') . '" to "' . ($newValue ?? 'Unknown') . '"'
                    ];
                } elseif (in_array($key, ['classification', 'classification_name_at_time'])) {
                    $changes[] = [
                        'label' => 'Classification',
                        'value' => 'Changed from "' . ($oldValue ?? 'Unknown') . '" to "' . ($newValue ?? 'Unknown') . '"'
                    ];
                } elseif (in_array($key, ['identifier', 'ride_identifier_at_time'])) {
                    $changes[] = [
                        'label' => 'Identification',
                        'value' => 'Changed from "' . ($oldValue ?? 'Unknown') . '" to "' . ($newValue ?? 'Unknown') . '"'
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
