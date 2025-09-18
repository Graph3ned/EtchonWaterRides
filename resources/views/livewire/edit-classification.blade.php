<div class="min-h-full p-4">
    <div class="w-full rounded-lg relative overflow-hidden">
        <!-- Success/Error Messages -->
        @if (session()->has('success'))
            <div id="success-message" class="max-w-4xl mx-auto px-4 mb-4">
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

        @if (session()->has('error'))
            <div id="error-message" class="max-w-4xl mx-auto px-4 mb-4">
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                    <span class="block sm:inline">{{ session('error') }}</span>
                    <button 
                        onclick="hideMessage('error-message')" 
                        class="absolute top-0 bottom-0 right-0 px-4 py-3"
                    >
                        <svg class="fill-current h-6 w-6 text-red-500" role="button" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                            <title>Close</title>
                            <path d="M14.348 14.849a1.2 1.2 0 0 1-1.697 0L10 11.819l-2.651 3.029a1.2 1.2 0 1 1-1.697-1.697l2.758-3.15-2.759-3.152a1.2 1.2 0 1 1 1.697-1.697L10 8.183l2.651-3.031a1.2 1.2 0 1 1 1.697 1.697l-2.758 3.152 2.758 3.15a1.2 1.2 0 0 1 0 1.698z"/>
                        </svg>
                    </button>
                </div>
            </div>
        @endif

        <!-- Main Form Card -->
        <div class="max-w-4xl mx-auto">
            <div class="bg-white rounded-xl shadow-lg overflow-hidden border border-gray-100 transition-all duration-300 hover:shadow-xl">
                <!-- Header -->
                <div class="bg-gradient-to-r from-cyan-500 to-blue-600 p-4 sm:p-6">
                    <div class="flex items-center gap-4">
                        @if(!empty($classification->image_path))
                            <div class="flex-shrink-0">
                                <img src="{{ asset('storage/'.$classification->image_path) }}" alt="{{ $classification->name }}" class="w-24 h-24 sm:w-24 sm:h-24 md:w-28 md:h-28 lg:w-32 lg:h-32 rounded-lg object-cover border-2 border-white/30" />
                            </div>
                        @else
                        <div class="w-24 h-24 sm:w-24 sm:h-24 md:w-28 md:h-28 lg:w-32 lg:h-32 rounded-lg border border-dashed border-white/30 bg-white/10 flex items-center justify-center text-[10px] text-white/80">
                            No image
                        </div>
                        @endif
                        <div class="flex-1">
                            <h2 class="text-2xl sm:text-3xl font-bold text-white mb-3">Edit Classification: {{ $classification->name }}</h2>
                            <div class="flex flex-col gap-2">
                                <button wire:click="$set('showImageModal', true)" class="px-3 py-1.5 sm:px-4 sm:py-2 bg-white/20 hover:bg-white/30 text-white rounded-lg text-xs sm:text-sm font-medium w-fit">
                                    Change Picture
                                </button>
                                @error('classificationImage')
                                    <p class="text-blue-200 text-xs rounded-lg p-1.5 inline-block">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Form Content -->
                <div class="p-6">
                    <form wire:submit.prevent="submit" class="space-y-6">
                        <div class="text-center mb-6">
                            <h3 class="text-xl font-semibold text-gray-800 mb-2">Edit Classification Details</h3>
                            <p class="text-gray-600">Update the classification name, price, and identifiers</p>
                        </div>

                        <!-- Current Info -->
                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                            <div class="flex items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-500 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <div>
                                    <p class="text-sm font-medium text-blue-800">Ride Type: {{ $rideType->name }}</p>
                                    <p class="text-sm text-blue-600">Current Classification: {{ $classification->name }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Classification Details -->
                        <div class="border rounded-lg p-4">
                            <h4 class="font-semibold text-gray-800 mb-4">Classification Details</h4>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="text-sm text-gray-700">Name</label>
                                    <input type="text" wire:model="name" placeholder="e.g., Small, Big, Double" class="w-full text-sm rounded-lg border-gray-200 bg-gray-50 focus:bg-white hover:bg-gray-50/80 focus:border-blue-500 focus:ring-2 focus:ring-blue-200" />
                                    @error('name')
                                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div>
                                    <label class="text-sm text-gray-700">Price per hour</label>
                                    <input type="number" step="0.01" min="0.01" wire:model="price_per_hour" placeholder="e.g., 500" class="w-full text-sm rounded-lg border-gray-200 bg-gray-50 focus:bg-white hover:bg-gray-50/80 focus:border-blue-500 focus:ring-2 focus:ring-blue-200" />
                                    @error('price_per_hour')
                                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <div class="mt-4 space-y-2">
                                <label class="text-sm text-gray-700">Identifiers</label>
                                
                                <!-- Existing Identifiers -->
                                
                                
                                <!-- Form Inputs for New/Edit Identifiers -->
                                <!-- <h5 class="text-sm font-medium text-gray-600 mb-2">Add/Edit Identifiers:</h5> -->
                                <div class="space-y-2">
                                    @foreach($identifiers as $iIndex => $identifier)
                                        <div class="flex items-center mb-2">
                                            <div class="flex-1 mr-4">
                                                <input type="text" wire:model="identifiers.{{ $iIndex }}" placeholder="e.g., Red, Blue, Yellow" class="w-full text-sm rounded-lg border-gray-200 bg-gray-50 focus:bg-white hover:bg-gray-50/80 focus:border-blue-500 focus:ring-2 focus:ring-blue-200" />
                                            </div>
                                            
                                            <div class="flex space-x-1">
                                                <!-- Add button only for empty inputs -->
                                                @if(empty(trim($identifier)))
                                                    <button type="button" wire:click="addIdentifierToDatabase({{ $iIndex }})" class="px-3 py-2 text-green-500 hover:bg-green-50 rounded-lg text-sm font-medium">
                                                        Add
                                                    </button>
                                                @endif
                                                
                                                @if(!empty(trim($identifier)) && ($identifierInDatabase[$iIndex] ?? false))
                                                    <button type="button" 
                                                            wire:click="toggleIdentifierStatus({{ $iIndex }})"
                                                            class="inline-flex items-center px-2 py-1 sm:px-3 sm:py-2 {{ ($identifierStatus[$iIndex] ?? true) ? 'bg-blue-500 hover:bg-blue-600' : 'bg-gray-500 hover:bg-gray-600' }} 
                                                                   text-white rounded-lg transition-all duration-200 text-xs sm:text-sm font-medium sm:w-auto sm:text-xs
                                                                   shadow hover:shadow-md transform hover:-translate-y-0.5">
                                                            @if($identifierStatus[$iIndex] ?? true)
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                                            @else
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                            @endif
                                                        </svg>
                                                        {{ ($identifierStatus[$iIndex] ?? true) ? 'Active' : 'Inactive' }}
                                                    </button>
                                                @endif
                                                
                                                @if(!empty(trim($identifier)) && ($identifierInDatabase[$iIndex] ?? false))
                                                    <button type="button" 
                                                            wire:click="confirmDeleteIdentifier({{ $iIndex }})"
                                                            class="inline-flex items-center px-3 py-2 bg-red-500 hover:bg-red-600 text-xs sm:text-sm
                                                                   text-white rounded-lg transition-all duration-200 text-xs sm:text-sm font-medium sm:w-auto sm:text-xs
                                                                   shadow hover:shadow-md transform hover:-translate-y-0.5">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                        </svg>
                                                        <span class="hidden sm:inline">Delete</span>
                                                    </button>
                                                @elseif(empty(trim($identifier)) || !($identifierInDatabase[$iIndex] ?? false))
                                                    <button type="button" wire:click="removeIdentifier({{ $iIndex }})" class="p-2 text-red-500 hover:bg-red-50 rounded-lg">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                        </svg>
                                                    </button>
                                                @endif
                                            </div>
                                        </div>
                                        @error('identifiers.'.$iIndex)
                                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                        @enderror
                                    @endforeach
                                </div>

                                <button type="button" wire:click="addIdentifier" class="w-full flex items-center justify-center px-4 py-2 border-2 border-dashed border-gray-300 rounded-lg text-gray-600 hover:border-blue-400 hover:text-blue-600">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                                    Add Identifier
                                </button>
                            </div>
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
                                Update Classification
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Image Upload Modal -->
    @if ($showImageModal)
    <div class="fixed inset-0 bg-gray-900 bg-opacity-60 flex items-center justify-center z-50 p-4">
        <div class="w-full max-w-md">
            <div class="bg-white rounded-lg shadow-xl border border-gray-100">
                <div class="p-4 sm:p-6">
                    <div class="mb-4">
                        <h3 class="text-lg sm:text-xl font-bold text-gray-900 mb-2">Change Classification Image</h3>
                        <p class="text-sm text-gray-600">Select a new image for {{ $classification->name }}</p>
                    </div>

                    <!-- Image Preview -->
                    <div class="mb-4">
                        @if($classificationImage && is_object($classificationImage))
                            <div class="text-center">
                                <img src="{{ $classificationImage->temporaryUrl() }}" alt="Preview" class="w-32 h-32 mx-auto rounded-lg object-cover border-2 border-gray-200" />
                                <p class="text-xs text-gray-500 mt-2">Preview</p>
                            </div>
                        @elseif(!empty($classification->image_path))
                            <div class="text-center">
                                <img src="{{ asset('storage/'.$classification->image_path) }}" alt="Current" class="w-32 h-32 mx-auto rounded-lg object-cover border-2 border-gray-200" />
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
                               wire:model="classificationImage" 
                               accept="image/jpeg,image/jpg,image/png,image/gif,image/webp,image/svg+xml" 
                               onchange="validateImageFileModal(this)"
                               class="w-full text-sm text-gray-700 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100" />
                        @error('classificationImage')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                        <div id="image-validation-error-modal" class="text-red-500 text-xs mt-1 hidden"></div>
                    </div>

                    <!-- Modal Buttons -->
                    <div class="flex flex-col sm:flex-row gap-3">
                        <button wire:click="saveClassificationImage" 
                                class="w-full bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg font-medium
                                       transform transition-all duration-200 hover:-translate-y-0.5 
                                       hover:shadow-md disabled:opacity-50 disabled:cursor-not-allowed"
                                @if(!$classificationImage || !is_object($classificationImage)) disabled @endif>
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
    <!-- Delete Identifier Modal -->
    @if ($showDeleteModal)
    <div id="deleteIdentifierModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 items-center justify-center z-50">
        <div class="flex justify-center items-center w-full h-full">
            <div class="w-full max-w-md">
                <div class="bg-white dark:bg-gray-800 overflow-hidden sm:rounded-lg shadow-lg">
                    <div class="p-6">
                        <div class="mb-4">
                            <h3 class="text-xl font-bold text-gray-900">Confirm Delete</h3>
                            <p class="mt-2 text-gray-600">Are you sure you want to delete this identifier? This action cannot be undone.</p>
                        </div>
                        <div class="flex space-x-3">
                            <button type="button" wire:click="closeDeleteModal"
                                class="w-full bg-gray-200 text-gray-800 px-4 py-2 rounded-lg font-medium
                                       transform transition-all duration-200 hover:-translate-y-1 
                                       hover:shadow-md hover:bg-gray-300">
                                Cancel
                            </button>
                            <button type="button" wire:click="deleteIdentifier"
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

    document.addEventListener('DOMContentLoaded', function() {
        // Auto-hide success messages after 3 seconds
        const successMessage = document.getElementById('success-message');
        if (successMessage) {
            setTimeout(() => {
                hideMessage('success-message');
            }, 3000);
        }
        
        // Auto-hide error messages after 5 seconds (longer for errors)
        const errorMessage = document.getElementById('error-message');
        if (errorMessage) {
            setTimeout(() => {
                hideMessage('error-message');
            }, 5000);
        }
    });
</script>
