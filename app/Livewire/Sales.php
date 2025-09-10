<?php

namespace App\Livewire;

use App\Models\Rental;
use Carbon\Carbon;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\On;

class Sales extends Component
{
    use WithPagination;

    public $totalPrice = 0;
    public $showModal = false;
    public $modalDetails;
    public $paginate = 10;
    public $selectedUser = '';
    public $selectedRideType = '';
    public $classification = '';
    public $start_date = ''; 
    public $end_date = ''; 
    public $load = [];
    public $dateRange = '';
    public $percentageChange;
    public $selected_day = '';
    public $selected_month = '';
    
    protected $listeners = [
        'closeModal' => 'closeEditModal',
        'rideUpdated' => 'refreshPage'
    ];

    public function mount()
    {
        // Get all stored values from session, defaulting dateRange to 'this_month'
        $this->selectedUser = session('selected_staff', '');
        $this->selectedRideType = session('selected_ride_type', '');
        $this->classification = session('selected_classification', '');
        $this->dateRange = session('date_range', 'this_month'); // Changed default to 'this_month'
        $this->start_date = session('start_date', '');
        $this->end_date = session('end_date', '');
        $this->paginate = session('paginate', 10);
        $this->load = Rental::all();
        $this->selected_day = session('selected_day', '');
        $this->selected_month = session('selected_month', '');
    }


    public function resetFilter()
    {
        // Clear all session values
        session()->forget([
            'selected_staff',
            'selected_ride_type',
            'selected_classification',
            'date_range',
            'start_date',
            'end_date',
            'paginate',
            'selected_month'
        ]);
        
        // Reset all properties
        $this->selectedUser = '';
        $this->selectedRideType = '';
        $this->classification = '';
        $this->dateRange = 'this_month'; 
        $this->start_date = null;
        $this->end_date = null;
        $this->paginate = '10';
        $this->selected_month = '';
        
        $this->resetPage();
        
        $url = preg_replace('/\?page=\d+/', '', request()->header('Referer'));
        $url = preg_replace('/&page=\d+/', '', $url);
        return redirect($url);
    }

    public function updatedSelectedUser()
    {
        $this->resetPage();
        
        // Store the selected staff in session
        session(['selected_staff' => $this->selectedUser]);
        session(['selected_ride_type' => $this->selectedRideType]);
        session(['selected_classification' => $this->classification]);
        
        // Get current URL and remove any page parameter
        $url = preg_replace('/\?page=\d+/', '', request()->header('Referer'));
        $url = preg_replace('/&page=\d+/', '', $url);
        
        // Refresh the page with clean URL
        return redirect($url);
    }

    public function updatedSelectedRideType()
    {
        $this->resetPage();
        
        // Store the selected values in session
        session(['selected_staff' => $this->selectedUser]);
        session(['selected_ride_type' => $this->selectedRideType]);
        session(['selected_classification' => $this->classification]);
        
        // Clean URL and redirect
        $url = preg_replace('/\?page=\d+/', '', request()->header('Referer'));
        $url = preg_replace('/&page=\d+/', '', $url);
        return redirect($url);
    }

    public function updatedClassification()
    {
        $this->resetPage();
        
        // Store the selected values in session
        session(['selected_staff' => $this->selectedUser]);
        session(['selected_ride_type' => $this->selectedRideType]);
        session(['selected_classification' => $this->classification]);
        
        // Clean URL and redirect
        $url = preg_replace('/\?page=\d+/', '', request()->header('Referer'));
        $url = preg_replace('/&page=\d+/', '', $url);
        return redirect($url);
    }

    public function updatedDateRange()
    {
        $this->resetPage();
        
        // Store date range in session
        session(['date_range' => $this->dateRange]);
        
        // Clean URL and redirect
        $url = preg_replace('/\?page=\d+/', '', request()->header('Referer'));
        $url = preg_replace('/&page=\d+/', '', $url);
        return redirect($url);
    }

