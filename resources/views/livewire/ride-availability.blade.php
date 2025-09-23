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
                        <div class="flex md:items-end">
                            <button wire:click="clearFilters" class="self-start md:self-auto mt-6 md:mt-0 px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg">Clear Filters</button>
                        </div>
                    </div>
                </div>

                <!-- Statistics Cards -->
                <div class="p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold text-gray-800">Ride Type Statistics</h3>
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
                                    <div class="flex flex-col sm:flex-row items-start sm:items-center gap-3">
                                        <div class="w-50 h-50 sm:w-24 sm:h-24 md:w-28 md:h-28 order-2 sm:order-1">
                                            @if(!empty($ride->image_path))
                                                <img src="{{ asset('storage/'.$ride->image_path) }}" alt="{{ $ride->identifier }}" class="w-full h-full rounded object-cover border" />
                                            @else
                                                <div class="w-50 h-50 sm:w-24 sm:h-24 md:w-28 md:h-28 rounded border border-dashed border-gray-300 bg-white flex items-center justify-center text-[10px] text-gray-400">No image</div>
                                            @endif
                                        </div>
                                        <div class="order-1 sm:order-2">                                        
                                            <p class="text-sm text-gray-600">{{ $ride->classification->name ?? 'Unknown Classification' }}</p>
                                            <p class="text-sm text-gray-500">{{ $ride->identifier }}</p>
                                        </div>
                                    </div>
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
                                    <div class="flex flex-col sm:flex-row items-start sm:items-center gap-3">
                                        <div class="w-50 h-50 sm:w-24 sm:h-24 md:w-28 md:h-28 order-2 sm:order-1">
                                           @if(!empty($ride->image_path))
                                                <img src="{{ asset('storage/'.$ride->image_path) }}" alt="{{ $ride->identifier }}" class="w-full h-full rounded object-cover border" />
                                            @else
                                                <div class="w-50 h-50 sm:w-24 sm:h-24 md:w-28 md:h-28 rounded border border-dashed border-gray-300 bg-white flex items-center justify-center text-[10px] text-gray-400">No image</div>
                                            @endif
                                            
                                        </div>
                                        <div class="order-1 sm:order-2">
                                            <p class="text-sm text-gray-600">{{ $ride->classification->name ?? 'Unknown Classification' }}</p>
                                            <p class="text-sm text-gray-500">{{ $ride->identifier }}</p>
                                            <p class="text-xs {{ $ride->is_overdue ?? false ? 'text-red-600 font-semibold' : 'text-gray-600' }}"><strong>Time Left:</strong> {{ $ride->time_left_formatted ?? 'Unknown' }}</p>
                                            @if(($ride->overdue_minutes ?? 0) >= 120 && auth()->check() && auth()->user()->userType == 1) 
                                                <div class="mt-2">
                                                    <button type="button" wire:click="endOverdueRental({{ $ride->id }})" class="px-3 py-1.5 text-xs rounded bg-red-600 hover:bg-red-700 text-white md:inline-block">End Time</button>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
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

                <!-- Inactive Rides -->
                <div class="p-6 border-t border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 12H6" />
                        </svg>
                        Inactive Rides ({{ $inactiveRides->count() }})
                    </h3>

                    @if($inactiveRides->count() > 0)
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            @foreach($inactiveRides as $ride)
                                <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
                                    <div class="flex justify-between items-start mb-2">
                                        <h4 class="font-medium text-gray-800">{{ $ride->classification->rideType->name ?? 'Unknown Ride Type' }}</h4>
                                        <span class="px-2 py-1 bg-gray-100 text-gray-700 text-xs rounded-full">Inactive</span>
                                    </div>
                                    <div class="flex flex-col sm:flex-row items-start sm:items-center gap-3">
                                        <div class="w-50 h-50 sm:w-24 sm:h-24 md:w-28 md:h-28 flex-shrink-0 order-2 sm:order-1">
                                            @if(!empty($ride->image_path))
                                                <img src="{{ asset('storage/'.$ride->image_path) }}" alt="{{ $ride->identifier }}" class="w-full h-full rounded object-cover border" />
                                            @else
                                                <div class="w-24 h-24 sm:w-24 sm:h-24 md:w-28 md:h-28 rounded border border-dashed border-gray-300 bg-white flex items-center justify-center text-[10px] text-gray-400">No image</div>
                                            @endif
                                        </div>
                                        <div class="order-1 sm:order-2">
                                            <p class="text-sm text-gray-600">{{ $ride->classification->name ?? 'Unknown Classification' }}</p>
                                            <p class="text-sm text-gray-500">{{ $ride->identifier }}</p>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-8 text-gray-500">
                            <svg class="w-12 h-12 mx-auto mb-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 12H6" />
                            </svg>
                            <p>No inactive rides</p>
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
