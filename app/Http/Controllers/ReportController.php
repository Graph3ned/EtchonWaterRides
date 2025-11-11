<?php

namespace App\Http\Controllers;

use App\Models\Rental;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Barryvdh\DomPDF\Facade\Pdf;

class ReportController extends Controller
{
    public function generate(Request $request)
    {
        // This method handles the report generation request
        // The actual generation is handled by the Livewire component
        return response()->json(['status' => 'success']);
    }

    public function export(Request $request, $type)
    {
        // Merge filters from request with session-backed defaults to mirror UI state
        $filters = $request->get('filters', []);
        $filters = [
            'dateRange' => $filters['dateRange'] ?? session('date_range', 'this_month'),
            'startDate' => $filters['startDate'] ?? session('start_date', ''),
            'endDate' => $filters['endDate'] ?? session('end_date', ''),
            'selectedDay' => $filters['selectedDay'] ?? session('selected_day', ''),
            'selectedMonth' => $filters['selectedMonth'] ?? session('selected_month', ''),
            'selectedYear' => $filters['selectedYear'] ?? session('selected_year', ''),
            'selectedUser' => $filters['selectedUser'] ?? session('selected_staff', ''),
            'selectedRideType' => $filters['selectedRideType'] ?? session('selected_ride_type', ''),
            'classification' => $filters['classification'] ?? session('selected_classification', ''),
            'selectedRideIdentifier' => $filters['selectedRideIdentifier'] ?? session('selected_ride_identifier', ''),
        ];

        // Get format from request (default to csv for backward compatibility)
        $format = $request->get('format', 'csv');

        // Build query based on filters
        $query = $this->buildFilteredQuery($filters);
        $rentals = $query->get();

        if ($format === 'pdf') {
            if ($type === 'financial') {
                return $this->exportFinancialPDF($rentals, $filters);
            } else {
                return $this->exportOperationalPDF($rentals, $filters);
            }
        } else {
            if ($type === 'financial') {
                return $this->exportFinancialCSV($rentals, $filters);
            } else {
                return $this->exportOperationalCSV($rentals, $filters);
            }
        }
    }

    protected function buildFilteredQuery($filters)
    {
        $query = Rental::query()->with(['ride.classification.rideType']);

        // Apply filters
        if (!empty($filters['selectedUser'])) {
            $query->where('user_name_at_time', $filters['selectedUser']);
        }
        
        if (!empty($filters['selectedRideType'])) {
            $query->where(function($q) use ($filters) {
                $q->where('ride_type_name_at_time', $filters['selectedRideType'])
                  ->orWhereHas('ride.classification.rideType', function($rq) use ($filters) {
                      $rq->where('name', $filters['selectedRideType']);
                  });
            });
        }
        
        if (!empty($filters['classification'])) {
            $query->where(function($q) use ($filters) {
                $q->where('classification_name_at_time', $filters['classification'])
                  ->orWhereHas('ride.classification', function($rq) use ($filters) {
                      $rq->where('name', $filters['classification']);
                  });
            });
        }
        
        if (!empty($filters['selectedRideIdentifier'])) {
            $query->where(function($q) use ($filters) {
                $q->where('ride_identifier_at_time', $filters['selectedRideIdentifier'])
                  ->orWhereHas('ride', function($rq) use ($filters) {
                      $rq->where('identifier', $filters['selectedRideIdentifier']);
                  });
            });
        }

        // Apply date range filter
        $query = $this->applyDateRangeFilter($query, $filters);

        return $query->orderBy('created_at', 'desc');
    }

