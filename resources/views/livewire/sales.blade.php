<div class="min-h-full p-4">
  <div class="w-full rounded-lg relative overflow-hidden">

    

    <!-- Decorative wave elements -->
    <div class="absolute top-0 left-0 right-0 h-1"></div>

    <div class="w-full">
  

    <div class="flex flex-col lg:flex-row lg:space-x-4">
      <!-- Filter options section -->
      <div class="w-full lg:w-1/4 py-4">
        <div class="bg-white rounded-xl shadow-lg overflow-hidden border border-gray-100 transition-all duration-300 hover:shadow-xl">
          <!-- Card Header -->
          <div class="bg-gradient-to-r from-cyan-500 to-blue-600 px-6 py-4">
            <div class="flex justify-between items-center">
              <h3 class="text-white text-lg font-semibold tracking-wide">Filter Options</h3>
              <button wire:click="resetFilter" 
                      class="inline-flex items-center px-4 py-1.5 bg-white/20 hover:bg-white/30 active:bg-white/40 rounded-lg 
                             transition-all duration-200 text-white text-sm font-medium backdrop-blur-sm group">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2 group-hover:-rotate-45 transition-transform duration-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                </svg>
                Reset
              </button>
            </div>
          </div>

          <!-- Card Body -->
          <div class="p-6 space-y-6">
            <!-- Staff Select -->
            <div class="space-y-2">
              <label class="flex items-center text-gray-700 font-medium text-sm">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                </svg>
                Staff
              </label>
              <select wire:model.live="selectedUser" 
                      class="w-full text-sm rounded-lg border-gray-200 bg-gray-50 focus:bg-white hover:bg-gray-50/80
                             focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all duration-200">
                <option value="">All Staff</option>
                @foreach ($users as $user)
                  <option value="{{ $user }}" class="py-2">{{ $user }}</option>
                @endforeach
              </select>
            </div>

            <!-- Ride Type & Classification Group -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <!-- Ride Type Select -->
              <div class="space-y-2">
                <label class="flex items-center text-gray-700 font-medium text-sm">
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                  </svg>
                  Ride Type
                </label>
                <select wire:model.live="selectedRideType" 
                        class="w-full text-sm rounded-lg border-gray-200 bg-gray-50 focus:bg-white hover:bg-gray-50/80
                               focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all duration-200">
                  <option value="">All Types</option>
                  @foreach ($rideTypes as $rideType)
                    <option value="{{ $rideType }}" class="py-2">{{ str_replace('_', ' ', $rideType) }}</option>
                  @endforeach
                </select>
              </div>

              <!-- Classification Select -->
              <div class="space-y-2">
                <label class="flex items-center text-gray-700 font-medium text-sm">
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                  </svg>
                  Classification
                </label>
                <select wire:model.live="classification" 
                        class="w-full text-sm rounded-lg border-gray-200 bg-gray-50 focus:bg-white hover:bg-gray-50/80
                               focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all duration-200">
                  <option value="">All Classifications</option>
                  @foreach ($classifications as $classificationOption)
                    <option value="{{ $classificationOption }}" class="py-2">{{ str_replace('_', ' ', $classificationOption) }}</option>
                  @endforeach
                </select>
              </div>
            </div>

            <!-- Identifier Select -->
            <div class="space-y-2">
              <label class="flex items-center text-gray-700 font-medium text-sm">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                </svg>
                Identifier
              </label>
              <select wire:model.live="selectedIdentifier" 
                      class="w-full text-sm rounded-lg border-gray-200 bg-gray-50 focus:bg-white hover:bg-gray-50/80
                             focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all duration-200">
                <option value="">All Identifiers</option>
                @foreach ($identifiers as $identifier)
                  <option value="{{ $identifier }}" class="py-2">{{ $identifier }}</option>
                @endforeach
              </select>
            </div>

            <!-- Date Range Section -->
            <div class="space-y-2">
              <label class="flex items-center text-gray-700 font-medium text-sm">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
                Date Range
              </label>
              <select wire:model.live="dateRange" 
                      class="w-full text-sm rounded-lg border-gray-200 bg-gray-50 focus:bg-white hover:bg-gray-50/80
                             focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all duration-200">
                <option value="">All Time</option>
                <option value="today">Today</option>
                <option value="select_day">Select Day</option>
                <option value="select_month">Select Month</option>
                <option value="this_month" selected>Current Month</option>
                <option value="custom">Custom Date Range</option>
              </select>

              @if($dateRange === 'select_month')
                <div class="mt-2">
                  <div x-data="{ fp: null, open() { this.fp && this.fp.open() } }"
                       x-init="fp = flatpickr($refs.monthInput, {
                           plugins: [new monthSelectPlugin({ shorthand: true, dateFormat: 'Y-m', altFormat: 'F Y', theme: 'material_blue' })],
                           dateFormat: 'Y-m',
                           positionElement: $refs.monthBtn,
                           onChange: function(selectedDates, dateStr){ $wire.set('selected_month', dateStr) }
                       })">
                    <input x-ref="monthInput" type="text" class="sr-only" />
                    <button x-ref="monthBtn" type="button" @click="open()"
                            class="w-full border border-blue-200 rounded-lg px-3 py-2.5 text-gray-700 bg-white hover:bg-blue-50 focus:outline-none focus:ring-2 focus:ring-blue-300 text-sm">
                      {{ $selected_month ? $selected_month : 'Select Month' }}
                    </button>
                  </div>
                </div>
              @endif

              @if($dateRange === 'select_day')
                <div class="mt-2">
                  <div x-data="{ fp: null, open() { this.fp && this.fp.open() } }"
                       x-init="fp = flatpickr($refs.dayInput, {
                           dateFormat: 'Y-m-d',
                           positionElement: $refs.dayBtn,
                           onChange: function(selectedDates, dateStr){ $wire.set('selected_day', dateStr) }
                       })">
                    <input x-ref="dayInput" type="text" class="sr-only" />
                    <button x-ref="dayBtn" type="button" @click="open()"
                            class="w-full border border-blue-200 rounded-lg px-3 py-2.5 text-gray-700 bg-white hover:bg-blue-50 focus:outline-none focus:ring-2 focus:ring-blue-300 text-sm">
                      {{ $selected_day ? $selected_day : 'Select Date' }}
                    </button>
                  </div>
                </div>
              @endif

              @if($dateRange === 'custom')
                <div class="mt-2 space-y-2">
                  <div x-data="{ fp: null, open() { this.fp && this.fp.open() } }"
                       x-init="fp = flatpickr($refs.startInput, {
                           dateFormat: 'Y-m-d',
                           positionElement: $refs.startBtn,
                           onChange: function(selectedDates, dateStr){ $wire.set('start_date', dateStr) }
                       })">
                    <input x-ref="startInput" type="text" class="sr-only" />
                    <button x-ref="startBtn" type="button" @click="open()"
                            class="w-full border border-blue-200 rounded-lg px-3 py-2.5 text-gray-700 bg-white hover:bg-blue-50 focus:outline-none focus:ring-2 focus:ring-blue-300 text-sm">
                      {{ $start_date ? $start_date : 'From Date' }}
                    </button>
                  </div>
                  <div x-data="{ fp: null, open() { this.fp && this.fp.open() } }"
                       x-init="fp = flatpickr($refs.endInput, {
                           dateFormat: 'Y-m-d',
                           positionElement: $refs.endBtn,
                           onChange: function(selectedDates, dateStr){ $wire.set('end_date', dateStr) }
                       })">
                    <input x-ref="endInput" type="text" class="sr-only" />
                    <button x-ref="endBtn" type="button" @click="open()"
                            class="w-full border border-blue-200 rounded-lg px-3 py-2.5 text-gray-700 bg-white hover:bg-blue-50 focus:outline-none focus:ring-2 focus:ring-blue-300 text-sm">
                      {{ $end_date ? $end_date : 'To Date' }}
                    </button>
                  </div>
                </div>
              @endif
            </div>
          </div>
        </div>
      </div>

      <!-- Sales Info Section -->
      <div class="w-full lg:w-3/4 py-4">
        <div class="bg-white rounded-xl shadow-lg overflow-hidden border border-gray-100 transition-all duration-300 hover:shadow-xl">
          <!-- Card Header with gradient background -->
          <div class="bg-gradient-to-r from-cyan-500 to-blue-600 p-6">
            <div class="flex flex-col md:flex-row md:justify-between md:items-center gap-4">
              <!-- Sales Info -->
              <div class="space-y-1">
                <p class="text-white/80 text-sm font-medium">Total Sales</p>
                <h5 class="text-3xl font-bold text-white">₱{{ number_format($totalPrice, 2) }}</h5>
              </div>
            </div>
          </div>

          <!-- Chart Container -->
          <div class="p-6">
            <div class="h-[300px]">
              <canvas id="salesChart" 
                      data-labels='@json($this->getChartLabels())'
                      data-values='@json($this->getChartData())'></canvas>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- ----------------table-------------------- -->
     
    <div class="w-full py-4 mb-4">
      <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4 mb-4">
        <div class="flex items-center space-x-2">
          <label class="flex items-center text-gray-700 font-medium text-sm">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16" />
            </svg>
            Show
          </label>
          <select wire:model.live="paginate" 
                  class="text-sm rounded-lg border-gray-200 bg-gray-50 focus:bg-white hover:bg-gray-50/80
                         focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all duration-200">
            <option value="10">10</option>
            <option value="15">15</option>
            <option value="20">20</option>
            <option value="50">50</option>
            <option value="100">100</option>
            <option value="150">150</option>
            <option value="200">200</option>
          </select>
          <span class="text-sm text-gray-600">entries</span>
        </div>
        <div class="flex justify-center sm:justify-end">
          <a href="{{ route('admin.generate-report') }}" 
             class="inline-flex items-center justify-center bg-[#00A3E0] text-white py-2.5 px-6 rounded-lg font-medium 
                    transform transition-all duration-200 hover:-translate-y-1 
                    hover:shadow-lg hover:bg-[#0093CC] max-w-[200px] sm:max-w-none">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
            Generate Report
          </a>
        </div>
      </div>

      <div class="overflow-x-auto rounded-lg shadow-lg border border-blue-100">
        <div class="max-h-[600px] overflow-y-auto">
          <table class="w-full min-w-max table-auto text-sm">
            <thead class="bg-gradient-to-r from-cyan-500 to-blue-600 text-white text-sm font-semibold sticky top-0 z-10">
              <tr class="border-b border-blue-400">
                <th class="px-4 py-3 text-left">No.</th>
                <th class="px-4 py-3 text-left">Staff</th>
                <th class="px-4 py-3 text-left">Ride Type & Classification</th>
                <th class="px-4 py-3 text-left hidden lg:table-cell">Identification</th>
                <th class="px-4 py-3 text-left hidden sm:table-cell">Duration</th>
                <th class="px-4 py-3 text-left hidden md:table-cell">Jackets</th>
                <th class="px-4 py-3 text-left">Total</th>
                <th class="px-4 py-3 text-left hidden md:table-cell">Start</th>
                <th class="px-4 py-3 text-left hidden md:table-cell">End</th>
                <th class="px-4 py-3 text-left hidden lg:table-cell">Date</th>
                <th class="px-4 py-3 text-left hidden lg:table-cell">Note</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-blue-100">
              @foreach ($rides as $ride)
                <tr class="bg-white hover:bg-blue-50 transition-colors duration-200">
                  <td class="px-4 py-3 text-gray-700">
                    {{ ($rides->total() - ($rides->currentPage() - 1) * $rides->perPage()) - $loop->iteration + 1 }}
                  </td>
                  <td class="px-4 py-3">
                    <div class="text-gray-700">{{ $ride->user_name_at_time }}</div>
                  </td>
                  <td class="px-4 py-3 lg:w-[200px] lg:min-w-[200px]">
                    <!-- Match Staff Dashboard style: Bold Ride Type then Classification -->
                    <div class="text-gray-700 font-bold text-lg">{{ $ride->ride->classification->rideType->name ?? ($ride->ride_type_name_at_time ?? 'Unknown') }}</div>
                    <div class="text-sm text-blue-600 font-medium">{{ $ride->ride->classification->name ?? ($ride->classification_name_at_time ?? 'Unknown') }}</div>
                    <!-- Mobile view details with improved styling -->
                    <div class="lg:hidden space-y-1.5 mt-2">
                        
                        <!-- Only show these details when their corresponding columns are hidden -->
                        <div class="space-y-1.5">
                            <!-- Duration - only when duration column is hidden -->
                            <div class="sm:hidden text-xs min-w-[150px]">
                                <span class="text-emerald-600 font-medium">Duration:</span>
                                <span class="text-gray-600">
                                    @if ($ride->duration_minutes >= 60)
                                        {{ intdiv($ride->duration_minutes, 60) }}hr{{ intdiv($ride->duration_minutes, 60) > 1 ? 's' : '' }}
                                        @if ($ride->duration_minutes % 60 > 0)
                                            {{ $ride->duration_minutes % 60 }}min
                                        @endif
                                    @else
                                        {{ $ride->duration_minutes }}min
                                    @endif
                                </span>
                            </div>

                            <!-- Date - matches hidden lg:table-cell and portrait mode -->
                            <div class="lg:hidden portrait:block landscape:hidden text-xs min-w-[150px]">
                                <span class="text-rose-600 font-medium">Date:</span>
                                <span class="text-gray-600">{{ \Carbon\Carbon::parse($ride->created_at)->format('M d, Y') }}</span>
                            </div>

                            <!-- Jackets - matches hidden md:table-cell -->
                            <div class="md:hidden text-xs min-w-[150px]">
                                <span class="text-emerald-600 font-medium">Jackets:</span>
                                <span class="text-gray-600">{{ $ride->life_jacket_quantity }}</span>
                            </div>

                            <!-- Time info - matches hidden md:table-cell -->
                            <div class="md:hidden text-xs space-y-1">
                                <div class="flex flex-col min-w-[150px]">
                                    <span class="text-indigo-600 font-medium">Start:</span>
                                    <span class="text-gray-600 ml-4">{{ \Carbon\Carbon::parse($ride->start_at)->format('h:i A') }}</span>
                                </div>
                                <div class="flex flex-col min-w-[150px]">
                                    <span class="text-indigo-600 font-medium">End:</span>
                                    <span class="text-gray-600 ml-4">{{ \Carbon\Carbon::parse($ride->end_at)->format('h:i A') }}</span>
                                </div>
                            </div>

                            <!-- Note - matches hidden lg:table-cell -->
                            <div class="lg:hidden text-xs min-w-[150px]">
                                <span class="text-purple-600 font-medium">Note:</span>
                                <span class="text-gray-600">{{ $ride->note ?? '-' }}</span>
                            </div>
                        </div>
                    </div>
                  </td>
                  <td class="px-4 py-3 text-gray-700 hidden lg:table-cell">{{ $ride->ride->identifier ?? $ride->ride_identifier_at_time ?? 'Unknown' }}</td>
                  <td class="px-4 py-3 text-gray-700 hidden sm:table-cell">
                    @if ($ride->duration_minutes >= 60)
                      {{ intdiv($ride->duration_minutes, 60) }}hr{{ intdiv($ride->duration_minutes, 60) > 1 ? 's' : '' }}
                      @if ($ride->duration_minutes % 60 > 0)
                        {{ $ride->duration_minutes % 60 }}min
                      @endif
                    @else
                      {{ $ride->duration_minutes }}min
                    @endif
                  </td>
                  <td class="px-4 py-3 text-gray-700 text-center hidden md:table-cell">{{ $ride->life_jacket_quantity }}</td>
                  <td class="px-4 py-3 text-gray-700">₱{{ number_format($ride->computed_total, 2) }}</td>
                  <td class="px-4 py-3 text-gray-700 hidden md:table-cell">{{ \Carbon\Carbon::parse($ride->start_at)->format('h:i A') }}</td>
                  <td class="px-4 py-3 text-gray-700 hidden md:table-cell">{{ \Carbon\Carbon::parse($ride->end_at)->format('h:i A') }}</td>
                  <td class="px-4 py-3 text-gray-700 hidden lg:table-cell">{{ \Carbon\Carbon::parse($ride->created_at)->format('M d, Y') }}</td>
                  <td class="px-4 py-3 text-gray-700 hidden lg:table-cell">{{ $ride->note ?? '-' }}</td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      </div>
      <!-- Pagination -->
      @if(count($rides))
        <div class="mt-6">
          {{ $rides->links('livewire.livewire-pagination-links') }}
        </div>
      @endif
    </div>
  </div>
</div>
<script>
// Use window object to avoid redeclaration issues
window.salesChartInstance = window.salesChartInstance || null;


// Listen for pagination events
document.addEventListener('livewire:pagination', function () {
    initChart();
});

// Listen for any Livewire updates
document.addEventListener('livewire:updated', function () {
    initChart();
});

Livewire.on('updateChart', () => {
    initChart();
});

function initChart() {
    // Add a small delay to ensure DOM is ready
    setTimeout(() => {
        if (window.salesChartInstance) {
            window.salesChartInstance.destroy();
        }

        const ctx = document.getElementById('salesChart');
        if (!ctx) return;

        // Get chart data from data attributes
        const chartLabels = JSON.parse(ctx.dataset.labels || '[]');
        const chartData = JSON.parse(ctx.dataset.values || '[]');

    window.salesChartInstance = new Chart(ctx, {
        type: 'line',
        data: {
            labels: chartLabels,
            datasets: [{
                label: 'Daily Sales',
                data: chartData,
                borderWidth: 3,
                borderColor: '#1A56DB',
                backgroundColor: (context) => {
                    const chart = context.chart;
                    const { ctx, chartArea } = chart;
                    if (!chartArea) return null;
                    
                    const gradient = ctx.createLinearGradient(0, chartArea.top, 0, chartArea.bottom);
                    gradient.addColorStop(0, 'rgba(26, 86, 219, 0.4)');
                    gradient.addColorStop(1, 'rgba(26, 86, 219, 0)');
                    return gradient;
                },
                fill: true,
                tension: 0.4,
                pointRadius: 0, // Hide points by default
                pointHoverRadius: 6, // Point size on hover
                pointHoverBackgroundColor: '#ffffff', // White center
                pointHoverBorderColor: '#1A56DB', // Blue border
                pointHoverBorderWidth: 3,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: {
                intersect: false,
                mode: 'index',
            },
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    backgroundColor: 'rgba(255, 255, 255, 0.95)',
                    titleColor: '#1f2937',
                    bodyColor: '#1f2937',
                    bodyFont: {
                        size: 13,
                        family: 'Inter, sans-serif',
                    },
                    titleFont: {
                        size: 13,
                        family: 'Inter, sans-serif',
                        weight: '600',
                    },
                    padding: 12,
                    boxPadding: 6,
                    borderColor: 'rgba(0, 0, 0, 0.1)',
                    borderWidth: 1,
                    callbacks: {
                        label: function(context) {
                            return '₱' + context.parsed.y.toLocaleString('en-US', {
                                minimumFractionDigits: 2,
                                maximumFractionDigits: 2
                            });
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: {
                        drawBorder: false,
                        color: 'rgba(0, 0, 0, 0.05)',
                        drawTicks: false,
                    },
                    ticks: {
                        callback: function(value) {
                            return '₱' + value.toLocaleString('en-US');
                        },
                        font: {
                            family: "Inter, sans-serif",
                            size: 11,
                            weight: '500'
                        },
                        color: '#6B7280',
                        padding: 10
                    },
                    border: {
                        display: false
                    }
                },
                x: {
                    grid: {
                        display: false
                    },
                    ticks: {
                        font: {
                            family: "Inter, sans-serif",
                            size: 11,
                            weight: '500'
                        },
                        color: '#6B7280',
                        maxRotation: 0,
                        padding: 10
                    },
                    border: {
                        display: false
                    }
                }
            },
            hover: {
                mode: 'index',
                intersect: false
            },
            elements: {
                line: {
                    borderJoinStyle: 'round'
                }
            }
        },
        plugins: [{
            id: 'hoverLine',
            beforeDraw: function(chart) {
                if (chart.tooltip._active && chart.tooltip._active.length) {
                    const activePoint = chart.tooltip._active[0];
                    const { ctx } = chart;
                    const { x } = activePoint.element;
                    const topY = chart.scales.y.top;
                    const bottomY = chart.scales.y.bottom;

                    ctx.save();
                    ctx.beginPath();
                    ctx.moveTo(x, topY);
                    ctx.lineTo(x, bottomY);
                    ctx.lineWidth = 1;
                    ctx.strokeStyle = 'rgba(0, 0, 0, 0.1)';
                    ctx.stroke();
                    ctx.restore();
                }
            }
        }]
    });
    }, 100); // 100ms delay to ensure DOM is ready
}

window.addEventListener('resize', function() {
    initChart();
});

// Listen for pagination clicks specifically
document.addEventListener('click', function(e) {
    if (e.target.closest('[wire\\:click]') && e.target.closest('[wire\\:click]').getAttribute('wire:click').includes('gotoPage')) {
        setTimeout(() => {
            initChart();
        }, 200);
    }
});

document.addEventListener('livewire:initialized', () => {
    Livewire.on('refreshPage', () => {
        location.reload();
    });
});
</script>


