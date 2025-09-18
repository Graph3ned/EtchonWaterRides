<div class="min-h-full p-4" wire:poll.10s>
    <div class="w-full rounded-lg relative overflow-hidden">
        <div class="max-w-7xl mx-auto">
            <div class="bg-white rounded-xl shadow-lg overflow-hidden border border-gray-100">
                <div class="bg-gradient-to-r from-cyan-500 to-blue-600 p-6">
                    <div class="flex justify-between items-center">
                        <div class="flex items-center">
                            <h2 class="text-2xl font-bold text-white">Ride Availability</h2>
                            <div class="ml-3 flex items-center">
                                <div class="w-2 h-2 bg-green-400 rounded-full animate-pulse"></div>
                                <span class="ml-2 text-sm text-white opacity-90">Live</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="p-6 border-b border-gray-200">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Filter by Ride Type</label>
                            <select wire:model.live="selectedRideType" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <option value="">All Ride Types</option>
                                @foreach($rideTypes as $rideType)
                                    <option value="{{ $rideType->id }}">{{ $rideType->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Filter by Classification</label>
                            <select wire:model.live="selectedClassification" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
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

                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
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
                                            @if(!empty($ride->classification->image_path))
                                                <img src="{{ asset('storage/'.$ride->classification->image_path) }}" alt="{{ $ride->classification->name }}" class="w-full h-full rounded object-cover border" />
                                            @else
                                                <div class="w-50 h-50 sm:w-24 sm:h-24 md:w-28 md:h-28 rounded border border-dashed border-gray-300 bg-white flex items-center justify-center text-[10px] text-gray-400">No image</div>
                                            @endif
                                        </div>
                                        <div class="order-1 sm:order-2">
                                            <p class="text-sm text-gray-600">{{ $ride->classification->name ?? 'Unknown Classification' }}</p>
                                            <p class="text-sm text-gray-500">{{ $ride->identifier }}</p>
                                            <p class="text-sm text-black-500">₱{{ number_format($ride->classification->price_per_hour ?? 0, 2) }} per hour</p>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-8 text-gray-500">
                            <svg class="w-12 h-12 mx-auto mb-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 12h6m-6-4h6m2 5.291A7.962 7.962 0 0112 15c-2.34 0-4.29-1.009-5.824-2.709" /></svg>
                            <p>No available rides found with current filters.</p>
                        </div>
                    @endif
                </div>

                <div class="p-6 border-t border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
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
                                        <div class="w-50 h-50 sm:w-24 sm:h-24 md:w-28 md:h-28 flex-shrink-0 order-2 sm:order-1">
                                            @if(!empty($ride->classification->image_path))
                                                <img src="{{ asset('storage/'.$ride->classification->image_path) }}" alt="{{ $ride->classification->name }}" class="w-full h-full rounded object-cover border" />
                                            @else
                                                <div class="w-50 h-50 sm:w-24 sm:h-24 md:w-28 md:h-28 rounded border border-dashed border-gray-300 bg-white flex items-center justify-center text-[10px] text-gray-400">No image</div>
                                            @endif
                                        </div>
                                        <div class="order-1 sm:order-2">
                                            <p class="text-sm text-gray-600">{{ $ride->classification->name ?? 'Unknown Classification' }}</p>
                                            <p class="text-sm text-gray-500">{{ $ride->identifier }}</p>
                                            <p class="text-sm text-black-500">₱{{ number_format($ride->classification->price_per_hour ?? 0, 2) }} per hour</p>
                                            <p class="text-xs {{ $ride->is_overdue ?? false ? 'text-red-600 font-semibold' : 'text-gray-600' }}"><strong>Time Left:</strong> {{ $ride->time_left_formatted ?? 'Unknown' }}</p>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-8 text-gray-500">
                            <svg class="w-12 h-12 mx-auto mb-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                            <p>No rides are currently being used.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>


