<div class="min-h-full">
    <div class="w-full max-w-lg p-6 bg-white rounded-lg relative overflow-hidden border-double border-4 border-[#00A3E0] mx-auto">

        <h1 class="text-2xl font-bold text-center mb-6 text-[#00A3E0]">Edit Ride</h1>

        @if (session()->has('message'))
            <div class="mb-4 p-4 bg-green-200 text-green-800 rounded-lg">
                {{ session('message') }}
            </div>
        @endif

        @if (session()->has('error'))
            <div class="mb-4 p-4 bg-red-200 text-red-800 rounded-lg">
                {{ session('error') }}
            </div>
        @endif

        <form wire:submit.prevent="updateRides">
            <!-- ----------------- ride type ----------------- -->
            <div class="mb-4">
                <label for="rideTypeId" class="block text-sm font-medium text-gray-700">Ride Type</label>
                <select wire:model.live="rideTypeId" 
                    id="rideTypeId" 
                    class="block w-full mt-1 rounded-lg border-gray-300 shadow-sm focus:border-[#00A3E0] focus:ring focus:ring-[#00A3E0] focus:ring-opacity-50">
                    <option value="" disabled>Select Ride Type</option>
                    @foreach($rideTypes as $rideType)
                        <option value="{{ $rideType->id }}">{{ $rideType->name }}</option>
                    @endforeach
                </select>
                @error('rideTypeId') 
                    <span class="text-red-500">{{ $message }}</span> 
                @enderror
            </div>

            <!-- ----------------- classification ----------------- -->
            <div class="mb-4">
                <label for="classificationId" class="block text-sm font-medium text-gray-700">Classification</label>
                <select wire:model.live="classificationId" 
                    id="classificationId" 
                    class="block w-full mt-1 rounded-lg border-gray-300 shadow-sm focus:border-[#00A3E0] focus:ring focus:ring-[#00A3E0] focus:ring-opacity-50">
                    <option value="" disabled>Select Classification</option>
                    @foreach($classifications as $classification)
                        <option value="{{ $classification->id }}">{{ $classification->name }}</option>
                    @endforeach
                </select>
                @error('classificationId') 
                    <span class="text-red-500">{{ $message }}</span> 
                @enderror
            </div>

            <!-- ----------------- specific ride ----------------- -->
            <div class="mb-4">
                <label for="rideId" class="block text-sm font-medium text-gray-700">Ride</label>
                <select wire:model.live="rideId" 
                    id="rideId" 
                    class="block w-full mt-1 rounded-lg border-gray-300 shadow-sm focus:border-[#00A3E0] focus:ring focus:ring-[#00A3E0] focus:ring-opacity-50">
                    <option value="" disabled>Select Ride</option>
                    @foreach($rides as $ride)
                        <option value="{{ $ride->id }}">{{ $ride->identifier }} ({{ $ride->classification->name }})</option>
                    @endforeach
                </select>
                @error('rideId') 
                    <span class="text-red-500">{{ $message }}</span> 
                @enderror
            </div>

            <!-- ----------------- note ----------------- -->
            <div class="mb-4">
                <label for="note" class="block text-sm font-medium text-gray-700">Note:</label>
                <textarea 
                    wire:model="note" 
                    id="note" 
                    rows="3" 
                    class="block w-full mt-1 rounded-lg border-gray-300 shadow-sm focus:border-[#00A3E0] focus:ring focus:ring-[#00A3E0] focus:ring-opacity-50"
                    placeholder="Enter any additional notes here..."
                ></textarea>
                @error('note')
                    <div class="text-red-500">{{$message}}</div>
                @enderror
            </div>

            <!-- ----------------- life jacket quantity ----------------- -->
            <div class="mb-4">
                <label for="life_jacket_quantity" class="block text-sm font-medium text-gray-700">Life Jacket Quantity</label>
                <select wire:model="life_jacket_quantity" 
                    id="life_jacket_quantity" 
                    class="block w-full mt-1 rounded-lg border-gray-300 shadow-sm focus:border-[#00A3E0] focus:ring focus:ring-[#00A3E0] focus:ring-opacity-50">
                    @foreach(range(0, 5) as $quantity)
                        <option value="{{ $quantity }}">{{ $quantity }}</option>
                    @endforeach
                </select>
                @error('life_jacket_quantity') 
                    <span class="text-red-500">{{ $message }}</span> 
                @enderror
            </div>

            <div class="mb-6 text-center">
                <h1 class="text-2xl font-bold text-[#00A3E0] mb-2">Extend Ride</h1>
                <p class="text-gray-600 text-sm">Adjust the duration of the current ride</p>
            </div>

            <!-- Current Duration Display -->
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700">Current Duration</label>
                <p class="text-lg font-semibold text-gray-800">
                    @if($duration >= 60)
                        {{ intdiv($duration, 60) }}hr{{ intdiv($duration, 60) > 1 ? 's' : '' }}
                        @if($duration % 60 > 0)
                            {{ $duration % 60 }}min
                        @endif
                    @else
                        {{ $duration }} minutes
                    @endif
                </p>
            </div>

            <!-- Extend Time Section -->
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700">Extend Time</label>
                
                <!-- Predefined Extension Duration (only show when custom duration is not selected) -->
                @if(!$showCustomDuration)
                    <select wire:model.live="extendDuration" class="block w-full mt-1 rounded-lg border-gray-300 shadow-sm focus:border-[#00A3E0] focus:ring focus:ring-[#00A3E0] focus:ring-opacity-50">
                        <option value="0">No Extension</option>
                        <option value="30">30 minutes</option>
                        <option value="60">1 Hour</option>
                        <option value="120">2 Hours</option>
                        <option value="180">3 Hours</option>
                        <option value="240">4 Hours</option>
                        <option value="300">5 Hours</option>
                    </select>
                @endif

                

                <!-- Custom Duration Input -->
                @if($showCustomDuration)
                    <div class="mt-4">
                        <label for="customDuration" class="block text-sm font-medium text-gray-700">Custom Extension (minutes)</label>
                        <input wire:model.live="customDuration" type="number" min="1" class="block w-full mt-1 rounded-lg border-gray-300 shadow-sm focus:border-[#00A3E0] focus:ring focus:ring-[#00A3E0] focus:ring-opacity-50">
                    </div>
                @endif

                <!-- Custom Duration Toggle -->
                <div class="mt-4 flex items-center">
                    <label class="inline-flex items-center cursor-pointer">
                        <input type="checkbox" wire:model.live="showCustomDuration" class="sr-only peer">
                        <div class="relative w-11 h-6 bg-gray-200 rounded-full peer peer-focus:ring-4 peer-focus:ring-sky-300 dark:peer-focus:ring-sky-800 dark:bg-sky-700 peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:start-[2px] after:bg-white after:border-sky-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-sky-600 peer-checked:bg-sky-600"></div>
                        <span class="ms-3 text-sm font-medium text-slate-700">Custom Extension</span>
                    </label>
                </div>
            </div>

            <!-- New Total Duration Display -->
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700">New Total Duration</label>
                <p class="text-lg font-semibold text-gray-800">
                    @php
                        $newDuration = $duration + ($showCustomDuration ? (int)$customDuration : (int)$extendDuration);
                    @endphp
                    @if($newDuration >= 60)
                        {{ intdiv($newDuration, 60) }}hr{{ intdiv($newDuration, 60) > 1 ? 's' : '' }}
                        @if($newDuration % 60 > 0)
                            {{ $newDuration % 60 }}min
                        @endif
                    @else
                        {{ $newDuration }} minutes
                    @endif
                </p>
            </div>

            <!-- Updated Time End Display -->
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700">New End Time</label>
                <p class="text-lg font-semibold text-gray-800">{{ \Carbon\Carbon::parse($endAt)->format('h:i A') }}</p>
            </div>

            <!-- Updated Total Price Display -->
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700">New Total Price</label>
                <p class="text-lg font-semibold text-gray-800">₱{{ number_format($computedTotal, 2) }}</p>
            </div>

            <div class="flex space-x-4">
                <button type="button" 
                    wire:click="updateStatus"
                    class="w-full bg-red-500 text-white px-5 py-2.5 rounded-lg font-medium
                           transform transition-all duration-200 hover:-translate-y-1 
                           hover:shadow-md hover:bg-red-600">
                    End Rental
                </button>
                <button type="button" 
                    wire:click="$dispatch('closeModal')"
                    class="w-full bg-[#FF8C00] text-white px-5 py-2.5 rounded-lg font-medium
                           transform transition-all duration-200 hover:-translate-y-1 
                           hover:shadow-md hover:bg-[#E67E00]">
                    Cancel
                </button>

                <button type="submit"
                    class="w-full bg-[#00A3E0] text-white py-2.5 px-5 rounded-lg font-medium 
                           transform transition-all duration-200 hover:-translate-y-1 
                           hover:shadow-md hover:bg-[#0093CC]">
                    Update Rental
                </button>
            </div>
        </form>
    </div>
</div>
