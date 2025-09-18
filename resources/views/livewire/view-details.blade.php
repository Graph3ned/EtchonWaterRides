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

    <div class="w-full max-w-4xl mx-auto rounded-lg relative overflow-hidden shadow-xl">
        <!-- Main Content Card -->
        <div class="bg-white rounded-xl shadow-xl overflow-hidden border border-gray-100 transition-all duration-300 hover:shadow-2xl">
            <!-- Header -->
            <div class="bg-gradient-to-r from-cyan-500 to-blue-600 p-4 sm:p-6">
                <div class="flex items-center gap-4">
                    <!-- Image Preview -->
                    @if(!empty($rideType->image_path))
                        <div class="flex-shrink-0">
                            <img src="{{ asset('storage/'.$rideType->image_path) }}" alt="{{ $rideType->name }}" class="w-24 h-24 sm:w-24 sm:h-24 md:w-28 md:h-28 lg:w-32 lg:h-32 rounded-lg object-cover border-2 border-white/30" />
                        </div>
                    @else
                    <!-- <div class="w-24 h-24 sm:w-24 sm:h-24 md:w-28 md:h-28 lg:w-32 lg:h-32 rounded-lg border border border-dashed border-white/30 bg-white/10 flex items-center justify-center text-[10px] text-white/80">
                        No image
                    </div> -->
                    @endif
                    
                    <!-- Content Column -->
                    <div class="flex-1">
                        <h2 class="text-2xl sm:text-3xl font-bold text-white mb-3">{{ $rideType->name }}</h2>
                        <div class="flex flex-col gap-2">
                            <button wire:click="$set('showImageModal', true)" class="px-3 py-1.5 sm:px-4 sm:py-2 bg-white/20 hover:bg-white/30 text-white rounded-lg text-xs sm:text-sm font-medium w-fit">
                                Change Picture
                            </button>
                            @error('rideTypeImage')
                                <p class="text-blue-200 text-xs rounded-lg p-1.5 inline-block">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <!-- Content -->
            <div class="p-4 sm:p-6">
                <!-- Action Buttons -->
                <div class="flex gap-2 mb-6">
                    <button wire:navigate 
                            href="/admin/add-ride-classification/{{ $rideType->id }}" 
                            class="w-full inline-flex justify-center items-center px-2 py-2 sm:px-6 sm:py-2.5
                                   bg-gradient-to-r from-cyan-500 to-blue-600 hover:from-cyan-600 hover:to-blue-700
                                   text-white rounded-lg transition-all duration-200 font-medium text-xs sm:text-sm
                                   shadow-md hover:shadow-lg transform hover:-translate-y-0.5">
                         <span class="hidden sm:inline-block">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                            </svg>
                        </span>
                        Add Classification
                    </button>

                    <button wire:click="confirmDelete('{{ $rideType->id }}')"
                            class="w-full inline-flex justify-center items-center px-4 py-2 sm:px-6 sm:py-2.5
                                   bg-red-500 hover:bg-red-600 text-white rounded-lg 
                                   transition-all duration-200 font-medium text-xs sm:text-sm
                                   shadow-md hover:shadow-lg transform hover:-translate-y-0.5">
                        <span class="hidden sm:inline-block">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                        </span>
                        Delete
                    </button>

                    <button wire:navigate 
                            href="/admin/edit-ride-type/{{ $rideType->id }}"
                            class="w-full inline-flex justify-center items-center px-4 py-2 sm:px-6 sm:py-2.5
                                   bg-gradient-to-r from-cyan-500 to-blue-600 hover:from-cyan-600 hover:to-blue-700
                                   text-white rounded-lg transition-all duration-200 font-medium text-xs sm:text-sm
                                   shadow-md hover:shadow-lg transform hover:-translate-y-0.5">
                        <span class="hidden sm:inline-block">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                            </svg>
                        </span>
                        Edit
                    </button>
                </div>

                <!-- Classifications and Rides -->
                <div class="space-y-6">
                    @forelse ($classifications as $classification)
                        <div class="border rounded-lg p-3 sm:p-4 bg-gray-50 hover:bg-gray-100 transition-colors duration-200">
                            <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center mb-4 gap-3">
                                <div class="flex items-center gap-3 flex-1 min-w-0">
                                    <div class="w-16 h-16 sm:w-20 sm:h-20 md:w-24 md:h-24 flex-shrink-0">
                                        @if (!empty($classification->image_path))
                                            <img src="{{ asset('storage/'.$classification->image_path) }}" alt="{{ $classification->name }}" class="w-16 h-16 sm:w-20 sm:h-20 md:w-24 md:h-24 rounded-lg object-cover border" />
                                        @else
                                            <!-- <div class="w-16 h-16 sm:w-20 sm:h-20 md:w-24 md:h-24 rounded-lg border border-dashed border-gray-300 bg-gray-50 flex items-center justify-center text-[10px] text-gray-400">
                                                No image
                                            </div> -->
                                        @endif
                                    </div>
                                    <div class="min-w-0">
                                        <h3 class="text-base sm:text-lg font-semibold text-gray-800 mb-1 truncate">{{ $classification->name }}</h3>
                                        <p class="text-sm text-gray-600">₱{{ number_format($classification->price_per_hour, 2) }} per hour</p>
                                    </div>
                                </div>
                                <div class="flex gap-2 w-full sm:w-auto">
                                    <button wire:navigate 
                                            href="/admin/edit-classification/{{ $classification->id }}"
                                            class="inline-flex items-center justify-center px-3 py-1.5 w-full sm:w-auto
                                                   bg-gradient-to-r from-cyan-500 to-blue-600 hover:from-cyan-600 hover:to-blue-700
                                                   text-white rounded-lg transition-all duration-200 text-xs sm:text-sm
                                                   shadow hover:shadow-md transform hover:-translate-y-0.5">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                        Edit
                                    </button>

                                    <button wire:click="classificationConfirmDelete('{{ $classification->id }}')"
                                            class="inline-flex items-center justify-center px-3 py-1.5 w-full sm:w-auto bg-red-500 hover:bg-red-600 
                                                   text-white rounded-lg transition-all duration-200 text-xs sm:text-sm
                                                   shadow hover:shadow-md transform hover:-translate-y-0.5">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                        Delete
                                    </button>
                                </div>
                            </div>
                            
                            <!-- Rides for this classification -->
                            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 gap-2">
                                @forelse ($classification->rides as $ride)
                                    <div class="rounded-lg p-2 sm:p-3 text-center border-2 {{ $ride->is_active ? 'bg-green-50 border-green-200' : 'bg-red-100 border-red-300' }} hover:shadow-md transition-shadow duration-200">
                                        <div class="text-xs sm:text-sm font-medium text-gray-900 truncate">{{ $ride->identifier }}</div>
                                        <div class="text-[10px] sm:text-xs text-gray-500 mt-1">
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
                <div class="mt-6 flex justify-center sm:justify-end">
                    <button wire:navigate 
                            href="/admin/rides-rate"
                            class="inline-flex items-center px-4 py-2 sm:px-6 sm:py-2.5
                                   bg-gray-600 hover:bg-gray-700 text-white rounded-lg 
                                   transition-all duration-200 font-medium text-xs sm:text-sm
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
    <div id="deleteModal" class="fixed inset-0 bg-gray-900 bg-opacity-60 flex items-center justify-center z-50 p-4">
      <div class="w-full max-w-md">
        <div class="bg-white rounded-lg shadow-xl border border-gray-100">
          <div class="p-4 sm:p-6">
            <div class="mb-4 text-center">
              <svg class="w-12 h-12 text-red-500 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
              </svg>
              <h3 class="text-lg sm:text-xl font-bold text-gray-900">Confirm Delete</h3>
              <p class="mt-2 text-sm sm:text-base text-gray-600">Are you sure you want to delete this ride type? This action cannot be undone.</p>
            </div>
            <div class="flex flex-col sm:flex-row gap-3">
              <button id="cancelDelete" type="button" wire:click="closeModal"
                class="w-full bg-gray-100 text-gray-700 px-4 py-2 rounded-lg font-medium
                       transform transition-all duration-200 hover:-translate-y-0.5 
                       hover:shadow-md hover:bg-gray-200">
                Cancel
              </button>
              <button id="confirmDelete" type="button" wire:click="delete"
                class="w-full bg-red-500 text-white px-4 py-2 rounded-lg font-medium
                       transform transition-all duration-200 hover:-translate-y-0.5 
                       hover:shadow-md hover:bg-red-600">
                Delete
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
    @endif

    <!-- Classification Delete Modal -->
    @if ($classificationShowModal)
    <div id="deleteClassificationModal" class="fixed inset-0 bg-gray-900 bg-opacity-60 flex items-center justify-center z-50 p-4">
      <div class="w-full max-w-md">
        <div class="bg-white rounded-lg shadow-xl border border-gray-100">
          <div class="p-4 sm:p-6">
            <div class="mb-4 text-center">
              <svg class="w-12 h-12 text-red-500 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
              </svg>
              <h3 class="text-lg sm:text-xl font-bold text-gray-900">Confirm Delete</h3>
              <p class="mt-2 text-sm sm:text-base text-gray-600">Are you sure you want to delete this classification? This action cannot be undone.</p>
            </div>
            <div class="flex flex-col sm:flex-row gap-3">
              <button id="cancelDeleteClassification" type="button" wire:click="classificationCloseModal"
                class="w-full bg-gray-100 text-gray-700 px-4 py-2 rounded-lg font-medium
                       transform transition-all duration-200 hover:-translate-y-0.5 
                       hover:shadow-md hover:bg-gray-200">
                Cancel
              </button>
              <button id="confirmDeleteClassification" type="button" wire:click="deleteClassification"
                class="w-full bg-red-500 text-white px-4 py-2 rounded-lg font-medium
                       transform transition-all duration-200 hover:-translate-y-0.5 
                       hover:shadow-md hover:bg-red-600">
                Delete
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
    @endif

    <!-- Image Upload Modal -->
    @if ($showImageModal)
    <div class="fixed inset-0 bg-gray-900 bg-opacity-60 flex items-center justify-center z-50 p-4">
        <div class="w-full max-w-md">
            <div class="bg-white rounded-lg shadow-xl border border-gray-100">
                <div class="p-4 sm:p-6">
                    <div class="mb-4">
                        <h3 class="text-lg sm:text-xl font-bold text-gray-900 mb-2">Change Ride Type Image</h3>
                        <p class="text-sm text-gray-600">Select a new image for {{ $rideType->name }}</p>
                    </div>
                    
                    <!-- Image Preview -->
                    <div class="mb-4">
                        @if($rideTypeImage && is_object($rideTypeImage))
                            <div class="text-center">
                                <img src="{{ $rideTypeImage->temporaryUrl() }}" alt="Preview" class="w-32 h-32 mx-auto rounded-lg object-cover border-2 border-gray-200" />
                                <p class="text-xs text-gray-500 mt-2">Preview</p>
                            </div>
                        @elseif(!empty($rideType->image_path))
                            <div class="text-center">
                                <img src="{{ asset('storage/'.$rideType->image_path) }}" alt="Current" class="w-32 h-32 mx-auto rounded-lg object-cover border-2 border-gray-200" />
                                <p class="text-xs text-gray-500 mt-2">Current Image</p>
                            </div>
                        @else
                            <div class="text-center py-8 bg-gray-50 rounded-lg border-2 border-dashed border-gray-300">
                                <svg class="w-12 h-12 mx-auto text-gray-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                                <p class="text-sm text-gray-500">No image selected</p>
                            </div>
                        @endif
                    </div>
                    
                    <!-- File Input -->
                    <div class="mb-4">
                        <input type="file" 
                               wire:model="rideTypeImage" 
                               accept="image/jpeg,image/jpg,image/png,image/gif,image/webp,image/svg+xml" 
                               onchange="validateImageFileModal(this)"
                               class="w-full text-sm text-gray-700 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100" />
                        @error('rideTypeImage')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                        <div id="image-validation-error-modal" class="text-red-500 text-xs mt-1 hidden"></div>
                    </div>
                    
                    <!-- Modal Buttons -->
                    <div class="flex flex-col sm:flex-row gap-3">
                        <button wire:click="saveRideTypeImage" 
                                class="w-full bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg font-medium
                                       transform transition-all duration-200 hover:-translate-y-0.5 
                                       hover:shadow-md disabled:opacity-50 disabled:cursor-not-allowed"
                                @if(!$rideTypeImage || !is_object($rideTypeImage)) disabled @endif>
                            Save Image
                        </button>
                        <button wire:click="$set('showImageModal', false)" 
                                class="w-full bg-gray-100 text-gray-700 px-4 py-2 rounded-lg font-medium
                                       transform transition-all duration-200 hover:-translate-y-0.5 
                                       hover:shadow-md hover:bg-gray-200">
                            Cancel
                        </button>
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

        // Image file validation function for modal
        function validateImageFileModal(input) {
            const file = input.files[0];
            const errorDiv = document.getElementById('image-validation-error-modal');
            
            if (file) {
                const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp', 'image/svg+xml'];
                const allowedExtensions = ['.jpg', '.jpeg', '.png', '.gif', '.webp', '.svg'];
                const maxSize = 2 * 1024 * 1024; // 2MB
                
                // Check file extension
                const fileName = file.name.toLowerCase();
                const hasValidExtension = allowedExtensions.some(ext => fileName.endsWith(ext));
                
                if (!allowedTypes.includes(file.type) || !hasValidExtension) {
                    errorDiv.textContent = 'Please select a valid image file (JPEG, JPG, PNG, GIF, WebP, or SVG). DOCX and other document files are not allowed.';
                    errorDiv.classList.remove('hidden');
                    input.value = ''; // Clear the input
                    return false;
                }
                
                if (file.size > maxSize) {
                    errorDiv.textContent = 'File size must be less than 2MB.';
                    errorDiv.classList.remove('hidden');
                    input.value = ''; // Clear the input
                    return false;
                }
                
                // Hide error if validation passes
                errorDiv.classList.add('hidden');
            }
            
            return true;
        }
    </script>
</div>
