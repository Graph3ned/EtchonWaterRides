<?php

namespace App\Livewire;

use App\Models\Rental;
use Carbon\Carbon;
use Livewire\Component;
use Livewire\Attributes\On;

class ReportsDashboard extends Component
{
    public $reportType = 'financial';
    public $dateRange = 'this_month';
    public $startDate = '';
    public $endDate = '';
    public $selectedDay = '';
    public $selectedMonth = '';
    public $selectedYear = '';
    public $selectedUser = '';
    public $selectedRideType = '';
    public $classification = '';
    public $selectedRideIdentifier = '';
    public $reportData = [];
    public $reportSummary = [];

    public function mount()
    {
        // Get filter values from session (same as Sales component)
        $this->selectedUser = session('selected_staff', '');
        $this->selectedRideType = session('selected_ride_type', '');
        $this->classification = session('selected_classification', '');
        $this->selectedRideIdentifier = session('selected_ride_identifier', '');
        $this->dateRange = session('date_range', 'this_month');
        $this->startDate = session('start_date', '');
        $this->endDate = session('end_date', '');
        $this->selectedDay = session('selected_day', '');
        $this->selectedMonth = session('selected_month', '');
        $this->selectedYear = session('selected_year', '');
        
        // Auto-generate report on mount
        $this->generateReport();
    }

    public function generateReport()
    {
        // Get filtered data
        $query = $this->buildFilteredQuery();
        $rentals = $query->get();
        
        if ($this->reportType === 'financial') {
            $this->generateFinancialReport($rentals);
        } else {
            $this->generateOperationalReport($rentals);
        }
    }

    protected function buildFilteredQuery()
    {
        $query = Rental::query()->with(['ride.classification.rideType']);

        // Apply filters (same logic as Sales component)
        if ($this->selectedUser !== '') {
            $query->where('user_name_at_time', $this->selectedUser);
        }
        
        if ($this->selectedRideType !== '') {
            $query->where(function($q) {
                $q->where('ride_type_name_at_time', $this->selectedRideType)
                  ->orWhereHas('ride.classification.rideType', function($rq) {
                      $rq->where('name', $this->selectedRideType);
                  });
            });
        }
        
        if ($this->classification !== '') {
            $query->where(function($q) {
                $q->where('classification_name_at_time', $this->classification)
                  ->orWhereHas('ride.classification', function($rq) {
                      $rq->where('name', $this->classification);
                  });
            });
        }
        
        if ($this->selectedRideIdentifier !== '') {
            $query->where(function($q) {
                $q->where('ride_identifier_at_time', $this->selectedRideIdentifier)
                  ->orWhereHas('ride', function($rq) {
                      $rq->where('identifier', $this->selectedRideIdentifier);
                  });
            });
        }

        // Apply date range filter
        $query = $this->applyDateRangeFilter($query);

        return $query->orderBy('created_at', 'desc');
    }

