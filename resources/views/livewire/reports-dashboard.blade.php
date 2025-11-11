<div class="w-full">
    <!-- Report Controls Section -->
    <div class="bg-white rounded-xl shadow-lg overflow-hidden border border-gray-100 mb-6">
        <div class="bg-gradient-to-r from-cyan-500 to-blue-600 p-4 sm:p-6">
            <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4">
                <div>
                    <h3 class="text-lg sm:text-xl font-bold text-white">Report Controls</h3>
                    <p class="text-white/80 text-xs sm:text-sm">Filter and customize your reports</p>
                </div>
                <button wire:click="clearFilters" 
                        class="inline-flex items-center justify-center bg-white/20 text-white py-2 px-3 sm:px-4 rounded-lg font-medium text-sm sm:text-base
                               transform transition-all duration-200 hover:-translate-y-1 
                               hover:shadow-lg hover:bg-white/30 w-full sm:w-auto">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                    Clear Filters
                </button>
            </div>
        </div>
        
        <div class="p-4 sm:p-6">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5 gap-3 sm:gap-4 mb-4 sm:mb-6">
                <!-- Report Type Selection -->
                <div>
                    <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-1 sm:mb-2">Report Type</label>
                    <select wire:model.live="reportType" class="w-full px-2 sm:px-3 py-2 text-sm sm:text-base border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="financial">Financial Summary</option>
                        <option value="operational">Operational Summary</option>
                    </select>
                </div>

                <!-- Date Range Selection -->
                <div>
                    <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-1 sm:mb-2">Date Range</label>
                    <select wire:model.live="dateRange" class="w-full px-2 sm:px-3 py-2 text-sm sm:text-base border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="today">Today</option>
                        <option value="yesterday">Yesterday</option>
                        <option value="select_day">Select Day</option>
                        <option value="this_week">This Week</option>
                        <option value="last_week">Last Week</option>
                        <option value="this_month">This Month</option>
                        <option value="last_month">Last Month</option>
                        <option value="select_month">Select Month</option>
                        <option value="this_year">This Year</option>
                        <option value="last_year">Last Year</option>
                        <option value="select_year">Select Year</option>
                        <option value="custom">Custom Range</option>
                    </select>
                </div>

                <!-- Select Day -->
                @if($dateRange === 'select_day')
                <div>
                    <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-1 sm:mb-2">Select Day</label>
                    <div x-data="{ fp: null, open() { this.fp && this.fp.open() } }"
                         x-init="fp = flatpickr($refs.dayInput, {
                             dateFormat: 'Y-m-d',
                             positionElement: $refs.dayBtn,
                             onChange: function(selectedDates, dateStr){ $wire.set('selectedDay', dateStr) }
                         })">
                        <input x-ref="dayInput" type="text" class="sr-only" />
                        <button x-ref="dayBtn" type="button" @click="open()"
                                class="w-full border border-blue-200 rounded-lg px-3 py-2.5 text-gray-700 bg-white hover:bg-blue-50 focus:outline-none focus:ring-2 focus:ring-blue-300 text-sm">
                            {{ $selectedDay ? $selectedDay : 'Select Date' }}
                        </button>
                    </div>
                </div>
                @endif

                <!-- Select Month -->
                @if($dateRange === 'select_month')
                <div>
                    <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-1 sm:mb-2">Select Month</label>
                    <!-- Mobile/Phone View: Select Dropdown -->
                    <select wire:model.live="selectedMonth" class="w-full px-2 sm:px-3 py-2 text-sm sm:text-base border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent sm:hidden">
                        <option value="">Select Month</option>
                        @php
                            $monthNames = ['', 'January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
                        @endphp
                        @for($month = 1; $month <= 12; $month++)
                            @php
                                $value = sprintf('%02d', $month);
                                $label = $monthNames[$month];
                            @endphp
                            <option value="{{ $value }}">{{ $label }}</option>
                        @endfor
                    </select>
                    
                    <!-- Desktop View: Button with Flatpickr -->
                    <div class="hidden sm:block" x-data="{ fp: null, open() { this.fp && this.fp.open() } }"
                         x-init="fp = flatpickr($refs.monthInput, {
                             plugins: [new monthSelectPlugin({ shorthand: true, dateFormat: 'Y-m', altFormat: 'F Y', theme: 'material_blue' })],
                             dateFormat: 'Y-m',
                             positionElement: $refs.monthBtn,
                             onChange: function(selectedDates, dateStr){ $wire.set('selectedMonth', dateStr) }
                         })">
                        <input x-ref="monthInput" type="text" class="sr-only" />
                        <button x-ref="monthBtn" type="button" @click="open()"
                                class="w-full border border-blue-200 rounded-lg px-3 py-2.5 text-gray-700 bg-white hover:bg-blue-50 focus:outline-none focus:ring-2 focus:ring-blue-300 text-sm">
                            {{ $this->getSelectedMonthName() }}
                        </button>
                    </div>
                </div>
                @endif

                <!-- Select Year -->
                @if($dateRange === 'select_year')
                <div>
                    <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-1 sm:mb-2">Select Year</label>
                    <select wire:model.live="selectedYear" class="w-full px-2 sm:px-3 py-2 text-sm sm:text-base border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="">Select Year</option>
                        @for($year = date('Y'); $year >= 2020; $year--)
                            <option value="{{ $year }}">{{ $year }}</option>
                        @endfor
                    </select>
                </div>
                @endif

                <!-- Custom Date Range -->
                @if($dateRange === 'custom')
                <div>
                    <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-1 sm:mb-2">Start Date</label>
                    <input type="date" wire:model.live="startDate" 
                           class="w-full px-2 sm:px-3 py-2 text-sm sm:text-base border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>
                <div>
                    <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-1 sm:mb-2">End Date</label>
                    <input type="date" wire:model.live="endDate" 
                           class="w-full px-2 sm:px-3 py-2 text-sm sm:text-base border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>
                @endif

                <!-- Staff Filter -->
                <div>
                    <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-1 sm:mb-2">Staff Member</label>
                    <select wire:model.live="selectedUser" class="w-full px-2 sm:px-3 py-2 text-sm sm:text-base border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="">All Staff</option>
                        @foreach($this->getStaffList() as $staff)
                            <option value="{{ $staff }}">{{ $staff }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Ride Type Filter -->
                <div>
                    <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-1 sm:mb-2">Ride Type</label>
                    <select wire:model.live="selectedRideType" class="w-full px-2 sm:px-3 py-2 text-sm sm:text-base border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="">All Ride Types</option>
                        @foreach($this->getRideTypesList() as $rideType)
                            <option value="{{ $rideType }}">{{ $rideType }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Classification Filter -->
                <div>
                    <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-1 sm:mb-2">Classification</label>
                    <select wire:model.live="classification" 
                            class="w-full px-2 sm:px-3 py-2 text-sm sm:text-base border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent
                                   {{ $selectedRideType === '' ? 'bg-gray-100 text-gray-500' : '' }}"
                            {{ $selectedRideType === '' ? 'disabled' : '' }}>
                        <option value="">{{ $selectedRideType === '' ? 'Select a Ride Type first' : 'All Classifications' }}</option>
                        @foreach($this->getClassificationsList() as $classification)
                            <option value="{{ $classification }}">{{ $classification }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Ride Identifier Filter -->
                <div>
                    <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-1 sm:mb-2">Ride Identifier</label>
                    <select wire:model.live="selectedRideIdentifier" 
                            class="w-full px-2 sm:px-3 py-2 text-sm sm:text-base border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent
                                   {{ $classification === '' ? 'bg-gray-100 text-gray-500' : '' }}"
                            {{ $classification === '' ? 'disabled' : '' }}>
                        <option value="">{{ $classification === '' ? 'Select a Classification first' : 'All Ride Identifiers' }}</option>
                        @foreach($this->getRideIdentifiersList() as $identifier)
                            <option value="{{ $identifier }}">{{ $identifier }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Export Options -->
            <div class="flex justify-center gap-3">
                <button wire:click="exportReport" 
                        class="inline-flex items-center justify-center bg-[#00A3E0] text-white py-2 px-6 rounded-lg font-medium text-sm sm:text-base
                               transform transition-all duration-200 hover:-translate-y-1 
                               hover:shadow-lg hover:bg-[#0093CC]">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    Export CSV
                </button>
                <button wire:click="exportPdf" 
                        class="inline-flex items-center justify-center bg-red-600 text-white py-2 px-6 rounded-lg font-medium text-sm sm:text-base
                               transform transition-all duration-200 hover:-translate-y-1 
                               hover:shadow-lg hover:bg-red-700">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                    </svg>
                    Export PDF
                </button>
            </div>
        </div>
    </div>

    <!-- Report Display Section -->
    @if(!empty($reportData))
    <div class="bg-white rounded-xl shadow-lg overflow-hidden border border-gray-100">
        <!-- Report Header -->
        <div class="bg-gradient-to-r from-blue-500 to-cyan-600 p-4 sm:p-6">
            <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-3 sm:gap-4">
                <div>
                    <h3 class="text-lg sm:text-2xl font-bold text-white">
                        {{ ucfirst($reportType) }} Report
                    </h3>
                    <p class="text-white/80 text-xs sm:text-sm">
                        Generated on {{ now()->format('M d, Y \a\t h:i A') }}
                    </p>
                    <p class="text-white/80 text-xs sm:text-sm">
                        Period: {{ $this->getPeriodDescription() }}
                    </p>
                </div>
            </div>
        </div>

        <!-- Report Content -->
        <div class="p-4 sm:p-6">
            @if($reportType === 'financial')
                @include('reports.financial-summary')
            @else
                @include('reports.operational-summary')
            @endif
        </div>
    </div>
    @else
    <!-- No Data Message -->
    <div class="bg-white rounded-xl shadow-lg overflow-hidden border border-gray-100">
        <div class="p-6 sm:p-12 text-center">
            <svg class="mx-auto h-10 w-10 sm:h-12 sm:w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
            </svg>
            <h3 class="mt-2 text-sm sm:text-base font-medium text-gray-900">No data available</h3>
            <p class="mt-1 text-xs sm:text-sm text-gray-500">Try adjusting your filters to see report data.</p>
        </div>
    </div>
    @endif


</div>

<!-- Flatpickr Scripts -->
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/plugins/monthSelect/index.js"></script>