    public function updatedStartDate()
    {
        $this->resetPage();
        
        // Store start date in session
        session(['start_date' => $this->start_date]);
        
        // Clean URL and redirect
        $url = preg_replace('/\?page=\d+/', '', request()->header('Referer'));
        $url = preg_replace('/&page=\d+/', '', $url);
        return redirect($url);
    }

    public function updatedEndDate()
    {
        $this->resetPage();
        
        // Store end date in session
        session(['end_date' => $this->end_date]);
        
        // Clean URL and redirect
        $url = preg_replace('/\?page=\d+/', '', request()->header('Referer'));
        $url = preg_replace('/&page=\d+/', '', $url);
        return redirect($url);
    }

    public function updatedPaginate()
    {
        $this->resetPage();
        
        // Store pagination value in session
        session(['paginate' => $this->paginate]);
        
        // Clean URL and redirect
        $url = preg_replace('/\?page=\d+/', '', request()->header('Referer'));
        $url = preg_replace('/&page=\d+/', '', $url);
        return redirect($url);
    }

    public function updatedSelectedDay()
    {
        $this->resetPage();
        
        // Store selected day in session
        session(['selected_day' => $this->selected_day]);
        
        // Clean URL and redirect
        $url = preg_replace('/\?page=\d+/', '', request()->header('Referer'));
        $url = preg_replace('/&page=\d+/', '', $url);
        return redirect($url);
    }

    public function updatedSelectedMonth()
    {
        $this->resetPage();
        
        // Store selected month in session
        session(['selected_month' => $this->selected_month]);
        
        // Clean URL and redirect
        $url = preg_replace('/\?page=\d+/', '', request()->header('Referer'));
        $url = preg_replace('/&page=\d+/', '', $url);
        return redirect($url);
    }

    public function updated($property)
    {
        if (in_array($property, ['timeRange', 'selectedUser', 'selectedRideType', 'classification'])) {
            // $this->dispatch('updateChart', $this->getChartData());
        }
    }
    

    

    public function render()
    {
        // Apply filters to fetch the rides without pagination for total price calculation
        $filteredRidesQuery = $this->buildFilteredRidesQuery()
            ->orderBy('created_at', 'desc');

        // Calculate the total price based on the filtered rides
        $this->totalPrice = $filteredRidesQuery->sum('totalPrice');

        // Fetch the rides with pagination (for display purposes)
        $rides = $filteredRidesQuery->paginate($this->paginate);

        // Fetch the distinct values for filtering
        $users = Rental::distinct()->pluck('user');
        $rideTypes = Rental::when($this->selectedUser, fn($query) => $query->where('user', $this->selectedUser))
            ->distinct()->pluck('rideType');
        $classifications = Rental::query()
            ->when($this->selectedUser !== '', fn($query) => $query->where('user', $this->selectedUser))
            ->when($this->selectedRideType !== '', fn($query) => $query->where('rideType', $this->selectedRideType))
            ->distinct()->pluck('classification');

        // Dispatch chart update after render
        $this->dispatch('updateChart');

        return view('livewire.sales', [
            'rides' => $rides,
            'users' => $users,
            'rideTypes' => $rideTypes,
            'classifications' => $classifications,
        ]);
    }
    
