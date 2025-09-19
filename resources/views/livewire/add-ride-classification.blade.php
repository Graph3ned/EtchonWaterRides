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
        <div class="max-w-4xl mx-auto">
            <div class="bg-white rounded-xl shadow-lg overflow-hidden border border-gray-100 transition-all duration-300 hover:shadow-xl">
                <!-- Header -->
                <div class="bg-gradient-to-r from-cyan-500 to-blue-600 p-6">
                    <div class="flex justify-between items-center">
                        <h2 class="text-2xl font-bold text-white">Add Classifications to {{ $rideType->name }}</h2>
                    </div>
                </div>

                <!-- Form Content -->
                <div class="p-6">
                    <form wire:submit.prevent="submit" class="space-y-6">
                        <div class="text-center mb-6">
                            <h3 class="text-xl font-semibold text-gray-800 mb-2">Add Classifications</h3>
                            <p class="text-gray-600">Add one or more classifications with price and identifiers</p>
                        </div>

                        <div class="space-y-6">
                            @foreach($classificationsInput as $cIndex => $c)
                                <div class="border rounded-lg p-4">
                                    <div class="flex justify-between items-center mb-3">
                                        <h4 class="font-semibold text-gray-800">Classification #{{ $cIndex + 1 }}</h4>
                                        @if(count($classificationsInput) > 1)
                                            <button type="button" wire:click="removeClassification({{ $cIndex }})" class="text-red-500 hover:text-red-600 text-sm">Remove</button>
                                        @endif
                                    </div>

                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <div>
                                            <label class="text-sm text-gray-700">Name</label>
                                            <input type="text" wire:model="classificationsInput.{{ $cIndex }}.name" placeholder="e.g., Small, Big, Double" class="w-full text-sm rounded-lg border-gray-200 bg-gray-50 focus:bg-white hover:bg-gray-50/80 focus:border-blue-500 focus:ring-2 focus:ring-blue-200" />
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

                                    <div class="mt-4 space-y-4">
                                        <label class="text-sm text-gray-700 font-medium">Identifiers</label>
                                        @foreach($c['identifiers'] as $iIndex => $identifier)
                                            <div class="border rounded-lg p-3 bg-gray-50">
                                                <div class="flex items-center space-x-2 mb-3">
                                                    <input type="text" 
                                                           wire:model="classificationsInput.{{ $cIndex }}.identifiers.{{ $iIndex }}.name" 
                                                           placeholder="e.g., Red, Blue, Yellow" 
                                                           class="flex-1 text-sm rounded-lg border-gray-200 bg-white focus:bg-white hover:bg-gray-50/80 focus:border-blue-500 focus:ring-2 focus:ring-blue-200" />
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
                                                
                                                <div class="space-y-2">
                                                    <label class="flex items-center text-gray-600 font-medium text-xs">Identifier Image (Optional)</label>
                                                    <input type="file" 
                                                           wire:model="classificationsInput.{{ $cIndex }}.identifiers.{{ $iIndex }}.image"
                                                           accept="image/jpeg,image/jpg,image/png,image/gif,image/webp,image/svg+xml"
                                                           onchange="validateImageFile(this)"
                                                           class="w-full text-xs text-gray-700 file:mr-2 file:py-1 file:px-2 file:rounded file:border-0 file:text-xs file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100" />
                                                    @error('classificationsInput.'.$cIndex.'.identifiers.'.$iIndex.'.image')
                                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                                    @enderror
                                                    @if (isset($classificationsInput[$cIndex]['identifiers'][$iIndex]['image']) && is_object($classificationsInput[$cIndex]['identifiers'][$iIndex]['image']))
                                                        <div class="mt-2">
                                                            <img src="{{ $classificationsInput[$cIndex]['identifiers'][$iIndex]['image']->temporaryUrl() }}" 
                                                                 alt="Preview" 
                                                                 class="h-16 w-16 object-cover rounded border" />
                                                        </div>
                                                    @endif
                                                </div>
                                                
                                                @error('classificationsInput.'.$cIndex.'.identifiers.'.$iIndex.'.name')
                                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                                @enderror
                                            </div>
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

                            <button type="button" wire:click="addClassification" class="w-full flex items-center justify-center px-4 py-2 border-2 border-dashed border-gray-300 rounded-lg text-gray-600 hover:border-blue-400 hover:text-blue-600">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                                Add Another Classification
                            </button>
                        </div>

                        <!-- Active Status -->
                        <div class="flex items-center space-x-2">
                            <input wire:model="isActive" type="checkbox" id="isActive" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500" />
                            <label for="isActive" class="text-sm text-gray-700">Make rides active immediately</label>
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
                                Create Classifications
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-hide success messages after 5 seconds
    const successMessage = document.querySelector('#success-message');
    if (successMessage) {
        setTimeout(function() {
            successMessage.style.opacity = '0';
            setTimeout(function() {
                successMessage.style.display = 'none';
            }, 500);
        }, 5000);
    }
    
    // Auto-hide error messages after 7 seconds (longer for errors)
    const errorMessage = document.querySelector('#error-message');
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
        const errorDiv = input.parentNode.querySelector('.image-validation-error') || 
                        document.getElementById('image-validation-error');
        
        if (file) {
            const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp', 'image/svg+xml'];
            const allowedExtensions = ['.jpg', '.jpeg', '.png', '.gif', '.webp', '.svg'];
            const maxSize = 2 * 1024 * 1024; // 2MB
            
            // Check file extension
            const fileName = file.name.toLowerCase();
            const hasValidExtension = allowedExtensions.some(ext => fileName.endsWith(ext));
            
            if (!allowedTypes.includes(file.type) || !hasValidExtension) {
                if (errorDiv) {
                    errorDiv.textContent = 'Please select a valid image file (JPEG, JPG, PNG, GIF, WebP, or SVG). DOCX and other document files are not allowed.';
                    errorDiv.classList.remove('hidden');
                }
                input.value = ''; // Clear the input
                return false;
            }
            
            if (file.size > maxSize) {
                if (errorDiv) {
                    errorDiv.textContent = 'File size must be less than 2MB.';
                    errorDiv.classList.remove('hidden');
                }
                input.value = ''; // Clear the input
                return false;
            }
            
            // Hide error if validation passes
            if (errorDiv) {
                errorDiv.classList.add('hidden');
            }
        }
        
        return true;
    }

    // Make validateImageFile globally available
    window.validateImageFile = validateImageFile;
});
</script>
