<div class="min-h-full p-4">
  <div class="w-full rounded-lg relative overflow-hidden">
    <!-- Success Message -->
    @if (session()->has('success'))
        <div id="success-message" class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4 transition-opacity duration-500" role="alert">
            <span class="block sm:inline">{{ session('success') }}</span>
        </div>
    @endif

    <!-- Header Card -->
    <div class="bg-white rounded-xl shadow-lg overflow-hidden border border-gray-100 transition-all duration-300 hover:shadow-xl mb-6">
      <div class="bg-gradient-to-r from-cyan-500 to-blue-600 p-6">
        <div class="flex flex-col md:flex-row justify-between items-center gap-4">
          <h2 class="text-2xl font-bold text-white">Ride Types</h2>
          <button wire:navigate href="/admin/add-ride" 
                  class="inline-flex items-center px-6 py-2.5 bg-white/20 hover:bg-white/30 active:bg-white/40 
                         rounded-lg transition-all duration-200 text-white text-sm font-medium backdrop-blur-sm group">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
              </svg>
              Add New Ride
          </button>
        </div>
      

      <!-- Price Cards Grid -->
      <div class="p-6">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
          @foreach ($rideTypes as $rideType)
            <div class="bg-white rounded-xl shadow-lg overflow-hidden border border-gray-100 
                        transition-all duration-300 hover:-translate-y-1 hover:shadow-xl group">
              <div class="p-6">

                <div class="flex flex-col sm:flex-row sm:items-center gap-4">
                   <!-- Image Section -->
                   <div class="flex justify-center sm:justify-start">
                      @if(!empty($rideType->image_path))
                        <img 
                          src="{{ asset('storage/'.$rideType->image_path) }}" 
                          alt="{{ $rideType->name }}" 
                          class="object-cover rounded aspect-square w-24 h-24 sm:w-24 sm:h-24 md:w-28 md:h-28 lg:w-32 lg:h-32" />
                      @else
                        <!-- <div class="w-24 h-24 sm:w-24 sm:h-24 md:w-28 md:h-28 lg:w-32 lg:h-32 rounded-lg border border-dashed border-gray-300 bg-gray-50 flex items-center justify-center text-[10px] text-gray-400">
                          No image
                        </div> -->
                      @endif                    
                    </div>
                    
                    <!-- Content Section -->
                    <div class="flex-1 text-center sm:text-left">
                      <div class="mb-3">
                          <h3 class="text-lg sm:text-xl font-semibold text-gray-800 leading-tight break-words">
                          {{ $rideType->name }}
                          </h3>
                        </div>
                      
                      <!-- Button and Indicators -->
                      <div class="space-y-3">
                        <button wire:navigate href="/admin/view-details/{{ $rideType->id }}" 
                              class="inline-flex items-center justify-center sm:justify-start px-4 py-2 bg-gradient-to-r from-cyan-500 to-blue-600 
                                    hover:from-cyan-600 hover:to-blue-700 rounded-lg transition-all duration-200 
                                    text-white text-sm font-medium shadow-md hover:shadow-lg w-full sm:w-auto">
                          <span>View Details</span>
                          <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 ml-2 group-hover:translate-x-1 transition-transform" 
                              fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                          </svg>
                        </button>
                        
                        <!-- Ride Count Indicators -->
                        <div class="flex items-center justify-center sm:justify-start gap-3 text-xs">
                          <div class="flex items-center gap-1">
                            <div class="w-2 h-2 bg-green-400 rounded-full"></div>
                            <span class="text-green-600 font-medium">{{ $this->getRideCounts($rideType)['active'] }} Active</span>
                          </div>
                          <div class="flex items-center gap-1">
                            <div class="w-2 h-2 bg-gray-400 rounded-full"></div>
                            <span class="text-gray-600 font-medium">{{ $this->getRideCounts($rideType)['inactive'] }} Inactive</span>
                          </div>
                        </div>
                      </div>
                    </div>
                  
                </div>
              </div>
            </div>
          @endforeach
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
});
</script>
