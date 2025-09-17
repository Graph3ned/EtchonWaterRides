<div class="min-h-full">
    <div class="w-full p-4 relative">
        <!-- Clean title with logo blue -->
        <h2 class="text-2xl font-bold text-center mb-6 text-[#00A3E0]">🌊 Staff Dashboard</h2>

        <div class="flex justify-between items-center mb-6 flex-col sm:flex-row space-y-4 sm:space-y-0">
            <div class="flex items-center space-x-4">
                <!-- Add Ride Button -->
                <button wire:click="$set('showAddRides', true)"
                        class="bg-[#00A3E0] text-white py-2.5 px-6 rounded-lg font-medium 
                               transform transition-all duration-200 hover:-translate-y-1 
                               hover:shadow-lg hover:bg-[#0093CC]">
                    Add Rides
                </button>

                <!-- Ride Filter Dropdown -->
                <select wire:model.live="rideFilter" 
                        class="border border-blue-200 rounded-lg px-4 py-2.5 text-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-300">
                    <option value="all">Show All Rides</option>
                    <option value="ended">Show Ended Rides</option>
                    <option value="ongoing">Show Ongoing Rides</option>
                </select>
            </div>

            <!-- Total Price Display -->
            <div class="text-center sm:text-right">
                <span class="text-lg font-semibold text-[#00A3E0] whitespace-nowrap">
                    Total Income: ₱{{ number_format($totalPrice, 2) }}
                </span>
            </div>
        </div>

        <!-- Table Section -->
        <div class="rounded-lg shadow-lg border border-blue-100">
            <div class="max-h-[600px] overflow-y-auto scrollbar-thin scrollbar-thumb-gray-400 scrollbar-track-gray-100">
                <table class="w-full lg:w-full sm:min-w-max table-auto text-sm">
                    <thead class="bg-gradient-to-r from-cyan-500 to-blue-600 text-white text-sm font-semibold sticky top-0 z-10">
                        <tr class="border-b border-blue-400">
                            <th class="px-4 py-3 text-left">No.</th>
                            <th class="px-4 py-3 text-left">Ride Type & Classification</th>
                            <th class="px-4 py-3 text-left hidden sm:table-cell">Identification</th>
                            <th class="px-4 py-3 text-left hidden sm:table-cell">Duration</th>
                            <th class="px-4 py-3 text-left hidden md:table-cell">Life Jackets</th>
                            <th class="px-4 py-3 text-left">Total</th>
                            <th class="px-4 py-3 text-left hidden md:table-cell">Start</th>
                            <th class="px-4 py-3 text-left hidden md:table-cell">End</th>
                            <th class="px-4 py-3 text-left hidden md:table-cell">Remaining</th>
                            <th class="px-4 py-3 text-left hidden lg:table-cell">Note</th>
                            <th class="px-4 py-3 text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-blue-100">
                        @foreach ($filteredRides->reverse() as $ride)
                            <tr class="bg-white hover:bg-blue-50 transition-colors duration-200">
                                <td class="px-4 py-3 text-gray-700">
                                    {{ $filteredRides->count() - $loop->iteration + 1 }}
                                </td>
                                <td class="px-4 py-3 lg:w-[200px] lg:min-w-[200px]">
                                    <!-- Combined Ride Type & Classification -->
                                    <div class="text-gray-700 font-bold text-lg">{{ $ride->ride->classification->rideType->name ?? 'Unknown' }}</div>
                                    <div class="text-sm text-blue-600 font-medium">{{ $ride->ride->classification->name ?? 'Unknown' }}</div>
                                    <!-- Mobile view details -->
                                    <div class="lg:hidden space-y-1.5 mt-2">
                                        <!-- Identification -->
                                        <div class="text-xs min-w-[150px]">
                                            <span class="text-green-600 font-medium">ID:</span>
                                            <span class="text-gray-600 font-semibold">{{ $ride->ride->identifier ?? 'Unknown' }}</span>
                                        </div>
                                        <!-- Time info with added top margin to separate from classification -->
                                        <div class="md:hidden text-xs space-y-1 mt-2">
                                            <div class="flex flex-col min-w-[150px]">
                                                <span class="text-indigo-600 font-medium">Starts: <span class="text-gray-600">{{ \Carbon\Carbon::parse($ride->start_at)->format('h:i A') }}</span></span>
                                                
                                            </div>
                                            <div class="flex flex-col min-w-[150px]">
                                                <span class="text-indigo-600 font-medium">End: <span class="text-gray-600">{{ \Carbon\Carbon::parse($ride->end_at)->format('h:i A') }}</span></span>
                                                
                                            </div>
                                            <div class="flex flex-col min-w-[150px] text-sm">
                                                <span class="text-indigo-600 font-medium">Remaining: <span class="text-gray-600 remaining-time font-bold" data-end="{{ \Carbon\Carbon::parse($ride->end_at)->format('Y-m-d H:i:s') }}"></span></span>
                                                
                                            </div>
                                        </div>
                                        
                                        <!-- Duration -->
                                        <div class="sm:hidden text-xs min-w-[150px]">
                                            <span class="text-emerald-600 font-medium">Duration:</span>
                                            <span class="text-gray-600">
                                                @if ($ride->duration_minutes >= 60)
                                                    {{ intdiv($ride->duration_minutes, 60) }}hr{{ intdiv($ride->duration_minutes, 60) > 1 ? 's' : '' }}
                                                    @if ($ride->duration_minutes % 60 > 0)
                                                        {{ $ride->duration_minutes % 60 }}min
                                                    @endif
                                                @else
                                                    {{ $ride->duration_minutes }}min
                                                @endif
                                            </span>
                                        </div>

                                        <!-- Jackets -->
                                        <div class="md:hidden text-xs min-w-[150px]">
                                            <span class="text-emerald-600 font-medium">Jackets:</span>
                                            <span class="text-gray-600">{{ $ride->life_jacket_quantity }}</span>
                                        </div>
                                        
                                        <!-- Note moved to bottom -->
                                        <div class="text-xs min-w-[150px] max-w-[200px]">
                                            <span class="text-purple-600 font-medium">Note:</span>
                                            <span class="text-gray-600 truncate block" title="{{ $ride->note ?? '-' }}">{{ $ride->note ?? '-' }}</span>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-3 text-gray-700 hidden sm:table-cell">
                                    <div class="font-semibold text-gray-800">{{ $ride->ride->identifier ?? 'Unknown' }}</div>
                                </td>
                                <td class="px-4 py-3 text-gray-700 hidden sm:table-cell">
                                    @if ($ride->duration_minutes >= 60)
                                        {{ intdiv($ride->duration_minutes, 60) }}hr{{ intdiv($ride->duration_minutes, 60) > 1 ? 's' : '' }}
                                        @if ($ride->duration_minutes % 60 > 0)
                                            {{ $ride->duration_minutes % 60 }}min
                                        @endif
                                    @else
                                        {{ $ride->duration_minutes }}min
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-gray-700 text-center hidden md:table-cell">
                                    {{ $ride->life_jacket_quantity }}
                                </td>
                                <td class="px-4 py-3 text-gray-700">
                                    <span class="font-bold pr-1">₱</span>{{ number_format($ride->computed_total, 2) }}
                                </td>
                                <td class="px-4 py-3 text-gray-700 hidden md:table-cell">
                                    {{ \Carbon\Carbon::parse($ride->start_at)->format('h:i A') }}
                                </td>
                                <td class="px-4 py-3 text-gray-700 hidden md:table-cell timeEnd">
                                    {{ \Carbon\Carbon::parse($ride->end_at)->format('h:i A') }}
                                </td>
                                <td class="px-4 py-3 hidden md:table-cell remaining-time" 
                                    data-end="{{ \Carbon\Carbon::parse($ride->end_at)->format('Y-m-d H:i:s') }}"
                                    data-marked="{{ isset($markedAsDone[$ride->id]) ? 'true' : 'false' }}">
                                    <!-- Remaining time will be populated by JS -->
                                </td>
                                <td class="px-4 py-3 text-gray-700 hidden lg:table-cell max-w-[200px]">
                                    <div class="truncate" title="{{ $ride->note ?? '-' }}">
                                        {{ $ride->note ?? '-' }}
                                    </div>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <div class="flex flex-col sm:flex-row justify-center space-y-2 sm:space-y-0 sm:space-x-2">
                                        <button wire:click="editRide({{ $ride->id }})" 
                                                class="transition-all duration-200 bg-blue-600 hover:bg-blue-700 text-white px-3 py-1.5 rounded-md shadow-md hover:shadow-lg">
                                            Edit
                                        </button>
                                        <button wire:click="confirmDelete({{ $ride->id }})" 
                                                class="transition-all duration-200 bg-red-500 hover:bg-red-600 text-white px-3 py-1.5 rounded-md shadow-md hover:shadow-lg">
                                            Delete
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mt-6 flex justify-center hidden">
            <button onclick="testTimeOut()" 
                    class="bg-[#00A3E0] text-white py-2.5 px-6 rounded-lg font-medium 
                           transform transition-all duration-200 hover:-translate-y-1 
                           hover:shadow-lg hover:bg-[#0093CC]">
                <div class="flex items-center space-x-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                              d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                    </svg>
                    <span>Test Time Out</span>
                </div>
            </button>
        </div>

        <!-- Create Ride Modal -->
        @if($showAddRides)
            <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
                <div class="flex items-center justify-center min-h-screen p-4">
                    <!-- Background overlay -->
                    <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" 
                         wire:click="$set('showAddRides', false)"></div>

                    <!-- Modal panel -->
                    <div class="relative bg-white rounded-lg shadow-xl transform transition-all w-full max-w-lg">
                        @livewire('add-ride')
                    </div>
                </div>
            </div>
        @endif

        <!-- Edit Ride Modal -->
        @if($showEditModal)
            <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
                <div class="flex items-center justify-center min-h-screen p-4">
                    <!-- Background overlay -->
                    <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" 
                         wire:click="$set('showEditModal', false)"></div>

                    <!-- Modal panel -->
                    <div class="relative bg-white rounded-lg shadow-xl transform transition-all w-full max-w-lg">
                        @livewire('edit-ride', ['rentalId' => $editingRideId])
                    </div>
                </div>
            </div>
        @endif

        <!-- Delete Confirmation Modal -->
        @if ($showModal)
            <div class="fixed z-50 inset-0 bg-gray-900 bg-opacity-60 flex items-center justify-center">
                <div class="bg-white rounded-lg shadow-xl p-6 max-w-md mx-auto">
                    <div class="text-center">
                        <svg class="w-16 h-16 text-red-600 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <h3 class="text-xl font-normal text-gray-500 mb-6">{{ $modalDetails }}</h3>
                        <div class="flex justify-center space-x-3">
                            <button wire:click="deleteRide"
                                    class="bg-red-500 text-white px-5 py-2 rounded-lg font-medium
                                           transform transition-all duration-200 hover:-translate-y-1 
                                           hover:shadow-md hover:bg-red-600">
                                Yes, I'm sure
                            </button>
                            <button wire:click="closeModal"
                                    class="bg-gray-100 text-gray-700 px-5 py-2 rounded-lg font-medium
                                           transform transition-all duration-200 hover:-translate-y-1 
                                           hover:shadow-md hover:bg-gray-200">
                                No, cancel
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- Confirmation Modal for Alarm -->
        

        <div id="modalConfirmAlarm" class="fixed hidden z-50 inset-0 bg-gray-900 bg-opacity-60 overflow-y-auto h-full w-full">
            <div class="flex items-center justify-center min-h-screen p-4">
                <div class="w-full max-w-lg p-6 bg-white rounded-lg relative overflow-hidden border-double border-4 border-[#00A3E0]">
                    <!-- Alarm icon -->
                    <div class="flex justify-center mb-6">
                        <div class="rounded-full bg-blue-100 p-3 ring-8 ring-blue-50">
                            <svg class="w-12 h-12 text-blue-500 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                      d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                            </svg>
                        </div>
                    </div>

                    <!-- Message -->
                    <div class="text-center mb-8">
                        <h3 class="text-2xl font-bold text-[#00A3E0] mb-2">Ride Time Alert! ⏰</h3>
                        <p id="modalRideDetails" class="text-lg text-gray-600">
                            <!-- Content will be populated by JavaScript -->
                        </p>
                        
                    </div>

                    <!-- Button -->
                    <div class="flex justify-center">
                        <button onclick="closeModal('modalConfirmAlarm')" 
                                class="bg-[#00A3E0] text-white px-8 py-3 rounded-xl
                                       font-semibold transform transition-all duration-200 
                                       hover:bg-[#0093CC] hover:-translate-y-0.5 
                                       hover:shadow-lg active:translate-y-0 
                                       focus:ring-4 focus:ring-blue-200 focus:outline-none">
                            Confirm & Stop Alarm
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    let alarmAudio = null;

    function testTimeOut() {
        const modalDetails = `<span class="font-bold text-red-500">Test Ride</span> - <span class="font-bold text-red-500">Test Classification</span> (Test ID) has ended.`;
        document.getElementById('modalRideDetails').innerHTML = modalDetails;
        playAlarmSound();
        openModal('modalConfirmAlarm');
    }

    window.openModal = function(modalId) {
        document.getElementById(modalId).style.display = 'block'
        document.getElementsByTagName('body')[0].classList.add('overflow-y-hidden')
    }

    window.closeModal = function(modalId) {
        document.getElementById(modalId).style.display = 'none'
        document.getElementsByTagName('body')[0].classList.remove('overflow-y-hidden')
        if (alarmAudio) {
            alarmAudio.pause();
            alarmAudio.currentTime = 0;
        }
    }

    // Close all modals when press ESC
    document.onkeydown = function(event) {
        event = event || window.event;
        if (event.keyCode === 27) {
            document.getElementsByTagName('body')[0].classList.remove('overflow-y-hidden')
            let modals = document.getElementsByClassName('modal');
            Array.prototype.slice.call(modals).forEach(i => {
                i.style.display = 'none'
            })
            if (alarmAudio) {
                alarmAudio.pause();
                alarmAudio.currentTime = 0;
            }
        }
    };

    function checkAlarms() {
        const currentTime = new Date();
        const currentTimeString = currentTime.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });

        const timeEnds = document.querySelectorAll('.timeEnd');

        timeEnds.forEach((element) => {
            const timeEnd = element.textContent.trim();
            
            if (timeEnd === currentTimeString) {
                const rideRow = element.closest('tr');
                const rideType = rideRow.querySelector('td:nth-child(2) > div:first-child').textContent.trim();
                const classification = rideRow.querySelector('td:nth-child(2) > div:nth-child(2)').textContent.trim();
                const identification = rideRow.querySelector('td:nth-child(3) > div').textContent.trim();

                const modalDetails = `<span class="font-bold text-red-500">${rideType}</span> - <span class="font-bold text-red-500">${classification}</span> (${identification}) has ended.`;
                document.getElementById('modalRideDetails').innerHTML = modalDetails;

                playAlarmSound();
                openModal('modalConfirmAlarm');
            }
        });
    }

    function playAlarmSound() {
        if (!alarmAudio) {
            alarmAudio = new Audio('/sound/alarm.mp3');
            alarmAudio.loop = true;
        }
        alarmAudio.play();
    }

    // Start checking alarms when document loads
    document.addEventListener('DOMContentLoaded', function() {
        checkAlarms();
        setInterval(checkAlarms, 60000); // Check every minute
    });

    function updateRemainingTimes() {
        const now = new Date();
        now.setDate(now.getDate());
        
        document.querySelectorAll('.remaining-time').forEach(cell => {
            // Check if marked as done
            const isMarked = cell.dataset.marked === 'true';
            if (isMarked) {
                cell.textContent = 'Ended';
                cell.classList.remove('text-green-600');
                cell.classList.add('text-red-500');
                return;
            }

            // Regular remaining time calculation
            const endTimeStr = cell.dataset.end;
            const endTime = new Date(endTimeStr);
            
            if (endTime > now) {
                const diff = endTime - now;
                const minutes = Math.floor(diff / (1000 * 60));
                const hours = Math.floor(minutes / 60);
                const remainingMinutes = minutes % 60;
                
                let timeText = '';
                if (hours > 0) {
                    timeText += `${hours}h `;
                }
                if (remainingMinutes > 0 || hours === 0) {
                    timeText += `${remainingMinutes}m`;
                }
                
                cell.textContent = timeText.trim();
                cell.classList.remove('text-red-500');
                cell.classList.add('text-green-600');
            } else {
                cell.textContent = 'Ended';
                cell.classList.remove('text-green-600');
                cell.classList.add('text-red-500');
            }
        });
    }

    // Update every second for more accurate countdown
    document.addEventListener('DOMContentLoaded', function() {
        updateRemainingTimes();
        setInterval(updateRemainingTimes, 1000);
    });

</script>
