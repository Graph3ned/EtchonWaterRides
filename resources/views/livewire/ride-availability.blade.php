<div class="min-h-full p-4" wire:poll.5s="refreshData">
    <div class="w-full rounded-lg relative overflow-hidden">
        <!-- Success/Error Messages -->
        @if (session()->has('success'))
            <div id="success-message" class="max-w-7xl mx-auto px-4 mb-4">
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                    <span class="block sm:inline">{{ session('success') }}</span>
                    <button 
                        onclick="hideMessage('success-message')" 
                        class="absolute top-0 bottom-0 right-0 px-4 py-3"
                    >
                        <svg class="fill-current h-6 w-6 text-green-500" role="button" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                            <title>Close</title>
                            <path d="M14.348 14.849a1.2 1.2 0 0 1-1.697 0L10 11.819l-2.651 3.029a1.2 1.2 0 1 1-1.697-1.697l2.758-3.15-2.759-3.152a1.2 1.2 0 1 1 1.697-1.697L10 8.183l2.651-3.031a1.2 1.2 0 1 1 1.697 1.697l-2.758 3.152 2.758 3.15a1.2 1.2 0 0 1 0 1.698z"/>
                        </svg>
                    </button>
                </div>
            </div>
        @endif

        <!-- Main Content Card -->
        <div class="max-w-7xl mx-auto">
            <div class="bg-white rounded-xl shadow-lg overflow-hidden border border-gray-100 transition-all duration-300 hover:shadow-xl">
                <!-- Header -->
                <div class="bg-gradient-to-r from-cyan-500 to-blue-600 p-6">
                    <div class="flex justify-between items-center">
                        <div class="flex items-center">
                            <h2 class="text-2xl font-bold text-white">Ride Availability Dashboard</h2>
                            <div class="ml-3 flex items-center">
                                <div class="w-2 h-2 bg-green-400 rounded-full animate-pulse"></div>
                                <span class="ml-2 text-sm text-white opacity-90">Live</span>
                            </div>
                        </div>
                        <button wire:click="refreshData(true)" 
                                class="px-4 py-2 bg-white bg-opacity-20 hover:bg-opacity-30 text-white rounded-lg transition-all duration-200 flex items-center">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                            </svg>
                            Refresh
                        </button>
                    </div>
                </div>

                <!-- Filters -->
                <div class="p-6 border-b border-gray-200">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Filter by Ride Type</label>
                            <select wire:model.live="selectedRideType" 
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <option value="">All Ride Types</option>
                                @foreach($rideTypes as $rideType)
                                    <option value="{{ $rideType->id }}">{{ $rideType->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Filter by Classification</label>
                            <select wire:model.live="selectedClassification" 
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <option value="">All Classifications</option>
                                @foreach($classifications as $classification)
                                    <option value="{{ $classification->id }}">{{ $classification->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        
                        
                    </div>
                </div>

                <!-- Statistics Cards -->
                <div class="p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold text-gray-800">Ride Type Statistics</h3>
                        <div class="text-xs text-gray-500">
                            Last updated: {{ now()->format('H:i:s') }}
                        </div>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mb-6">
                        @foreach($rideTypeStats as $stat)
                            <div class="bg-gray-50 rounded-lg p-4">
                                <div class="flex justify-between items-center mb-2">
                                    <h4 class="font-medium text-gray-800">{{ $stat['ride_type']->name }}</h4>
                                    <!-- <span class="text-sm text-gray-500">{{ $stat['usage_percentage'] }}% used</span> -->
                                </div>
                                <div class="flex justify-between text-sm">
                                    <span class="text-green-600">Available: {{ $stat['available'] }}</span>
                                    <span class="text-red-600">Used: {{ $stat['used'] }}</span>
                                    <span class="text-gray-600">Total: {{ $stat['total'] }}</span>
                                </div>
                                <!-- <div class="w-full bg-gray-200 rounded-full h-2 mt-2">
                                    <div class="bg-blue-500 h-2 rounded-full" style="width: {{ $stat['usage_percentage'] }}%"></div>
                                </div> -->
                            </div>
                        @endforeach
                    </div>
                </div>
                <!-- Available Rides -->
                <div class="p-6 border-t border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        Available Rides ({{ $availableRides->count() }})
                    </h3>
                    
                    @if($availableRides->count() > 0)
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            @foreach($availableRides as $ride)
                                <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                                    <div class="flex justify-between items-start mb-2">
                                        <h4 class="font-medium text-gray-800">{{ $ride->classification->rideType->name ?? 'Unknown Ride Type' }}</h4>
                                        <span class="px-2 py-1 bg-green-100 text-green-800 text-xs rounded-full">Available</span>
                                    </div>
                                    <p class="text-sm text-gray-600">{{ $ride->classification->name ?? 'Unknown Classification' }}</p>
                                    <p class="text-sm text-gray-500">{{ $ride->identifier }}</p>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-8 text-gray-500">
                            <svg class="w-12 h-12 mx-auto mb-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 12h6m-6-4h6m2 5.291A7.962 7.962 0 0112 15c-2.34 0-4.29-1.009-5.824-2.709" />
                            </svg>
                            <p>No available rides found with current filters.</p>
                        </div>
                    @endif
                </div>

                <!-- Used Rides -->
                <div class="p-6 border-t border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                        Currently Used Rides ({{ $usedRides->count() }})
                    </h3>
                    
                    @if($usedRides->count() > 0)
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            @foreach($usedRides as $ride)
                                <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                                    <div class="flex justify-between items-start mb-2">
                                        <h4 class="font-medium text-gray-800">{{ $ride->classification->rideType->name ?? 'Unknown Ride Type' }}</h4>
                                        <span class="px-2 py-1 bg-red-100 text-red-800 text-xs rounded-full">In Use</span>
                                    </div>
                                    <p class="text-sm text-gray-600">{{ $ride->classification->name ?? 'Unknown Classification' }}</p>
                                    <p class="text-sm text-gray-500">{{ $ride->identifier }}</p>
                                    <p class="text-xs {{ $ride->is_overdue ?? false ? 'text-red-600 font-semibold' : 'text-gray-600' }}">
                                        <strong>Time Left:</strong> {{ $ride->time_left_formatted ?? 'Unknown' }}
                                    </p>
                            
                            
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-8 text-gray-500">
                            <svg class="w-12 h-12 mx-auto mb-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <p>No rides are currently being used.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <script>
        function hideMessage(messageId) {
            const message = document.getElementById(messageId);
            if (message) {
                message.style.opacity = '0';
                message.style.transition = 'opacity 0.5s ease-out';
                setTimeout(() => {
                    message.style.display = 'none';
                }, 500);
            }
        }

        // Auto-hide success messages after 3 seconds
        document.addEventListener('DOMContentLoaded', function() {
            const successMessage = document.getElementById('success-message');
            if (successMessage) {
                setTimeout(() => {
                    hideMessage('success-message');
                }, 3000);
            }
        });
    </script>
</div>
