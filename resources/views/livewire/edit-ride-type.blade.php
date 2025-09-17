<div class="min-h-full p-4">
    <div class="w-full rounded-lg relative overflow-hidden">
        <!-- Success/Error Messages -->
        @if (session()->has('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                <span class="block sm:inline">{{ session('success') }}</span>
            </div>
        @endif

        @if (session()->has('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                <span class="block sm:inline">{{ session('error') }}</span>
            </div>
        @endif

        <!-- Main Form Card -->
        <div class="max-w-2xl mx-auto">
            <div class="bg-white rounded-xl shadow-lg overflow-hidden border border-gray-100 transition-all duration-300 hover:shadow-xl">
                <!-- Header -->
                <div class="bg-gradient-to-r from-cyan-500 to-blue-600 p-6">
                    <div class="flex justify-between items-center">
                        <h2 class="text-2xl font-bold text-white">Edit Ride Type</h2>
                    </div>
                </div>

                <!-- Form Content -->
                <div class="p-6">
                    <form wire:submit.prevent="updateRideType" class="space-y-6">
                        <div class="text-center mb-6">
                            <h3 class="text-xl font-semibold text-gray-800 mb-2">Update Ride Type Name</h3>
                            <p class="text-gray-600">Modify the name of your ride type</p>
                        </div>

                        <!-- Ride Type Input -->
                        <div class="space-y-2">
                            <label class="flex items-center text-gray-700 font-medium text-sm">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                                </svg>
                                Ride Type Name
                            </label>
                            <input wire:model="name" 
                                   type="text" 
                                   placeholder="e.g., Paddle Board, Kayak, Water Bike"
                                   class="w-full text-sm rounded-lg border-gray-200 bg-gray-50 focus:bg-white hover:bg-gray-50/80
                                          focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all duration-200">
                            @error('name')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>                        

                        <!-- Action Buttons -->
                        <div class="flex space-x-4 pt-4">
                            <button type="button" 
                                    wire:navigate 
                                    href="/admin/view-details/{{ $rideType->id }}"
                                    class="w-full inline-flex justify-center items-center px-6 py-2.5 border border-transparent
                                           text-white bg-gray-600 hover:bg-gray-700 focus:ring-gray-500
                                           rounded-lg transition-all duration-200 font-medium text-sm
                                           focus:outline-none focus:ring-2 focus:ring-offset-2">
                                Cancel
                            </button>

                            <button type="submit"
                                    class="w-full inline-flex justify-center items-center px-6 py-2.5
                                           bg-gradient-to-r from-cyan-500 to-blue-600 hover:from-cyan-600 hover:to-blue-700
                                           text-white rounded-lg transition-all duration-200 font-medium text-sm
                                           focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2
                                           shadow-md hover:shadow-lg">
                                Update Ride Type
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
