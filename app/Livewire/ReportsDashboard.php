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
            'this_week' => $query->whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]),
            'last_week' => $query->whereBetween('created_at', [Carbon::now()->subWeek()->startOfWeek(), Carbon::now()->subWeek()->endOfWeek()]),
            'this_month' => $query->whereMonth('created_at', Carbon::now()->month),
            'last_month' => $query->whereMonth('created_at', Carbon::now()->subMonth()->month),
            'this_year' => $query->whereYear('created_at', Carbon::now()->year),
            'last_year' => $query->whereYear('created_at', Carbon::now()->subYear()->year),
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
            'Revenue Growth' => $revenueGrowth . '%'
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
                'ride_type' => $firstRental->ride->classification->rideType->name ?? $firstRental->ride_type_name_at_time ?? 'Unknown'
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
            'this_week' => $query->whereBetween('created_at', [Carbon::now()->subWeek()->startOfWeek(), Carbon::now()->subWeek()->endOfWeek()]),
            'last_week' => $query->whereBetween('created_at', [Carbon::now()->subWeeks(2)->startOfWeek(), Carbon::now()->subWeeks(2)->endOfWeek()]),
            'this_month' => $query->whereMonth('created_at', Carbon::now()->subMonth()->month),
            'last_month' => $query->whereMonth('created_at', Carbon::now()->subMonths(2)->month),
            'this_year' => $query->whereYear('created_at', Carbon::now()->subYear()->year),
            'last_year' => $query->whereYear('created_at', Carbon::now()->subYears(2)->year),
            default => $query->whereDate('created_at', '<', $this->startDate)
        };

        $previousRentals = $previousQuery->get();
        
        return [
            'revenue' => $previousRentals->sum('computed_total'),
            'rentals' => $previousRentals->count()
        ];
    }

    protected function calculateGrowthRate($current, $previous)
    {
        if ($previous == 0) {
            return $current > 0 ? 100 : 0;
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
        
        // Clear session values
        session()->forget(['selected_staff', 'selected_ride_type', 'selected_classification', 'selected_ride_identifier', 'date_range', 'start_date', 'end_date']);
        
        // Regenerate report with cleared filters
        $this->generateReport();
    }

    // Auto-generate report when filters change
    public function updated($property)
    {
        if (in_array($property, ['reportType', 'dateRange', 'startDate', 'endDate', 'selectedUser', 'selectedRideType', 'classification', 'selectedRideIdentifier'])) {
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
        $query = Rental::query();
        
        // Filter by selected ride type if any
        if ($this->selectedRideType !== '') {
            $query->where(function($q) {
                $q->where('ride_type_name_at_time', $this->selectedRideType)
                  ->orWhereHas('ride.classification.rideType', function($rq) {
                      $rq->where('name', $this->selectedRideType);
                  });
            });
        }
        
        return $query->distinct()
            ->pluck('classification_name_at_time')
            ->filter()
            ->sort()
            ->values();
    }

    public function getRideIdentifiersList()
    {
        $query = Rental::query();
        
        // Filter by selected classification if any
        if ($this->classification !== '') {
            $query->where(function($q) {
                $q->where('classification_name_at_time', $this->classification)
                  ->orWhereHas('ride.classification', function($rq) {
                      $rq->where('name', $this->classification);
                  });
            });
        }
        
        return $query->distinct()
            ->pluck('ride_identifier_at_time')
            ->filter()
            ->merge(
                \App\Models\Ride::whereNotNull('identifier')
                    ->when($this->classification !== '', function($q) {
                        $q->whereHas('classification', function($rq) {
                            $rq->where('name', $this->classification);
                        });
                    })
                    ->pluck('identifier')
            )
            ->unique()
            ->sort()
            ->values();
    }

    public function getPeriodDescription()
    {
        return match($this->dateRange) {
            'today' => 'Today (' . Carbon::today()->format('M d, Y') . ')',
            'yesterday' => 'Yesterday (' . Carbon::yesterday()->format('M d, Y') . ')',
            'this_week' => 'This Week (' . Carbon::now()->startOfWeek()->format('M d') . ' - ' . Carbon::now()->endOfWeek()->format('M d, Y') . ')',
            'last_week' => 'Last Week (' . Carbon::now()->subWeek()->startOfWeek()->format('M d') . ' - ' . Carbon::now()->subWeek()->endOfWeek()->format('M d, Y') . ')',
            'this_month' => 'This Month (' . Carbon::now()->format('F Y') . ')',
            'last_month' => 'Last Month (' . Carbon::now()->subMonth()->format('F Y') . ')',
            'this_year' => 'This Year (' . Carbon::now()->format('Y') . ')',
            'last_year' => 'Last Year (' . Carbon::now()->subYear()->format('Y') . ')',
            'custom' => 'Custom Range (' . ($this->startDate ?: '') . ' to ' . ($this->endDate ?: '') . ')',
            default => 'Unknown Period'
        };
    }

    public function render()
    {
        return view('livewire.reports-dashboard');
    }
}