    protected function applyDateRangeFilter($query)
    {
        return match($this->dateRange) {
            'today' => $query->whereDate('created_at', Carbon::today()),
            'yesterday' => $query->whereDate('created_at', Carbon::yesterday()),
            'select_day' => $query->when($this->selected_day, function($query) {
                return $query->whereDate('created_at', $this->selected_day);
            }),
            'this_week' => $query->whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]),
            'last_week' => $query->whereBetween('created_at', [Carbon::now()->subWeek()->startOfWeek(), Carbon::now()->subWeek()->endOfWeek()]),
            'this_month' => $query->whereMonth('created_at', Carbon::now()->month),
            'last_month' => $query->whereMonth('created_at', Carbon::now()->subMonth()->month),
            'this_year' => $query->whereYear('created_at', Carbon::now()->year),
            'last_year' => $query->whereYear('created_at', Carbon::now()->subYear()->year),
            'custom' => $query->when($this->start_date && $this->end_date, function ($query) {
                return $query->whereDate('created_at', '>=', $this->start_date)
                             ->whereDate('created_at', '<=', $this->end_date);
            }),
            'select_month' => $query->when($this->selected_month, function($query) {
                try {
                    $date = Carbon::parse($this->selected_month . '-01');
                    return $query->whereYear('created_at', $date->year)
                                ->whereMonth('created_at', $date->month);
                } catch (\Exception $e) {
                    return $query;
                }
            }),
            default => $query
        };
    }

    protected function buildFilteredRidesQuery()
    {
        $query = Rental::query()
            ->when($this->selectedUser, fn($query) => $query->where('user', $this->selectedUser))
            ->when($this->selectedRideType, fn($query) => $query->where('rideType', $this->selectedRideType))
            ->when($this->classification, fn($query) => $query->where('classification', $this->classification));

        return $this->applyDateRangeFilter($query);
    }

    public function getAllRidesForChart()
    {
        $query = Rental::query();  // Assuming your model is named Ride

        // Apply the same filters as your paginated query
        if ($this->selectedUser) {
            $query->where('user', $this->selectedUser);
        }

        if ($this->selectedRideType) {
            $query->where('rideType', $this->selectedRideType);
        }

        if ($this->classification) {
            $query->where('classification', $this->classification);
        }

        // Apply date filters
        if ($this->dateRange) {
            switch ($this->dateRange) {
                case 'select_day':
                    $query->whereDate('created_at', $this->selected_day);
                    break;
                case 'today':
                    $query->whereDate('created_at', Carbon::today());
                    break;
                case 'yesterday':
                    $query->whereDate('created_at', Carbon::yesterday());
                    break;
                case 'this_week':
                    $query->whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]);
                    break;
                case 'last_week':
                    $query->whereBetween('created_at', [Carbon::now()->subWeek()->startOfWeek(), Carbon::now()->subWeek()->endOfWeek()]);
                    break;
                case 'this_month':
                    $query->whereMonth('created_at', Carbon::now()->month);
                    break;
                case 'last_month':
                    $query->whereMonth('created_at', Carbon::now()->subMonth()->month);
                    break;
                case 'this_year':
                    $query->whereYear('created_at', Carbon::now()->year);
                    break;
                case 'last_year':
                    $query->whereYear('created_at', Carbon::now()->subYear()->year);
                    break;
                case 'custom':
                    $query->whereBetween('created_at', [$this->start_date, $this->end_date]);
                    break;
                case 'select_month':
                    $date = Carbon::parse($this->selected_month . '-01');
                    $query->whereYear('created_at', $date->year)
                            ->whereMonth('created_at', $date->month);
                    break;
            }
        }

        return $query->get();
    }

    // public function applyFilter()
    // {
    //     // This will trigger a re-render with the current filter values
    //     $this->dispatch('updateChart');
        
    //     // Force a refresh of the data
    //     $this->render();
    // }

    public function refreshPage()
    {
        $this->dispatch('refreshPage');
    }

    public function getChartLabels()
    {
        $dailyTotals = $this->getAllRidesForChart()
            ->groupBy(function($ride) {
                return Carbon::parse($ride->created_at)->format('Y-m-d');
            })
            ->map(function($group) {
                return $group->sum('totalPrice');
            })
            ->sortKeys();
        
        return $dailyTotals->mapWithKeys(function($total, $date) {
            return [Carbon::parse($date)->format('M d, Y') => $total];
        })->keys()->toArray();
    }

    public function getChartData()
    {
        $dailyTotals = $this->getAllRidesForChart()
            ->groupBy(function($ride) {
                return Carbon::parse($ride->created_at)->format('Y-m-d');
            })
            ->map(function($group) {
                return $group->sum('totalPrice');
            })
            ->sortKeys();
        
        return $dailyTotals->mapWithKeys(function($total, $date) {
            return [Carbon::parse($date)->format('M d, Y') => $total];
        })->values()->toArray();
    }
}