    protected function applyDateRangeFilter($query, $filters)
    {
        $dateRange = $filters['dateRange'] ?? 'this_month';
        
        return match($dateRange) {
            'today' => $query->whereDate('created_at', Carbon::today()),
            'yesterday' => $query->whereDate('created_at', Carbon::yesterday()),
            'select_day' => $query->when(!empty($filters['selectedDay']), function ($query) use ($filters) {
                return $query->whereDate('created_at', $filters['selectedDay']);
            }),
            'this_week' => $query->whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]),
            'last_week' => $query->whereBetween('created_at', [Carbon::now()->subWeek()->startOfWeek(), Carbon::now()->subWeek()->endOfWeek()]),
            'this_month' => $query->whereMonth('created_at', Carbon::now()->month),
            'last_month' => $query->whereMonth('created_at', Carbon::now()->subMonth()->month),
            'select_month' => $query->when(!empty($filters['selectedMonth']), function ($query) use ($filters) {
                $monthVal = $filters['selectedMonth'];
                $year = Carbon::now()->year;
                if (strlen($monthVal) > 2) {
                    // Expecting format YYYY-MM from Flatpickr
                    [$year, $month] = array_map('intval', explode('-', $monthVal));
                } else {
                    $month = (int) $monthVal; // Expecting MM from mobile select
                }
                return $query->whereMonth('created_at', $month)->whereYear('created_at', $year);
            }),
            'this_year' => $query->whereYear('created_at', Carbon::now()->year),
            'last_year' => $query->whereYear('created_at', Carbon::now()->subYear()->year),
            'select_year' => $query->when(!empty($filters['selectedYear']), function ($query) use ($filters) {
                return $query->whereYear('created_at', (int) $filters['selectedYear']);
            }),
            'custom' => $query->when(!empty($filters['startDate']) && !empty($filters['endDate']), function ($query) use ($filters) {
                return $query->whereDate('created_at', '>=', $filters['startDate'])
                             ->whereDate('created_at', '<=', $filters['endDate']);
            }),
            default => $query
        };
    }


    protected function exportFinancialCSV($rentals, $filters)
    {
        $filename = 'financial_report_' . Carbon::now()->format('Ymd_His') . '.csv';
        
        return response()->streamDownload(function () use ($rentals, $filters) {
            $handle = fopen('php://output', 'w');
            
            // UTF-8 BOM for Excel
            fprintf($handle, chr(0xEF) . chr(0xBB) . chr(0xBF));
            
            // Calculate summary data
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
            
            // Summary section
            fputcsv($handle, ['FINANCIAL REPORT SUMMARY']);
            fputcsv($handle, ['Generated on', Carbon::now()->format('M d, Y \a\t h:i A')]);
            fputcsv($handle, ['Period', $this->getPeriodDescription($filters)]);
            fputcsv($handle, []);
            
            fputcsv($handle, ['Key Metrics']);
            fputcsv($handle, ['Total Revenue', '₱' . number_format($totalRevenue, 2)]);
            fputcsv($handle, ['Total Rentals', $totalRentals]);
            fputcsv($handle, ['Average Transaction', '₱' . number_format($averageTransaction, 2)]);
            fputcsv($handle, []);
            
            // Revenue by ride type
            fputcsv($handle, ['Revenue by Ride Type']);
            fputcsv($handle, ['Ride Type', 'Revenue']);
            foreach ($revenueByRideType as $rideType => $revenue) {
                fputcsv($handle, [$rideType, '₱' . number_format($revenue, 2)]);
            }
            fputcsv($handle, []);
            
            // Revenue by staff
            fputcsv($handle, ['Revenue by Staff']);
            fputcsv($handle, ['Staff Member', 'Revenue']);
            foreach ($revenueByStaff as $staff => $revenue) {
                fputcsv($handle, [$staff, '₱' . number_format($revenue, 2)]);
            }
            fputcsv($handle, []);
            
            // Detailed transactions
            fputcsv($handle, ['Detailed Transactions']);
            fputcsv($handle, [
                'Date', 'Staff', 'Ride Type', 'Classification', 'Ride Identifier', 'Duration (min)', 
                'Life Jackets', 'Total Price', 'Start Time', 'End Time', 'Note'
            ]);
            
            foreach ($rentals as $rental) {
                $rideTypeName = $rental->ride->classification->rideType->name ?? $rental->ride_type_name_at_time ?? 'Unknown';
                $classificationName = $rental->ride->classification->name ?? $rental->classification_name_at_time ?? 'Unknown';
                $rideIdentifier = $rental->ride_identifier_at_time ?? $rental->ride->identifier ?? 'Unknown';
                $startTime = $rental->start_at ? Carbon::parse($rental->start_at)->format('h:i A') : '';
                $endTime = $rental->end_at ? Carbon::parse($rental->end_at)->format('h:i A') : '';
                $dateVal = $rental->created_at ? Carbon::parse($rental->created_at)->format('M/d/Y') : '';
                $staffName = $rental->user_name_at_time ?? 'Unknown';
                
                fputcsv($handle, [
                    $dateVal,
                    $staffName,
                    $rideTypeName,
                    $classificationName,
                    $rideIdentifier,
                    $rental->duration_minutes ?? 0,
                    $rental->life_jacket_quantity ?? 0,
                    '₱' . number_format($rental->computed_total ?? 0, 2),
                    $startTime,
                    $endTime,
                    $rental->note ?? '-'
                ]);
            }
            
            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Cache-Control' => 'no-store, no-cache, must-revalidate',
        ]);
    }

    protected function exportOperationalCSV($rentals, $filters)
    {
        $filename = 'operational_report_' . Carbon::now()->format('Ymd_His') . '.csv';
        
        return response()->streamDownload(function () use ($rentals, $filters) {
            $handle = fopen('php://output', 'w');
            
            // UTF-8 BOM for Excel
            fprintf($handle, chr(0xEF) . chr(0xBB) . chr(0xBF));
            
            // Calculate summary data
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
            
            // Summary section
            fputcsv($handle, ['OPERATIONAL REPORT SUMMARY']);
            fputcsv($handle, ['Generated on', Carbon::now()->format('M d, Y \a\t h:i A')]);
            fputcsv($handle, ['Period', $this->getPeriodDescription($filters)]);
            fputcsv($handle, []);
            
            fputcsv($handle, ['Key Metrics']);
            fputcsv($handle, ['Total Rentals', $totalRentals]);
            fputcsv($handle, ['Total Duration (minutes)', $totalDuration]);
            fputcsv($handle, ['Average Duration (minutes)', round($averageDuration, 2)]);
            fputcsv($handle, ['Life Jackets Used', $lifeJacketUsage]);
            fputcsv($handle, []);
            
            // Popular ride types
            fputcsv($handle, ['Most Popular Ride Types']);
            fputcsv($handle, ['Ride Type', 'Rental Count']);
            foreach ($popularRideTypes as $rideType => $count) {
                fputcsv($handle, [$rideType, $count]);
            }
            fputcsv($handle, []);
            
            // Staff performance
            fputcsv($handle, ['Staff Performance']);
            fputcsv($handle, ['Staff Member', 'Rentals', 'Revenue', 'Avg Duration (min)']);
            foreach ($staffPerformance as $staff => $performance) {
                fputcsv($handle, [
                    $staff,
                    $performance['rentals'],
                    '₱' . number_format($performance['revenue'], 2),
                    round($performance['avg_duration'], 2)
                ]);
            }
            fputcsv($handle, []);
            
            // Peak hours
            fputcsv($handle, ['Peak Hours Analysis']);
            fputcsv($handle, ['Hour', 'Rental Count']);
            foreach ($peakHours as $hour => $count) {
                fputcsv($handle, [$hour . ':00', $count]);
            }
            fputcsv($handle, []);
            
            // Detailed transactions
            fputcsv($handle, ['Detailed Transactions']);
            fputcsv($handle, [
                'Date', 'Staff', 'Ride Type', 'Classification', 'Ride Identifier', 'Duration (min)', 
                'Life Jackets', 'Total Price', 'Start Time', 'End Time', 'Note'
            ]);
            
            foreach ($rentals as $rental) {
                $rideTypeName = $rental->ride->classification->rideType->name ?? $rental->ride_type_name_at_time ?? 'Unknown';
                $classificationName = $rental->ride->classification->name ?? $rental->classification_name_at_time ?? 'Unknown';
                $rideIdentifier = $rental->ride_identifier_at_time ?? $rental->ride->identifier ?? 'Unknown';
                $startTime = $rental->start_at ? Carbon::parse($rental->start_at)->format('h:i A') : '';
                $endTime = $rental->end_at ? Carbon::parse($rental->end_at)->format('h:i A') : '';
                $dateVal = $rental->created_at ? Carbon::parse($rental->created_at)->format('M/d/Y') : '';
                $staffName = $rental->user_name_at_time ?? 'Unknown';
                
                fputcsv($handle, [
                    $dateVal,
                    $staffName,
                    $rideTypeName,
                    $classificationName,
                    $rideIdentifier,
                    $rental->duration_minutes ?? 0,
                    $rental->life_jacket_quantity ?? 0,
                    '₱' . number_format($rental->computed_total ?? 0, 2),
                    $startTime,
                    $endTime,
                    $rental->note ?? '-'
                ]);
            }
            
            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Cache-Control' => 'no-store, no-cache, must-revalidate',
        ]);
    }

    protected function exportFinancialPDF($rentals, $filters)
    {
        // Calculate summary data (same as CSV)
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

        // Format rentals data for PDF
        $formattedRentals = $rentals->map(function($rental) {
            $rideTypeName = $rental->ride->classification->rideType->name ?? $rental->ride_type_name_at_time ?? 'Unknown';
            $classificationName = $rental->ride->classification->name ?? $rental->classification_name_at_time ?? 'Unknown';
            $rideIdentifier = $rental->ride_identifier_at_time ?? $rental->ride->identifier ?? 'Unknown';
            $startTime = $rental->start_at ? Carbon::parse($rental->start_at)->format('h:i A') : '';
            $endTime = $rental->end_at ? Carbon::parse($rental->end_at)->format('h:i A') : '';
            $dateVal = $rental->created_at ? Carbon::parse($rental->created_at)->format('M/d/Y') : '';
            $staffName = $rental->user_name_at_time ?? 'Unknown';
            
            return [
                'date' => $dateVal,
                'staff' => $staffName,
                'ride_type' => $rideTypeName,
                'classification' => $classificationName,
                'ride_identifier' => $rideIdentifier,
                'duration' => $rental->duration_minutes ?? 0,
                'life_jackets' => $rental->life_jacket_quantity ?? 0,
                'total_price' => $rental->computed_total ?? 0,
                'start_time' => $startTime,
                'end_time' => $endTime,
                'note' => $rental->note ?? '-'
            ];
        });

        $data = [
            'title' => 'Financial Report',
            'period' => $this->getPeriodDescription($filters),
            'generated_at' => Carbon::now()->format('M d, Y \a\t h:i A'),
            'totalRevenue' => $totalRevenue,
            'totalRentals' => $totalRentals,
            'averageTransaction' => $averageTransaction,
            'revenueByRideType' => $revenueByRideType,
            'revenueByStaff' => $revenueByStaff,
            'rentals' => $formattedRentals
        ];

        $pdf = Pdf::loadView('reports.financial-pdf', $data);
        $filename = 'financial_report_' . Carbon::now()->format('Ymd_His') . '.pdf';
        
        return $pdf->download($filename);
    }

    protected function exportOperationalPDF($rentals, $filters)
    {
        // Calculate summary data (same as CSV)
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

        // Format rentals data for PDF
        $formattedRentals = $rentals->map(function($rental) {
            $rideTypeName = $rental->ride->classification->rideType->name ?? $rental->ride_type_name_at_time ?? 'Unknown';
            $classificationName = $rental->ride->classification->name ?? $rental->classification_name_at_time ?? 'Unknown';
            $rideIdentifier = $rental->ride_identifier_at_time ?? $rental->ride->identifier ?? 'Unknown';
            $startTime = $rental->start_at ? Carbon::parse($rental->start_at)->format('h:i A') : '';
            $endTime = $rental->end_at ? Carbon::parse($rental->end_at)->format('h:i A') : '';
            $dateVal = $rental->created_at ? Carbon::parse($rental->created_at)->format('M/d/Y') : '';
            $staffName = $rental->user_name_at_time ?? 'Unknown';
            
            return [
                'date' => $dateVal,
                'staff' => $staffName,
                'ride_type' => $rideTypeName,
                'classification' => $classificationName,
                'ride_identifier' => $rideIdentifier,
                'duration' => $rental->duration_minutes ?? 0,
                'life_jackets' => $rental->life_jacket_quantity ?? 0,
                'total_price' => $rental->computed_total ?? 0,
                'start_time' => $startTime,
                'end_time' => $endTime,
                'note' => $rental->note ?? '-'
            ];
        });

        $data = [
            'title' => 'Operational Report',
            'period' => $this->getPeriodDescription($filters),
            'generated_at' => Carbon::now()->format('M d, Y \a\t h:i A'),
            'totalRentals' => $totalRentals,
            'totalDuration' => $totalDuration,
            'averageDuration' => $averageDuration,
            'lifeJacketUsage' => $lifeJacketUsage,
            'popularRideTypes' => $popularRideTypes,
            'staffPerformance' => $staffPerformance,
            'peakHours' => $peakHours,
            'rentals' => $formattedRentals
        ];

        $pdf = Pdf::loadView('reports.operational-pdf', $data);
        $filename = 'operational_report_' . Carbon::now()->format('Ymd_His') . '.pdf';
        
        return $pdf->download($filename);
    }

    protected function getPeriodDescription($filters)
    {
        $dateRange = $filters['dateRange'] ?? 'this_month';
        
        return match($dateRange) {
            'today' => 'Today (' . Carbon::today()->format('M d, Y') . ')',
            'yesterday' => 'Yesterday (' . Carbon::yesterday()->format('M d, Y') . ')',
            'select_day' => 'Selected Day (' . (!empty($filters['selectedDay']) ? Carbon::parse($filters['selectedDay'])->format('M d, Y') : 'No date selected') . ')',
            'this_week' => 'This Week (' . Carbon::now()->startOfWeek()->format('M d') . ' - ' . Carbon::now()->endOfWeek()->format('M d, Y') . ')',
            'last_week' => 'Last Week (' . Carbon::now()->subWeek()->startOfWeek()->format('M d') . ' - ' . Carbon::now()->subWeek()->endOfWeek()->format('M d, Y') . ')',
            'this_month' => 'This Month (' . Carbon::now()->format('F Y') . ')',
            'last_month' => 'Last Month (' . Carbon::now()->subMonth()->format('F Y') . ')',
            'select_month' => 'Selected Month (' . (!empty($filters['selectedMonth']) ? (strlen($filters['selectedMonth']) > 2
                ? Carbon::parse($filters['selectedMonth'] . '-01')->format('F Y')
                : Carbon::createFromDate(null, (int) $filters['selectedMonth'], 1)->format('F ' . Carbon::now()->format('Y')))
                : 'No month selected') . ')',
            'this_year' => 'This Year (' . Carbon::now()->format('Y') . ')',
            'last_year' => 'Last Year (' . Carbon::now()->subYear()->format('Y') . ')',
            'select_year' => 'Selected Year (' . (!empty($filters['selectedYear']) ? $filters['selectedYear'] : 'No year selected') . ')',
            'custom' => 'Custom Range (' . ($filters['startDate'] ?? '') . ' to ' . ($filters['endDate'] ?? '') . ')',
            default => 'Unknown Period'
        };
    }
}
