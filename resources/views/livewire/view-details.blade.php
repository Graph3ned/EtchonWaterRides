<div class="min-h-full p-4">
    <!-- Success Message Display -->
    @if (session()->has('success'))
        <div id="success-message" class="max-w-2xl mx-auto px-4 mb-4">
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                <span class="block sm:inline">{{ session('success') }}</span>
                <button 
                    onclick="hideMessage()" 
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

    <div class="w-full max-w-2xl mx-auto rounded-lg relative overflow-hidden shadow-xl">
        <!-- Main Content Card -->
        <div class="bg-white rounded-xl shadow-xl overflow-hidden border border-gray-100 transition-all duration-300 hover:shadow-2xl">
            <!-- Header -->
            <div class="bg-gradient-to-r from-cyan-500 to-blue-600 p-6">
                <div class="flex justify-between items-center">
                    <h2 class="text-2xl font-bold text-white">{{ $rideType->name }}</h2>
                </div>
            </div>

            <!-- Content -->
            <div class="p-6">
                <!-- Action Buttons -->
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
                    <button wire:navigate 
                            href="/admin/add-ride-classification/{{ $rideType->id }}" 
                            class="w-full inline-flex justify-center items-center px-6 py-2.5
                                   bg-gradient-to-r from-cyan-500 to-blue-600 hover:from-cyan-600 hover:to-blue-700
                                   text-white rounded-lg transition-all duration-200 font-medium text-sm
                                   shadow-md hover:shadow-lg transform hover:-translate-y-0.5">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Add Classification
                    </button>

                    <button wire:click="confirmDelete('{{ $rideType->id }}')"
                            class="w-full inline-flex justify-center items-center px-6 py-2.5
                                   bg-red-500 hover:bg-red-600 text-white rounded-lg 
                                   transition-all duration-200 font-medium text-sm
                                   shadow-md hover:shadow-lg transform hover:-translate-y-0.5">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                        Delete
                    </button>

                    <button wire:navigate 
                            href="/admin/edit-ride-type/{{ $rideType->id }}"
                            class="w-full inline-flex justify-center items-center px-6 py-2.5
                                   bg-gradient-to-r from-cyan-500 to-blue-600 hover:from-cyan-600 hover:to-blue-700
                                   text-white rounded-lg transition-all duration-200 font-medium text-sm
                                   shadow-md hover:shadow-lg transform hover:-translate-y-0.5">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                        Edit
                    </button>
                </div>

                <!-- Classifications and Rides -->
                <div class="space-y-6">
                    @forelse ($classifications as $classification)
                        <div class="border rounded-lg p-4 bg-gray-50">
                            <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center mb-4 gap-3">
                                <div>
                                    <h3 class="text-lg font-semibold text-gray-800">{{ $classification->name }}</h3>
                                    <p class="text-sm text-gray-600">₱{{ number_format($classification->price_per_hour, 2) }} per hour</p>
                                </div>
                                <div class="flex flex-col sm:flex-row gap-2">
                                    <button wire:navigate 
                                            href="/admin/edit-classification/{{ $classification->id }}"
                                            class="inline-flex items-center justify-center px-3 py-1.5 w-full sm:w-auto
                                                   bg-gradient-to-r from-cyan-500 to-blue-600 hover:from-cyan-600 hover:to-blue-700
                                                   text-white rounded-lg transition-all duration-200 text-sm
                                                   shadow hover:shadow-md transform hover:-translate-y-0.5">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                        Edit
                                    </button>

                                    <button wire:click="classificationConfirmDelete('{{ $classification->id }}')"
                                            class="inline-flex items-center justify-center px-3 py-1.5 w-full sm:w-auto bg-red-500 hover:bg-red-600 
                                                   text-white rounded-lg transition-all duration-200 text-sm
                                                   shadow hover:shadow-md transform hover:-translate-y-0.5">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                        Delete
                                    </button>
                                </div>
                            </div>
                            
                            <!-- Rides for this classification -->
                            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-2">
                                @forelse ($classification->rides as $ride)
                                    <div class="bg-white rounded-lg p-3 text-center border {{ $ride->is_active ? 'border-green-200 bg-green-50' : 'border-gray-200' }}">
                                        <div class="text-sm font-medium text-gray-900">{{ $ride->identifier }}</div>
                                        <div class="text-xs text-gray-500 mt-1">
                                            {{ $ride->is_active ? 'Active' : 'Inactive' }}
                                        </div>
                                    </div>
                                @empty
                                    <div class="col-span-full text-center text-gray-500 py-4">
                                        No rides available for this classification
                                    </div>
                                @endforelse
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-8 text-gray-500">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mx-auto mb-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                            </svg>
                            <p class="text-lg font-medium">No Classifications Found</p>
                            <p class="text-sm">This ride type doesn't have any classifications yet.</p>
                        </div>
                    @endforelse
                </div>

                <!-- Back Button -->
                <div class="mt-6 flex justify-end">
                    <button wire:navigate 
                            href="/admin/rides-rate"
                            class="inline-flex items-center px-6 py-2.5
                                   bg-gray-600 hover:bg-gray-700 text-white rounded-lg 
                                   transition-all duration-200 font-medium text-sm
                                   shadow-md hover:shadow-lg transform hover:-translate-y-0.5">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                        Back to Rides
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Modal -->
    @if ($showModal)
    <div id="deleteModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 items-center justify-center z-50">
    <div class="flex justify-center items-center w-full h-full">
      <div class="w-full max-w-md">
        <div class="bg-white dark:bg-gray-800 overflow-hidden sm:rounded-lg shadow-lg">
          <div class="p-6">
            <div class="mb-4">
              <h3 class="text-xl font-bold text-gray-900">Confirm Delete</h3>
              <p class="mt-2 text-gray-600">Are you sure you want to delete this ride? This action cannot be undone.</p>
            </div>
            <div class="flex space-x-3">
              <button id="cancelDelete" type="button" wire:click="closeModal"
                class="w-full bg-gray-200 text-gray-800 px-4 py-2 rounded-lg font-medium
                       transform transition-all duration-200 hover:-translate-y-1 
                       hover:shadow-md hover:bg-gray-300">
                Cancel
              </button>
              <button id="confirmDelete" type="button" wire:click="delete"
                class="w-full bg-red-500 text-white px-4 py-2 rounded-lg font-medium
                       transform transition-all duration-200 hover:-translate-y-1 
                       hover:shadow-md hover:bg-red-600">
                Delete
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
    @endif

    <!-- Classification Delete Modal -->
    @if ($classificationShowModal)
    <div id="deleteClassificationModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 items-center justify-center z-50">
    <div class="flex justify-center items-center w-full h-full">
      <div class="w-full max-w-md">
        <div class="bg-white dark:bg-gray-800 overflow-hidden sm:rounded-lg shadow-lg">
          <div class="p-6">
            <div class="mb-4">
              <h3 class="text-xl font-bold text-gray-900">Confirm Delete</h3>
              <p class="mt-2 text-gray-600">Are you sure you want to delete this classification? This action cannot be undone.</p>
            </div>
            <div class="flex space-x-3">
              <button id="cancelDeleteClassification" type="button" wire:click="classificationCloseModal"
                class="w-full bg-gray-200 text-gray-800 px-4 py-2 rounded-lg font-medium
                       transform transition-all duration-200 hover:-translate-y-1 
                       hover:shadow-md hover:bg-gray-300">
                Cancel
              </button>
              <button id="confirmDeleteClassification" type="button" wire:click="deleteClassification"
                class="w-full bg-red-500 text-white px-4 py-2 rounded-lg font-medium
                       transform transition-all duration-200 hover:-translate-y-1 
                       hover:shadow-md hover:bg-red-600">
                Delete
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
    @endif

    <script>
        function hideMessage() {
            const message = document.getElementById('success-message');
            if (message) {
                message.style.opacity = '0';
                message.style.transition = 'opacity 0.5s ease-out';
                setTimeout(() => {
                    message.style.display = 'none';
                }, 500);
            }
        }

        // Auto-hide message after 3 seconds
        document.addEventListener('DOMContentLoaded', function() {
            const message = document.getElementById('success-message');
            if (message) {
                setTimeout(() => {
                    hideMessage();
                }, 3000);
            }
        });
    </script>
</div>
