<div class="min-h-full p-4">
    <div class="w-full rounded-lg relative overflow-hidden">
        <!-- Success/Error Messages -->
        @if (session()->has('success'))
            <div id="success-message" class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4 transition-opacity duration-500" role="alert">
                <span class="block sm:inline">{{ session('success') }}</span>
            </div>
        @endif

        @if (session()->has('error'))
            <div id="error-message" class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4 transition-opacity duration-500" role="alert">
                <span class="block sm:inline">{{ session('error') }}</span>
            </div>
        @endif

        <!-- Main Form Card -->
        <div class="max-w-xl mx-auto">
            <div class="bg-white rounded-xl shadow-lg overflow-hidden border border-gray-100 transition-all duration-300 hover:shadow-xl">
                <!-- Header with Progress -->
                <div class="bg-gradient-to-r from-cyan-500 to-blue-600 p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h2 class="text-2xl font-bold text-white">Add New Water Ride</h2>
                        <span class="text-white/80 text-sm">Step {{ $currentStep }} of {{ $maxSteps }}</span>
                    </div>
                </div>

                <!-- Form Content -->
                <div class="p-6">
                    <!-- Step 1: Create New Ride Type -->
                    @if($currentStep === 1)
                        <div class="space-y-6">
                            <div class="text-center">
                                <h3 class="text-xl font-semibold text-gray-800 mb-2">Create New Ride Type</h3>
                                <p class="text-gray-600">Enter the name for your new water ride type</p>
                            </div>

                            <div class="space-y-4">
                                <label for="newRideTypeName" class="flex items-center text-gray-700 font-medium text-sm">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                                    </svg>
                                    Ride Type Name
                                </label>
                                
                                <input wire:model="newRideTypeName" 
                                       id="newRideTypeName"
                                       type="text" 
                                       placeholder="e.g., Jet Ski, Kayak, Paddle Board, Water Scooter"
                                       class="w-full text-sm rounded-lg border-gray-200 bg-gray-50 focus:bg-white hover:bg-gray-50/80
                                              focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all duration-200">
                                @error('newRideTypeName')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                                <div class="mt-4">
                                    <label class="flex items-center text-gray-700 font-medium text-sm">Ride Type Image</label>
                                    <input type="file" 
                                           wire:model="rideTypeImage" 
                                           accept="image/jpeg,image/jpg,image/png,image/gif,image/webp,image/svg+xml" 
                                           onchange="validateImageFile(this)"
                                           class="w-full text-sm text-gray-700 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100" />
                                    @error('rideTypeImage')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                    <div id="image-validation-error" class="text-red-500 text-xs mt-1 hidden"></div>
                                    @if ($rideTypeImage && is_object($rideTypeImage))
                                        <div class="mt-2">
                                            <img src="{{ $rideTypeImage->temporaryUrl() }}" alt="Preview" class="h-20 w-20 object-cover rounded" />
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Step 2: Add Multiple Classifications and Identifiers -->
                    @if($currentStep === 2)
                        <div class="space-y-6">
                            <div class="text-center">
                                <h3 class="text-xl font-semibold text-gray-800 mb-2">Add Classifications</h3>
                                <p class="text-gray-600">Add one or more classifications with price and identifiers</p>
                            </div>

                            <div class="space-y-6">
                                @foreach($classificationsInput as $cIndex => $c)
                                    <div class="classification-item border rounded-lg p-4">
                                        <div class="flex justify-between items-center mb-3">
                                            <h4 class="font-semibold text-gray-800">Classification #{{ $cIndex + 1 }}</h4>
                                            @if(count($classificationsInput) > 1)
                                                <button type="button" wire:click="removeClassification({{ $cIndex }})" 
                                                        class="px-3 py-1.5 text-red-500 hover:text-red-600 hover:bg-red-50 
                                                               rounded-lg text-sm font-medium transition-all duration-200 
                                                               transform hover:-translate-y-0.5 hover:shadow-sm">
                                                    Remove
                                                </button>
                                            @endif
                                        </div>

                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                            <div>
                                                <label class="text-sm text-gray-700">Name</label>
                                                <input type="text" wire:model="classificationsInput.{{ $cIndex }}.name" placeholder="e.g., Small, Big, Double" oninput="validateClassificationNameUniqueness(this)" class="w-full text-sm rounded-lg border-gray-200 bg-gray-50 focus:bg-white hover:bg-gray-50/80 focus:border-blue-500 focus:ring-2 focus:ring-blue-200" />
                                                @error('classificationsInput.'.$cIndex.'.name')
                                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                                @enderror
                                            </div>
                                            <div>
                                                <label class="text-sm text-gray-700">Price per hour</label>
                                                <input type="number" step="0.01" min="0.01" wire:model="classificationsInput.{{ $cIndex }}.price_per_hour" placeholder="e.g., 500" class="w-full text-sm rounded-lg border-gray-200 bg-gray-50 focus:bg-white hover:bg-gray-50/80 focus:border-blue-500 focus:ring-2 focus:ring-blue-200" />
                                                @error('classificationsInput.'.$cIndex.'.price_per_hour')
                                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="mt-4 space-y-2">
                                            <label class="text-sm text-gray-700">Identifiers</label>
                                            @foreach($c['identifiers'] as $iIndex => $identifier)
                                                <div class="flex items-center space-x-2 mb-2">
                                                    <input type="text" wire:model="classificationsInput.{{ $cIndex }}.identifiers.{{ $iIndex }}" placeholder="e.g., Red, Blue, Yellow" oninput="validateIdentifierUniqueness(this, {{ $cIndex }})" class="flex-1 text-sm rounded-lg border-gray-200 bg-gray-50 focus:bg-white hover:bg-gray-50/80 focus:border-blue-500 focus:ring-2 focus:ring-blue-200" />
                                                    @if(count($c['identifiers']) > 1)
                                                        <button type="button" wire:click="removeIdentifier({{ $cIndex }}, {{ $iIndex }})" 
                                                                class="p-2 text-red-500 hover:bg-red-50 hover:text-red-600 rounded-lg 
                                                                       transition-all duration-200 transform hover:-translate-y-0.5 
                                                                       hover:shadow-md active:scale-95">
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                            </svg>
                                                        </button>
                                                    @endif
                                                </div>
                                                @error('classificationsInput.'.$cIndex.'.identifiers.'.$iIndex)
                                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                                @enderror
                                            @endforeach

                                            <button type="button" wire:click="addIdentifier({{ $cIndex }})" 
                                                    class="w-full flex items-center justify-center px-4 py-2 border-2 border-dashed border-gray-300 
                                                           rounded-lg text-gray-600 hover:border-blue-400 hover:text-blue-600 
                                                           hover:bg-blue-50 transition-all duration-200 font-medium 
                                                           transform hover:-translate-y-0.5 hover:shadow-md active:scale-95">
                                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                                </svg>
                                                Add Identifier
                                            </button>
                                        </div>
                                    </div>
                                @endforeach

                                <button type="button" wire:click="addClassification" 
                                        class="w-full flex items-center justify-center px-4 py-2 border-2 border-dashed border-gray-300 
                                               rounded-lg text-gray-600 hover:border-blue-400 hover:text-blue-600 
                                               hover:bg-blue-50 transition-all duration-200 font-medium 
                                               transform hover:-translate-y-0.5 hover:shadow-md active:scale-95">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                    </svg>
                                    Add Another Classification
                                </button>
                            </div>

                            <!-- Active Status -->
                            <div class="flex items-center space-x-2">
                                <input wire:model="isActive" type="checkbox" id="isActive" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500" />
                                <label for="isActive" class="text-sm text-gray-700">Make rides active immediately</label>
                            </div>
                        </div>
                    @endif

                    <!-- Step 3: Add Identifiers -->
                    @if($currentStep === 3)
                        <div class="space-y-6">
                            <div class="text-center">
                                <h3 class="text-xl font-semibold text-gray-800 mb-2">Add Ride Identifiers</h3>
                                <p class="text-gray-600">Add the physical identifiers for this classification (e.g., colors, numbers)</p>
                            </div>

                            <!-- Identifiers List -->
                            <div class="space-y-3">
                                @foreach($identifiers as $index => $identifier)
                                    <div class="flex items-center space-x-2">
                                        <input wire:model="identifiers.{{ $index }}" 
                                               type="text" 
                                               placeholder="e.g., Red, Blue, Yellow, 001, 002"
                                               class="flex-1 text-sm rounded-lg border-gray-200 bg-gray-50 focus:bg-white hover:bg-gray-50/80
                                                      focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all duration-200">
                                        @if(count($identifiers) > 1)
                                            <button type="button" 
                                                    wire:click="removeIdentifier({{ $index }})"
                                                    class="p-2 text-red-500 hover:bg-red-50 hover:text-red-600 rounded-lg 
                                                           transition-all duration-200 transform hover:-translate-y-0.5 
                                                           hover:shadow-md active:scale-95">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                                </svg>
                                            </button>
                                        @endif
                                    </div>
                                    @error('identifiers.'.$index)
                                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                    @enderror
                                @endforeach
                            </div>

                            <!-- Add More Button -->
                            <button type="button" 
                                    wire:click="addIdentifier"
                                    class="w-full flex items-center justify-center px-4 py-2 border-2 border-dashed border-gray-300 
                                           rounded-lg text-gray-600 hover:border-blue-400 hover:text-blue-600 
                                           hover:bg-blue-50 transition-all duration-200 font-medium 
                                           transform hover:-translate-y-0.5 hover:shadow-md active:scale-95">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                </svg>
                                Add Another Identifier
                            </button>

                            <!-- Active Status -->
                            <div class="flex items-center space-x-2">
                                <input wire:model="isActive" 
                                       type="checkbox" 
                                       id="isActive"
                                       class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                <label for="isActive" class="text-sm text-gray-700">Make rides active immediately</label>
                            </div>
                        </div>
                    @endif

                    <!-- Navigation Buttons -->
                    <div class="flex justify-between pt-6">
                        <div class="mr-2">
                            @if($currentStep > 1)
                                <button type="button" 
                                        wire:click="previousStep"
                                        class="px-2 py-2.5 border border-gray-300 text-gray-700 bg-white hover:bg-gray-50 
                                               rounded-lg transition-all duration-200 font-medium text-sm
                                               focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2
                                               transform hover:-translate-y-0.5 hover:shadow-md active:scale-95">
                                    <div class="flex items-center">
                                    <svg class="w-4 h-4 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                                    </svg>
                                    Previous
                                    </div>
                                </button>
                            @else
                                <button type="button" 
                                        wire:navigate 
                                        href="/admin/rides-rate"
                                        class="px-2 py-2.5 border border-gray-300 text-gray-700 bg-white hover:bg-gray-50 
                                               rounded-lg transition-all duration-200 font-medium text-sm
                                               focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2
                                               transform hover:-translate-y-0.5 hover:shadow-md active:scale-95">
                                    <div class="flex items-center">
                                        <svg class="w-4 h-4 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                        <span>Cancel</span>
                                    </div>
                                </button>
                            @endif
                        </div>

                        <div>
                            @if($currentStep < $maxSteps)
                                <button type="button" 
                                        wire:click="nextStep"
                                        class="px-6 py-2.5 bg-gradient-to-r from-cyan-500 to-blue-600 hover:from-cyan-600 hover:to-blue-700
                                               text-white rounded-lg transition-all duration-200 font-medium text-sm
                                               focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2
                                               shadow-md hover:shadow-lg transform hover:-translate-y-0.5 active:scale-95">
                                    <div class="flex items-center gap-1">
                                        <Span>Next</Span>
                                        
                                        <svg class="w-4 h-4 ml-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                        </svg>
                                    </div>
                                </button>
                            @else
                                <button type="button" 
                                        wire:click="submit"
                                        class="px-6 py-2.5 bg-gradient-to-r from-green-500 to-green-600 hover:from-green-600 hover:to-green-700
                                               text-white rounded-lg transition-all duration-200 font-medium text-sm
                                               focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2
                                               shadow-md hover:shadow-lg transform hover:-translate-y-0.5 active:scale-95">
                                    <div class="flex items-center gap-1">
                                        <svg class="w-4 h-4 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                        </svg>
                                        <span>Add</span>
                                    </div>
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-hide success messages after 5 seconds
    const successMessage = document.getElementById('success-message');
    if (successMessage) {
        setTimeout(function() {
            successMessage.style.opacity = '0';
            setTimeout(function() {
                successMessage.style.display = 'none';
            }, 500);
        }, 5000);
    }
    
    // Auto-hide error messages after 7 seconds (longer for errors)
    const errorMessage = document.getElementById('error-message');
    if (errorMessage) {
        setTimeout(function() {
            errorMessage.style.opacity = '0';
            setTimeout(function() {
                errorMessage.style.display = 'none';
            }, 500);
        }, 7000);
    }

    // Image file validation function
    function validateImageFile(input) {
        const file = input.files[0];
        const errorDiv = document.getElementById('image-validation-error');
        
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

    // Function to validate identifier uniqueness within a classification
    function validateIdentifierUniqueness(input, classificationIndex) {
        const currentValue = input.value.trim().toLowerCase();
        const classificationDiv = input.closest('.classification-item');
        const identifierInputs = classificationDiv.querySelectorAll('input[wire\\:model*="identifiers"]');
        
        let duplicates = 0;
        identifierInputs.forEach(function(identifierInput) {
            if (identifierInput.value.trim().toLowerCase() === currentValue && currentValue !== '') {
                duplicates++;
            }
        });
        
        // Show error if there are duplicates
        if (duplicates > 1) {
            // Remove existing error message
            const existingError = classificationDiv.querySelector('.identifier-duplicate-error');
            if (existingError) {
                existingError.remove();
            }
            
            // Add error message
            const errorDiv = document.createElement('div');
            errorDiv.className = 'identifier-duplicate-error text-red-500 text-xs mt-1';
            errorDiv.textContent = 'Duplicate identifiers are not allowed within the same classification.';
            input.parentNode.appendChild(errorDiv);
            
            return false;
        } else {
            // Remove error message if no duplicates
            const existingError = classificationDiv.querySelector('.identifier-duplicate-error');
            if (existingError) {
                existingError.remove();
            }
            return true;
        }
    }

    // Function to validate classification name uniqueness across all classifications
    function validateClassificationNameUniqueness(input) {
        const currentValue = input.value.trim().toLowerCase();
        const allClassificationNameInputs = document.querySelectorAll('input[wire\\:model*="classificationsInput"][wire\\:model*="name"]');
        
        let duplicates = 0;
        allClassificationNameInputs.forEach(function(nameInput) {
            if (nameInput.value.trim().toLowerCase() === currentValue && currentValue !== '') {
                duplicates++;
            }
        });
        
        // Show error if there are duplicates
        if (duplicates > 1) {
            // Remove existing error message
            const existingError = input.parentNode.querySelector('.classification-name-duplicate-error');
            if (existingError) {
                existingError.remove();
            }
            
            // Add error message
            const errorDiv = document.createElement('div');
            errorDiv.className = 'classification-name-duplicate-error text-red-500 text-xs mt-1';
            errorDiv.textContent = 'Duplicate classification names are not allowed.';
            input.parentNode.appendChild(errorDiv);
            
            return false;
        } else {
            // Remove error message if no duplicates
            const existingError = input.parentNode.querySelector('.classification-name-duplicate-error');
            if (existingError) {
                existingError.remove();
            }
            return true;
        }
    }
});
</script>
