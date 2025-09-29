<!-- Operational Summary Report -->
<div class="space-y-6">
    <!-- Key Metrics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
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

        <div class="bg-gradient-to-r from-green-500 to-emerald-600 p-4 rounded-lg text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-green-100 text-sm">Total Duration</p>
                    <p class="text-2xl font-bold">
                        @if(isset($reportData['totalDuration']))
                            @if($reportData['totalDuration'] >= 60)
                                {{ intdiv($reportData['totalDuration'], 60) }}hr{{ $reportData['totalDuration'] % 60 > 0 ? ' ' . ($reportData['totalDuration'] % 60) . 'min' : '' }}
                            @else
                                {{ $reportData['totalDuration'] }}min
                            @endif
                        @else
                            0min
                        @endif
                    </p>
                </div>
                <svg class="w-8 h-8 text-green-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
        </div>

        <div class="bg-gradient-to-r from-purple-500 to-pink-600 p-4 rounded-lg text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-purple-100 text-sm">Avg Duration</p>
                    <p class="text-2xl font-bold">
                        @if(isset($reportData['averageDuration']))
                            @if($reportData['averageDuration'] >= 60)
                                {{ intdiv($reportData['averageDuration'], 60) }}hr{{ $reportData['averageDuration'] % 60 > 0 ? ' ' . ($reportData['averageDuration'] % 60) . 'min' : '' }}
                            @else
                                {{ round($reportData['averageDuration']) }}min
                            @endif
                        @else
                            0min
                        @endif
                    </p>
                </div>
                <svg class="w-8 h-8 text-purple-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                </svg>
            </div>
        </div>

        <div class="bg-gradient-to-r from-orange-500 to-red-600 p-4 rounded-lg text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-orange-100 text-sm">Life Jackets Used</p>
                    <p class="text-2xl font-bold">{{ $reportData['lifeJacketUsage'] ?? 0 }}</p>
                </div>
                <svg class="w-8 h-8 text-orange-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                </svg>
            </div>
        </div>
    </div>

    <!-- Most Popular Ride Types -->
    @if(isset($reportData['popularRideTypes']) && $reportData['popularRideTypes']->count() > 0)
    <div class="bg-gray-50 rounded-lg p-6">
        <h4 class="text-lg font-semibold text-gray-800 mb-4">Most Popular Ride Types</h4>
        <div class="space-y-3">
            @foreach($reportData['popularRideTypes']->take(5) as $rideType => $count)
            <div class="flex items-center justify-between bg-white p-3 rounded-lg shadow-sm">
                <span class="font-medium text-gray-700">{{ $rideType }}</span>
                <div class="flex items-center">
                    <div class="w-32 bg-gray-200 rounded-full h-2 mr-3">
                        <div class="bg-blue-600 h-2 rounded-full" style="width: {{ ($count / $reportData['popularRideTypes']->max()) * 100 }}%"></div>
                    </div>
                    <span class="font-bold text-blue-600">{{ $count }} rentals</span>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    <!-- Ride Utilization by Identifier -->
    @if(isset($reportData['rideUtilization']) && $reportData['rideUtilization']->count() > 0)
    <div class="bg-gray-50 rounded-lg p-4 sm:p-6">
        <h4 class="text-lg font-semibold text-gray-800 mb-4">Ride Utilization by Identifier</h4>
        <div class="space-y-3">
            @foreach($reportData['rideUtilization']->take(8) as $rideIdentifier => $data)
            <div class="bg-white p-3 rounded-lg shadow-sm">
                <!-- Mobile: Stack vertically -->
                <div class="block sm:hidden">
                    <div class="flex items-center mb-2">
                        <div class="w-3 h-3 rounded-full mr-3 
                            @if($data['rentals'] >= 10) bg-green-500
                            @elseif($data['rentals'] >= 5) bg-yellow-500
                            @else bg-red-500
                            @endif">
                        </div>
                        <div class="flex-1 min-w-0">
                            <span class="font-medium text-gray-700 text-sm break-words">{{ $rideIdentifier }}</span>
                            <p class="text-xs text-gray-500 break-words">{{ $data['classification'] }} • {{ $data['ride_type'] }}</p>
                        </div>
                    </div>
                    <div class="flex items-center justify-between">
                        <div class="flex-1 mr-3">
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div class="bg-cyan-600 h-2 rounded-full" style="width: {{ ($data['rentals'] / $reportData['rideUtilization']->max('rentals')) * 100 }}%"></div>
                            </div>
                        </div>
                        <div class="text-right">
                            <span class="font-bold text-cyan-600 text-sm">{{ $data['rentals'] }} rentals</span>
                            <p class="text-xs text-gray-500">
                                @if($data['total_duration'] >= 60)
                                    {{ intdiv($data['total_duration'], 60) }}hr{{ $data['total_duration'] % 60 > 0 ? ' ' . ($data['total_duration'] % 60) . 'min' : '' }}
                                @else
                                    {{ $data['total_duration'] }}min
                                @endif
                            </p>
                        </div>
                    </div>
                </div>
                
                <!-- Desktop: Horizontal layout -->
                <div class="hidden sm:flex items-center justify-between">
                    <div class="flex items-center">
                        <div class="w-3 h-3 rounded-full mr-3 
                            @if($data['rentals'] >= 10) bg-green-500
                            @elseif($data['rentals'] >= 5) bg-yellow-500
                            @else bg-red-500
                            @endif">
                        </div>
                        <div>
                            <span class="font-medium text-gray-700">{{ $rideIdentifier }}</span>
                            <p class="text-xs text-gray-500">{{ $data['classification'] }} • {{ $data['ride_type'] }}</p>
                        </div>
                    </div>
                    <div class="flex items-center">
                        <div class="w-32 bg-gray-200 rounded-full h-2 mr-3">
                            <div class="bg-cyan-600 h-2 rounded-full" style="width: {{ ($data['rentals'] / $reportData['rideUtilization']->max('rentals')) * 100 }}%"></div>
                        </div>
                        <div class="text-right">
                            <span class="font-bold text-cyan-600">{{ $data['rentals'] }} rentals</span>
                            <p class="text-xs text-gray-500">
                                @if($data['total_duration'] >= 60)
                                    {{ intdiv($data['total_duration'], 60) }}hr{{ $data['total_duration'] % 60 > 0 ? ' ' . ($data['total_duration'] % 60) . 'min' : '' }}
                                @else
                                    {{ $data['total_duration'] }}min
                                @endif
                            </p>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        <div class="mt-4 p-3 bg-blue-50 rounded-lg">
            <div class="flex items-center text-sm text-blue-700">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <span><strong>Legend:</strong> Green (10+ rentals) • Yellow (5-9 rentals) • Red (1-4 rentals)</span>
            </div>
        </div>
    </div>
    @endif

    <!-- Staff Performance -->
    @if(isset($reportData['staffPerformance']) && $reportData['staffPerformance']->count() > 0)
    <div class="bg-gray-50 rounded-lg p-6">
        <h4 class="text-lg font-semibold text-gray-800 mb-4">Staff Performance</h4>
        <div class="overflow-x-auto">
            <table class="min-w-full bg-white rounded-lg shadow-sm">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Staff Member</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Rentals</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Revenue</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Avg Duration</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($reportData['staffPerformance'] as $staff => $performance)
                    <tr>
                        <td class="px-4 py-3 text-sm font-medium text-gray-900">{{ $staff }}</td>
                        <td class="px-4 py-3 text-sm text-gray-900">{{ $performance['rentals'] }}</td>
                        <td class="px-4 py-3 text-sm text-gray-900">₱{{ number_format($performance['revenue'], 2) }}</td>
                        <td class="px-4 py-3 text-sm text-gray-900">
                            @if($performance['avg_duration'] >= 60)
                                {{ intdiv($performance['avg_duration'], 60) }}hr{{ $performance['avg_duration'] % 60 > 0 ? ' ' . ($performance['avg_duration'] % 60) . 'min' : '' }}
                            @else
                                {{ round($performance['avg_duration']) }}min
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    <!-- Peak Hours Analysis -->
    @if(isset($reportData['peakHours']) && $reportData['peakHours']->count() > 0)
    <div class="bg-gray-50 rounded-lg p-6">
        <h4 class="text-lg font-semibold text-gray-800 mb-4">Peak Hours Analysis</h4>
        <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-3">
            @foreach($reportData['peakHours']->take(12) as $hour => $count)
            <div class="bg-white p-3 rounded-lg shadow-sm text-center">
                <p class="text-sm font-medium text-gray-600">{{ \Carbon\Carbon::createFromFormat('H', $hour)->format('h:i A') }}</p>
                <p class="text-lg font-bold text-blue-600">{{ $count }}</p>
                <p class="text-xs text-gray-500">rentals</p>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    <!-- Operational Insights -->
    <div class="bg-gradient-to-r from-indigo-500 to-purple-600 rounded-lg p-6 text-white">
        <h4 class="text-lg font-semibold mb-4">Operational Insights</h4>
        <div class="space-y-2">
            @if(isset($reportData['popularRideTypes']) && $reportData['popularRideTypes']->count() > 0)
            <p class="text-indigo-100">
                <strong>Most Popular:</strong> {{ $reportData['popularRideTypes']->keys()->first() }} with {{ $reportData['popularRideTypes']->first() }} rentals
            </p>
            @endif
            
            @if(isset($reportData['peakHours']) && $reportData['peakHours']->count() > 0)
            <p class="text-indigo-100">
                <strong>Peak Hour:</strong> {{ \Carbon\Carbon::createFromFormat('H', $reportData['peakHours']->keys()->first())->format('h:i A') }} with {{ $reportData['peakHours']->first() }} rentals
            </p>
            @endif
            
            @if(isset($reportData['staffPerformance']) && $reportData['staffPerformance']->count() > 0)
            @php
                $topStaff = $reportData['staffPerformance']->sortByDesc('rentals')->first();
            @endphp
            <p class="text-indigo-100">
                <strong>Top Performer:</strong> {{ $reportData['staffPerformance']->sortByDesc('rentals')->keys()->first() }} with {{ $topStaff['rentals'] }} rentals
            </p>
            @endif
            
            @if(isset($reportData['averageDuration']))
            <p class="text-indigo-100">
                <strong>Average Rental Duration:</strong> 
                @if($reportData['averageDuration'] >= 60)
                    {{ intdiv($reportData['averageDuration'], 60) }} hours {{ $reportData['averageDuration'] % 60 > 0 ? $reportData['averageDuration'] % 60 . ' minutes' : '' }}
                @else
                    {{ round($reportData['averageDuration']) }} minutes
                @endif
            </p>
            @endif
        </div>
    </div>

    <!-- Recent Activity -->
    @if(isset($reportData['rentals']) && $reportData['rentals']->count() > 0)
    <div class="bg-gray-50 rounded-lg p-6">
        <h4 class="text-lg font-semibold text-gray-800 mb-4">Recent Activity</h4>
        <div class="space-y-3">
            @foreach($reportData['rentals']->take(5) as $rental)
            <div class="flex items-center justify-between bg-white p-3 rounded-lg shadow-sm">
                <div class="flex items-center">
                    <div class="w-2 h-2 bg-green-500 rounded-full mr-3"></div>
                    <div>
                        <p class="text-sm font-medium text-gray-900">
                            {{ $rental->ride->classification->rideType->name ?? $rental->ride_type_name_at_time ?? 'Unknown' }} rental
                        </p>
                        <p class="text-xs text-gray-500">
                            <strong>Ride:</strong> {{ $rental->ride_identifier_at_time ?? $rental->ride->identifier ?? 'Unknown' }} • 
                            by {{ $rental->user_name_at_time ?? 'Unknown' }} • {{ \Carbon\Carbon::parse($rental->created_at)->format('M d, h:i A') }}
                        </p>
                    </div>
                </div>
                <div class="text-right">
                    <p class="text-sm font-medium text-gray-900">
                        @if($rental->duration_minutes >= 60)
                            {{ intdiv($rental->duration_minutes, 60) }}hr{{ $rental->duration_minutes % 60 > 0 ? ' ' . ($rental->duration_minutes % 60) . 'min' : '' }}
                        @else
                            {{ $rental->duration_minutes }}min
                        @endif
                    </p>
                    <p class="text-xs text-gray-500">₱{{ number_format($rental->computed_total, 2) }}</p>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif
</div>