    protected function applyDateRangeFilter($query)
    {
        return match($this->dateRange) {
            'today' => $query->whereDate('created_at', Carbon::today()),
            'yesterday' => $query->whereDate('created_at', Carbon::yesterday()),
            'select_day' => $query->when($this->selectedDay, function($query) {
                return $query->whereDate('created_at', $this->selectedDay);
            }),
            'this_week' => $query->whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]),
            'last_week' => $query->whereBetween('created_at', [Carbon::now()->subWeek()->startOfWeek(), Carbon::now()->subWeek()->endOfWeek()]),
            'this_month' => $query->whereMonth('created_at', Carbon::now()->month),
            'last_month' => $query->whereMonth('created_at', Carbon::now()->subMonth()->month),
            'select_month' => $query->when($this->selectedMonth, function($query) {
                // Handle both formats: "12" (from dropdown) and "2024-12" (from flatpickr)
                $month = $this->selectedMonth;
                $year = Carbon::now()->year; // Use current year by default
                
                if (strlen($month) > 2) {
                    // Format: "2024-12" from flatpickr - extract month and year
                    $parts = explode('-', $month);
                    $year = (int) $parts[0];
                    $month = (int) $parts[1];
                } else {
                    // Format: "12" from dropdown - convert to int, use current year
                    $month = (int) $month;
                }
                
                return $query->whereMonth('created_at', $month)
                            ->whereYear('created_at', $year);
            }),
            'this_year' => $query->whereYear('created_at', Carbon::now()->year),
            'last_year' => $query->whereYear('created_at', Carbon::now()->subYear()->year),
            'select_year' => $query->when($this->selectedYear, function($query) {
                return $query->whereYear('created_at', $this->selectedYear);
            }),
            'custom' => $query->when($this->startDate && $this->endDate, function ($query) {
                return $query->whereDate('created_at', '>=', $this->startDate)
                             ->whereDate('created_at', '<=', $this->endDate);
            }),
            default => $query
        };
    }

    protected function generateFinancialReport($rentals)
    {
        $totalRevenue = $rentals->sum('computed_total');
        $totalRentals = $rentals->count();
        $averageTransaction = $totalRentals > 0 ? $totalRevenue / $totalRentals : 0;

        // Revenue by ride type
        $revenueByRideType = $rentals->groupBy(function($rental) {
            return $rental->ride->classification->rideType->name ?? $rental->ride_type_name_at_time ?? 'Unknown';
        })->map(function($group) {
            return $group->sum('computed_total');
        });

        // Revenue by staff
        $revenueByStaff = $rentals->groupBy('user_name_at_time')->map(function($group) {
            return $group->sum('computed_total');
        });

        // Period comparison
        $previousPeriodData = $this->getPreviousPeriodData();
        $revenueGrowth = $this->calculateGrowthRate($totalRevenue, $previousPeriodData['revenue']);

        $this->reportData = [
            'rentals' => $rentals,
            'totalRevenue' => $totalRevenue,
            'totalRentals' => $totalRentals,
            'averageTransaction' => $averageTransaction,
            'revenueByRideType' => $revenueByRideType,
            'revenueByStaff' => $revenueByStaff,
            'revenueGrowth' => $revenueGrowth,
            'previousPeriod' => $previousPeriodData
        ];

        $this->reportSummary = [
            'Total Revenue' => '₱' . number_format($totalRevenue, 2),
            'Total Rentals' => $totalRentals,
            'Average Transaction' => '₱' . number_format($averageTransaction, 2),
            'Revenue Growth' => $revenueGrowth !== null ? $revenueGrowth . '%' : 'No comparison data'
        ];
    }

    protected function generateOperationalReport($rentals)
    {
        $totalRentals = $rentals->count();
        $totalDuration = $rentals->sum('duration_minutes');
        $averageDuration = $totalRentals > 0 ? $totalDuration / $totalRentals : 0;

        // Most popular ride types
        $popularRideTypes = $rentals->groupBy(function($rental) {
            return $rental->ride->classification->rideType->name ?? $rental->ride_type_name_at_time ?? 'Unknown';
        })->map(function($group) {
            return $group->count();
        })->sortDesc();

        // Staff performance
        $staffPerformance = $rentals->groupBy('user_name_at_time')->map(function($group) {
            return [
                'rentals' => $group->count(),
                'revenue' => $group->sum('computed_total'),
                'avg_duration' => $group->avg('duration_minutes')
            ];
        });

        // Peak hours analysis
        $peakHours = $rentals->groupBy(function($rental) {
            return Carbon::parse($rental->start_at)->format('H');
        })->map(function($group) {
            return $group->count();
        })->sortDesc();

        // Life jacket usage
        $lifeJacketUsage = $rentals->sum('life_jacket_quantity');

        // Ride utilization by identifier
        $rideUtilization = $rentals->groupBy(function($rental) {
            return $rental->ride_identifier_at_time ?? $rental->ride->identifier ?? 'Unknown';
        })->map(function($group) {
            $firstRental = $group->first();
            return [
                'rentals' => $group->count(),
                'total_duration' => $group->sum('duration_minutes'),
                'ride_type' => $firstRental->ride->classification->rideType->name ?? $firstRental->ride_type_name_at_time ?? 'Unknown',
                'classification' => $firstRental->ride->classification->name ?? $firstRental->classification_name_at_time ?? 'Unknown',
            ];
        })->sortByDesc('rentals');

        $this->reportData = [
            'rentals' => $rentals,
            'totalRentals' => $totalRentals,
            'totalDuration' => $totalDuration,
            'averageDuration' => $averageDuration,
            'popularRideTypes' => $popularRideTypes,
            'staffPerformance' => $staffPerformance,
            'peakHours' => $peakHours,
            'lifeJacketUsage' => $lifeJacketUsage,
            'rideUtilization' => $rideUtilization
        ];

        $this->reportSummary = [
            'Total Rentals' => $totalRentals,
            'Total Duration' => $this->formatDuration($totalDuration),
            'Average Duration' => $this->formatDuration($averageDuration),
            'Life Jackets Used' => $lifeJacketUsage
        ];
    }

    protected function getPreviousPeriodData()
    {
        $query = Rental::query();
        
        // Apply same filters but for previous period
        if ($this->selectedUser !== '') {
            $query->where('user_name_at_time', $this->selectedUser);
        }
        
        if ($this->selectedRideType !== '') {
            $query->where(function($q) {
                $q->where('ride_type_name_at_time', $this->selectedRideType)
                  ->orWhereHas('ride.classification.rideType', function($rq) {
                      $rq->where('name', $this->selectedRideType);
                  });
            });
        }
        
        if ($this->classification !== '') {
            $query->where(function($q) {
                $q->where('classification_name_at_time', $this->classification)
                  ->orWhereHas('ride.classification', function($rq) {
                      $rq->where('name', $this->classification);
                  });
            });
        }
        
        if ($this->selectedRideIdentifier !== '') {
            $query->where(function($q) {
                $q->where('ride_identifier_at_time', $this->selectedRideIdentifier)
                  ->orWhereHas('ride', function($rq) {
                      $rq->where('identifier', $this->selectedRideIdentifier);
                  });
            });
        }

        // Get previous period based on current date range
        $previousQuery = match($this->dateRange) {
            'today' => $query->whereDate('created_at', Carbon::yesterday()),
            'yesterday' => $query->whereDate('created_at', Carbon::now()->subDays(2)),
            'select_day' => $query->when($this->selectedDay, function($query) {
                return $query->whereDate('created_at', Carbon::parse($this->selectedDay)->subDay());
            }),
            'this_week' => $query->whereBetween('created_at', [Carbon::now()->subWeek()->startOfWeek(), Carbon::now()->subWeek()->endOfWeek()]),
            'last_week' => $query->whereBetween('created_at', [Carbon::now()->subWeeks(2)->startOfWeek(), Carbon::now()->subWeeks(2)->endOfWeek()]),
            'this_month' => $query->whereMonth('created_at', Carbon::now()->subMonth()->month),
            'last_month' => $query->whereMonth('created_at', Carbon::now()->subMonths(2)->month),
            'select_month' => $query->when($this->selectedMonth, function($query) {
                // Handle both formats: "12" (from dropdown) and "2024-12" (from flatpickr)
                $month = $this->selectedMonth;
                $year = Carbon::now()->year; // Use current year
                
                if (strlen($month) > 2) {
                    // Format: "2024-12" from flatpickr - extract month and year
                    $parts = explode('-', $month);
                    $year = (int) $parts[0];
                    $month = (int) $parts[1];
                } else {
                    // Format: "12" from dropdown - convert to int, use current year
                    $month = (int) $month;
                }
                
                // Get previous month (same year)
                $previousMonth = $month - 1;
                if ($previousMonth <= 0) {
                    $previousMonth = 12;
                    $year = $year - 1; // If going to previous month, go to previous year
                }
                
                return $query->whereMonth('created_at', $previousMonth)
                            ->whereYear('created_at', $year);
            }),
            'this_year' => $query->whereYear('created_at', Carbon::now()->subYear()->year),
            'last_year' => $query->whereYear('created_at', Carbon::now()->subYears(2)->year),
            'select_year' => $query->when($this->selectedYear, function($query) {
                return $query->whereYear('created_at', $this->selectedYear - 1);
            }),
            'custom' => $this->getCustomPreviousPeriod($query),
            default => $query->whereDate('created_at', '<', $this->startDate)
        };

        $previousRentals = $previousQuery->get();
        
        return [
            'revenue' => $previousRentals->sum('computed_total'),
            'rentals' => $previousRentals->count()
        ];
    }

    protected function getCustomPreviousPeriod($query)
    {
        if (!$this->startDate || !$this->endDate) {
            return $query->whereRaw('1 = 0'); // Return empty result
        }
        
        $startDate = Carbon::parse($this->startDate);
        $endDate = Carbon::parse($this->endDate);
        $periodLength = $startDate->diffInDays($endDate);
        
        // Calculate previous period with same length
        $previousStartDate = $startDate->copy()->subDays($periodLength + 1);
        $previousEndDate = $startDate->copy()->subDay();
        
        return $query->whereBetween('created_at', [$previousStartDate, $previousEndDate]);
    }

    protected function calculateGrowthRate($current, $previous)
    {
        if ($previous == 0) {
            // If previous period has no data, don't show 100% growth
            // Instead, show a neutral indicator or skip growth calculation
            return null; // This will indicate no comparison available
        }
        
        return round((($current - $previous) / $previous) * 100, 1);
    }

    protected function formatDuration($minutes)
    {
        if ($minutes >= 60) {
            $hours = intdiv($minutes, 60);
            $remainingMinutes = $minutes % 60;
            return $hours . 'hr' . ($remainingMinutes > 0 ? ' ' . $remainingMinutes . 'min' : '');
        }
        return $minutes . 'min';
    }

    public function exportReport()
    {
        // This will be handled by the controller
        return redirect()->route('reports.export', [
            'type' => $this->reportType,
            'format' => 'csv',
            'filters' => [
                'dateRange' => $this->dateRange,
                'startDate' => $this->startDate,
                'endDate' => $this->endDate,
                'selectedDay' => $this->selectedDay,
                'selectedMonth' => $this->selectedMonth,
                'selectedYear' => $this->selectedYear,
                'selectedUser' => $this->selectedUser,
                'selectedRideType' => $this->selectedRideType,
                'classification' => $this->classification,
                'selectedRideIdentifier' => $this->selectedRideIdentifier
            ]
        ]);
    }


    public function clearFilters()
    {
        // Reset all filters to default values
        $this->selectedUser = '';
        $this->selectedRideType = '';
        $this->classification = '';
        $this->selectedRideIdentifier = '';
        $this->dateRange = 'this_month';
        $this->startDate = '';
        $this->endDate = '';
        $this->selectedDay = '';
        $this->selectedMonth = '';
        $this->selectedYear = '';
        
        // Clear session values
        session()->forget(['selected_staff', 'selected_ride_type', 'selected_classification', 'selected_ride_identifier', 'date_range', 'start_date', 'end_date', 'selected_day', 'selected_month', 'selected_year']);
        
        // Regenerate report with cleared filters
        $this->generateReport();
    }

    // Auto-generate report when filters change
    public function updated($property)
    {
        if (in_array($property, ['reportType', 'dateRange', 'startDate', 'endDate', 'selectedDay', 'selectedMonth', 'selectedYear', 'selectedUser', 'selectedRideType', 'classification', 'selectedRideIdentifier'])) {
            $this->generateReport();
        }
    }

    // Handle cascading filter logic
    public function updatedSelectedRideType()
    {
        // Reset classification and ride identifier when ride type changes
        $this->classification = '';
        $this->selectedRideIdentifier = '';
        $this->generateReport();
    }

    public function updatedClassification()
    {
        // Reset ride identifier when classification changes
        $this->selectedRideIdentifier = '';
        $this->generateReport();
    }

    public function getStaffList()
    {
        return Rental::query()
            ->distinct()
            ->pluck('user_name_at_time')
            ->filter()
            ->sort()
            ->values();
    }

    public function getRideTypesList()
    {
        return Rental::query()
            ->selectRaw('DISTINCT ride_types.name')
            ->join('rides', 'rentals.ride_id', '=', 'rides.id')
            ->join('classifications', 'rides.classification_id', '=', 'classifications.id')
            ->join('ride_types', 'classifications.ride_type_id', '=', 'ride_types.id')
            ->pluck('ride_types.name')
            ->filter()
            ->sort()
            ->values();
    }

    public function getClassificationsList()
    {
        // Only provide classifications when a ride type is selected
        if ($this->selectedRideType === '') {
            return collect();
        }

        $query = Rental::query();
        $query->where(function($q) {
            $q->where('ride_type_name_at_time', $this->selectedRideType)
              ->orWhereHas('ride.classification.rideType', function($rq) {
                  $rq->where('name', $this->selectedRideType);
              });
        });

        return $query->distinct()
            ->pluck('classification_name_at_time')
            ->filter()
            ->sort()
            ->values();
    }

    public function getRideIdentifiersList()
    {
        // Only provide identifiers when a classification is selected
        if ($this->classification === '') {
            return collect();
        }

        $query = Rental::query();
        $query->where(function($q) {
            $q->where('classification_name_at_time', $this->classification)
              ->orWhereHas('ride.classification', function($rq) {
                  $rq->where('name', $this->classification);
              });
        });

        return $query->distinct()
            ->pluck('ride_identifier_at_time')
            ->filter()
            ->merge(
                \App\Models\Ride::whereNotNull('identifier')
                    ->whereHas('classification', function($rq) {
                        $rq->where('name', $this->classification);
                    })
                    ->pluck('identifier')
            )
            ->unique()
            ->sort()
            ->values();
    }

    public function getSelectedMonthName()
    {
        if (!$this->selectedMonth) {
            return 'Select Month';
        }
        
        // Handle both formats: "12" (from dropdown) and "2024-12" (from flatpickr)
        if (strlen($this->selectedMonth) > 2) {
            // Format: "2024-12" from flatpickr
            return Carbon::parse($this->selectedMonth . '-01')->format('F');
        } else {
            // Format: "12" from dropdown
            return Carbon::createFromDate(null, $this->selectedMonth, 1)->format('F');
        }
    }

    public function getPeriodDescription()
    {
        return match($this->dateRange) {
            'today' => 'Today (' . Carbon::today()->format('M d, Y') . ')',
            'yesterday' => 'Yesterday (' . Carbon::yesterday()->format('M d, Y') . ')',
            'select_day' => 'Selected Day (' . ($this->selectedDay ? Carbon::parse($this->selectedDay)->format('M d, Y') : 'No date selected') . ')',
            'this_week' => 'This Week (' . Carbon::now()->startOfWeek()->format('M d') . ' - ' . Carbon::now()->endOfWeek()->format('M d, Y') . ')',
            'last_week' => 'Last Week (' . Carbon::now()->subWeek()->startOfWeek()->format('M d') . ' - ' . Carbon::now()->subWeek()->endOfWeek()->format('M d, Y') . ')',
            'this_month' => 'This Month (' . Carbon::now()->format('F Y') . ')',
            'last_month' => 'Last Month (' . Carbon::now()->subMonth()->format('F Y') . ')',
            'select_month' => 'Selected Month (' . ($this->selectedMonth ? $this->getSelectedMonthName() : 'No month selected') . ')',
            'this_year' => 'This Year (' . Carbon::now()->format('Y') . ')',
            'last_year' => 'Last Year (' . Carbon::now()->subYear()->format('Y') . ')',
            'select_year' => 'Selected Year (' . ($this->selectedYear ?: 'No year selected') . ')',
            'custom' => 'Custom Range (' . ($this->startDate ?: '') . ' to ' . ($this->endDate ?: '') . ')',
            default => 'Unknown Period'
        };
    }

    public function render()
    {
        return view('livewire.reports-dashboard');
    }
}
