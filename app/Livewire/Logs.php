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
    public $startDate = null; // YYYY-MM-DD (derived)
    public $endDate = null;   // YYYY-MM-DD (derived)
    // Sales-like date range controls
    public $dateRange = '';
    public $selected_month = null; // Y-m
    public $selected_day = null;   // Y-m-d (alt input)
    public $start_date = null;     // custom range start (Y-m-d)
    public $end_date = null;       // custom range end (Y-m-d)

    public $staff = null;

    // public function updatedStaff()
    // {
    //     $this->resetPage();
    // }

    // public function updatedLogFilter()
    // {
    //     $this->resetPage();
    // }

    // public function updatedStartDate()
    // {
    //     $this->resetPage();
    // }

    // public function updatedEndDate()
    // {
    //     $this->resetPage();
    // }

    // public function updatedDateRange()
    // {
    //     $this->resetPage();
    // }

    // public function updatedSelectedMonth()
    // {
    //     $this->resetPage();
    // }

    // public function updatedSelectedDay()
    // {
    //     $this->resetPage();
    // }

    // public function updatedStartDateCustom()
    // {
    //     $this->resetPage();
    // }

    // public function updatedEndDateCustom()
    // {
    //     $this->resetPage();
    // }

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

    public function clearFilters(): void
    {
        $this->logFilter = 'all';
        $this->staff = null;
        $this->dateRange = '';
        $this->selected_month = null;
        $this->selected_day = null;
        $this->start_date = null;
        $this->end_date = null;
        $this->startDate = null;
        $this->endDate = null;
        $this->resetPage();
    }

    public function render()
    {
        // Derive startDate/endDate from sales-like controls
        $this->startDate = null;
        $this->endDate = null;

        switch ($this->dateRange) {
            case 'today':
                $this->startDate = Carbon::today('Asia/Manila')->toDateString();
                $this->endDate = Carbon::today('Asia/Manila')->toDateString();
                break;
            case 'select_day':
                if (!empty($this->selected_day)) {
                    // selected_day expected Y-m-d
                    $this->startDate = Carbon::parse($this->selected_day)->toDateString();
                    $this->endDate = Carbon::parse($this->selected_day)->toDateString();
                }
                break;
            case 'select_month':
                if (!empty($this->selected_month)) {
                    // selected_month expected Y-m
                    $start = Carbon::createFromFormat('Y-m', $this->selected_month)->startOfMonth();
                    $end = Carbon::createFromFormat('Y-m', $this->selected_month)->endOfMonth();
                    $this->startDate = $start->toDateString();
                    $this->endDate = $end->toDateString();
                }
                break;
            case 'this_month':
                $this->startDate = Carbon::now('Asia/Manila')->startOfMonth()->toDateString();
                $this->endDate = Carbon::now('Asia/Manila')->endOfMonth()->toDateString();
                break;
            case 'custom':
                if (!empty($this->start_date)) {
                    $this->startDate = Carbon::parse($this->start_date)->toDateString();
                }
                if (!empty($this->end_date)) {
                    $this->endDate = Carbon::parse($this->end_date)->toDateString();
                }
                break;
            default:
                // All time - do nothing
                break;
        }
        switch ($this->dateRange) {
            case 'today':
                $this->startDate = Carbon::today('Asia/Manila')->toDateString();
                $this->endDate = Carbon::today('Asia/Manila')->toDateString();
                break;
            case 'select_day':
                if (!empty($this->selected_day)) {
                    // selected_day expected Y-m-d
                    $this->startDate = Carbon::parse($this->selected_day)->toDateString();
                    $this->endDate = Carbon::parse($this->selected_day)->toDateString();
                }
                break;
            case 'select_month':
                if (!empty($this->selected_month)) {
                    // selected_month expected Y-m
                    $start = Carbon::createFromFormat('Y-m', $this->selected_month)->startOfMonth();
                    $end = Carbon::createFromFormat('Y-m', $this->selected_month)->endOfMonth();
                    $this->startDate = $start->toDateString();
                    $this->endDate = $end->toDateString();
                }
                break;
            case 'this_month':
                $this->startDate = Carbon::now('Asia/Manila')->startOfMonth()->toDateString();
                $this->endDate = Carbon::now('Asia/Manila')->endOfMonth()->toDateString();
                break;
            case 'custom':
                if (!empty($this->start_date)) {
                    $this->startDate = Carbon::parse($this->start_date)->toDateString();
                }
                if (!empty($this->end_date)) {
                    $this->endDate = Carbon::parse($this->end_date)->toDateString();
                }
                break;
            default:
                // All time - do nothing
                break;
        }


        // Build staff options for filter (distinct staff who have logs)
        $staffOptions = StaffLog::with('user')
            ->select('user_id')
            ->whereNotNull('user_id')
            ->distinct()
            ->get()
            ->map(function ($log) {
                return [
                    'id' => $log->user_id,
                    'name' => optional($log->user)->name ?? 'User #' . $log->user_id,
                ];
            })
            ->sortBy('name')
            ->values();

        $query = StaffLog::with('user')->latest();

        if ($this->logFilter !== 'all') {
            $query->where('action', $this->logFilter);
        }

        if (!empty($this->staff)) {
            $query->where('user_id', $this->staff);
        }

        // Apply date filters if provided
        if (!empty($this->startDate)) {
            $query->whereDate('created_at', '>=', $this->startDate);
        }
        if (!empty($this->endDate)) {
            $query->whereDate('created_at', '<=', $this->endDate);
        }

        $logs = $query->paginate(20);

        $logs->getCollection()->transform(function ($log) {
            $formatted = $this->formatChanges($log->old_values, $log->new_values);
            $log->formatted_details = $formatted['formatted_details'];
            $log->formatted_changes = $formatted['formatted_changes'];
            return $log;
        });

        return view('livewire.logs', [
            'logs' => $logs,
            'staffOptions' => $staffOptions,
        ]);
    }
}
