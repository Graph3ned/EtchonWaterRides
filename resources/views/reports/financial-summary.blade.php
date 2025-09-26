<!-- Financial Summary Report -->
<div class="space-y-6">
    <!-- Key Metrics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-gradient-to-r from-green-500 to-emerald-600 p-4 rounded-lg text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-green-100 text-sm">Total Revenue</p>
                    <p class="text-2xl font-bold">{{ $reportData['totalRevenue'] ? '₱' . number_format($reportData['totalRevenue'], 2) : '₱0.00' }}</p>
                </div>
                <svg class="w-8 h-8 text-green-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                </svg>
            </div>
        </div>

        <div class="bg-gradient-to-r from-blue-500 to-cyan-600 p-4 rounded-lg text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-blue-100 text-sm">Total Rentals</p>
                    <p class="text-2xl font-bold">{{ $reportData['totalRentals'] ?? 0 }}</p>
                </div>
                <svg class="w-8 h-8 text-blue-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                </svg>
            </div>
        </div>

        <div class="bg-gradient-to-r from-purple-500 to-pink-600 p-4 rounded-lg text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-purple-100 text-sm">Avg Transaction</p>
                    <p class="text-2xl font-bold">{{ $reportData['averageTransaction'] ? '₱' . number_format($reportData['averageTransaction'], 2) : '₱0.00' }}</p>
                </div>
                <svg class="w-8 h-8 text-purple-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                </svg>
            </div>
        </div>

        <div class="bg-gradient-to-r from-orange-500 to-red-600 p-4 rounded-lg text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-orange-100 text-sm">Growth Rate</p>
                    <p class="text-2xl font-bold">
                        @if(isset($reportData['revenueGrowth']))
                            @if($reportData['revenueGrowth'] > 0)
                                <span class="text-green-200">+{{ $reportData['revenueGrowth'] }}%</span>
                            @elseif($reportData['revenueGrowth'] < 0)
                                <span class="text-red-200">{{ $reportData['revenueGrowth'] }}%</span>
                            @else
                                <span class="text-orange-200">0%</span>
                            @endif
                        @else
                            <span class="text-orange-200">N/A</span>
                        @endif
                    </p>
                </div>
                <svg class="w-8 h-8 text-orange-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z"></path>
                </svg>
            </div>
        </div>
    </div>

    <!-- Revenue by Ride Type -->
    @if(isset($reportData['revenueByRideType']) && $reportData['revenueByRideType']->count() > 0)
    <div class="bg-gray-50 rounded-lg p-6">
        <h4 class="text-lg font-semibold text-gray-800 mb-4">Revenue by Ride Type</h4>
        <div class="space-y-3">
            @foreach($reportData['revenueByRideType'] as $rideType => $revenue)
            <div class="flex items-center justify-between bg-white p-3 rounded-lg shadow-sm">
                <span class="font-medium text-gray-700">{{ $rideType }}</span>
                <span class="font-bold text-green-600">₱{{ number_format($revenue, 2) }}</span>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    <!-- Revenue by Staff -->
    @if(isset($reportData['revenueByStaff']) && $reportData['revenueByStaff']->count() > 0)
    <div class="bg-gray-50 rounded-lg p-6">
        <h4 class="text-lg font-semibold text-gray-800 mb-4">Revenue by Staff Member</h4>
        <div class="space-y-3">
            @foreach($reportData['revenueByStaff'] as $staff => $revenue)
            <div class="flex items-center justify-between bg-white p-3 rounded-lg shadow-sm">
                <span class="font-medium text-gray-700">{{ $staff }}</span>
                <span class="font-bold text-blue-600">₱{{ number_format($revenue, 2) }}</span>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    <!-- Period Comparison -->
    @if(isset($reportData['previousPeriod']))
    <div class="bg-gray-50 rounded-lg p-6">
        <h4 class="text-lg font-semibold text-gray-800 mb-4">Period Comparison</h4>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="bg-white p-4 rounded-lg shadow-sm">
                <h5 class="font-medium text-gray-600 mb-2">Current Period</h5>
                <p class="text-2xl font-bold text-green-600">₱{{ number_format($reportData['totalRevenue'], 2) }}</p>
                <p class="text-sm text-gray-500">{{ $reportData['totalRentals'] }} rentals</p>
            </div>
            <div class="bg-white p-4 rounded-lg shadow-sm">
                <h5 class="font-medium text-gray-600 mb-2">Previous Period</h5>
                <p class="text-2xl font-bold text-gray-600">₱{{ number_format($reportData['previousPeriod']['revenue'], 2) }}</p>
                <p class="text-sm text-gray-500">{{ $reportData['previousPeriod']['rentals'] }} rentals</p>
            </div>
        </div>
    </div>
    @endif

</div>
