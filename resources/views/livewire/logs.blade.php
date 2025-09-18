<div class="min-h-full">
    <div class="w-full p-4 relative">
        <!-- Clean title with logo blue -->
        <h2 class="text-2xl font-bold text-center mb-6 text-[#00A3E0]">📝 Staff Activity Logs</h2>

        <div class="flex justify-between items-center mb-6 flex-col sm:flex-row space-y-4 sm:space-y-0">
            <div class="flex items-center space-x-3">
                <!-- Log Filter Dropdown -->
                <select wire:model.live="logFilter" 
                        class="border border-blue-200 rounded-lg px-4 py-2.5 text-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-300">
                    <option value="all">Show All Logs</option>
                    <option value="delete">Show Deleted Records</option>
                    <option value="edit">Show Edited Records</option>
                    <option value="create">Show Created Records</option>
                </select>

                <!-- Sales-like Date Range beside the filter -->
                <div class="flex items-center space-x-2">
                    <select wire:model.live="dateRange" 
                            class="border border-blue-200 rounded-lg px-3 py-2.5 text-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-300">
                        <option value="">All Time</option>
                        <option value="today">Today</option>
                        <option value="select_day">Select Day</option>
                        <option value="select_month">Select Month</option>
                        <option value="this_month">Current Month</option>
                        <option value="custom">Custom Range</option>
                    </select>

                    @if($dateRange === 'select_month')
                        <input x-data x-init="flatpickr($el, { plugins: [new monthSelectPlugin({ shorthand: true, dateFormat: 'Y-m', altFormat: 'F Y', theme: 'material_blue' })], dateFormat: 'Y-m', altInput: true, altFormat: 'F Y' })"
                               wire:model.live="selected_month" type="text" placeholder="Select month"
                               class="border border-blue-200 rounded-lg px-3 py-2 text-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-300" />
                    @endif

                    @if($dateRange === 'select_day')
                        <input x-data x-init="flatpickr($el, { dateFormat: 'Y-m-d', altInput: true, altFormat: 'F j, Y', theme: 'material_blue' })"
                               wire:model.live="selected_day" type="text" placeholder="Select date"
                               class="border border-blue-200 rounded-lg px-3 py-2 text-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-300" />
                    @endif

                    @if($dateRange === 'custom')
                        <input x-data x-init="flatpickr($el, { dateFormat: 'Y-m-d', altInput: true, altFormat: 'F j, Y', theme: 'material_blue' })"
                               wire:model.live="start_date" type="text" placeholder="From"
                               class="border border-blue-200 rounded-lg px-3 py-2 text-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-300" />
                        <input x-data x-init="flatpickr($el, { dateFormat: 'Y-m-d', altInput: true, altFormat: 'F j, Y', theme: 'material_blue' })"
                               wire:model.live="end_date" type="text" placeholder="To"
                               class="border border-blue-200 rounded-lg px-3 py-2 text-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-300" />
                    @endif
                </div>
            </div>
        </div>

        <!-- Table Section -->
        <div class="rounded-lg shadow-lg border border-blue-100">
            <div class="max-h-[600px] overflow-y-auto scrollbar-thin scrollbar-thumb-gray-400 scrollbar-track-gray-100">
                <table class="w-full lg:w-full sm:min-w-max table-auto text-sm">
                    <thead class="bg-gradient-to-r from-cyan-500 to-blue-600 text-white text-sm font-semibold sticky top-0 z-10">
                        <tr class="border-b border-blue-400">
                            <th class="px-4 py-3 text-left">Date/Time</th>
                            <th class="px-4 py-3 text-left">Staff</th>
                            <th class="px-4 py-3 text-left">Action</th>
                            <th class="px-4 py-3 text-left">Changes</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-blue-100">
                        @foreach($logs as $log)
                            <tr class="bg-white hover:bg-blue-50 transition-colors duration-200">
                                <td class="px-4 py-3 text-gray-700">
                                    <div class="text-gray-700 font-bold">{{ $log->created_at->format('M d, Y h:i:s A') }}</div>
                                </td>
                                <td class="px-4 py-3 text-gray-700">
                                    <div class="text-gray-700 font-bold">{{ $log->user->name ?? 'N/A' }}</div>
                                </td>
                                <td class="px-4 py-3 text-gray-700">
                                    <div class="text-gray-700 font-bold">{{ ucfirst($log->action) }}</div>
                                </td>
                                <td class="px-4 py-3 text-gray-700">
                                    <div class="space-y-2">
                                        @if($log->action === 'delete')
                                            <div class="text-red-600 font-medium">Record Deleted</div>
                                            <div class="space-y-1.5 mt-2">
                                                @foreach($log->formatted_details as $detail)
                                                    <div class="flex flex-col min-w-[150px]">
                                                        <span class="text-indigo-600 font-medium">{{ $detail['label'] }}: 
                                                            <span class="text-gray-600">{{ $detail['value'] }}</span>
                                                        </span>
                                                    </div>
                                                @endforeach
                                            </div>
                                        @elseif($log->action === 'create')
                                            <div class="text-emerald-600 font-medium">Record Created</div>
                                            <div class="space-y-1.5 mt-2">
                                                @foreach($log->formatted_details as $detail)
                                                    <div class="flex flex-col min-w-[150px]">
                                                        <span class="text-indigo-600 font-medium">{{ $detail['label'] }}: 
                                                            <span class="text-gray-600">{{ $detail['value'] }}</span>
                                                        </span>
                                                    </div>
                                                @endforeach
                                            </div>
                                        @else
                                            <div class="text-blue-600 font-medium">Record Edited</div>
                                            <div class="space-y-1.5 mt-2">
                                                @foreach($log->formatted_details as $detail)
                                                    <div class="flex flex-col min-w-[150px]">
                                                        <span class="text-indigo-600 font-medium">{{ $detail['label'] }}: 
                                                            <span class="text-gray-600">{{ $detail['value'] }}</span>
                                                        </span>
                                                    </div>
                                                @endforeach
                                            </div>
                                            @if(count($log->formatted_changes) > 0)
                                                <div class="mt-3">
                                                    <div class="text-emerald-600 font-medium">Changes Made:</div>
                                                    <div class="space-y-1.5 mt-2">
                                                        @foreach($log->formatted_changes as $change)
                                                            <div class="flex flex-col min-w-[150px]">
                                                                <span class="text-purple-600 font-medium">{{ $change['label'] }}: 
                                                                    <span class="text-gray-600">{{ $change['value'] }}</span>
                                                                </span>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            @endif
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Pagination -->
        <div class="mt-4">
            {{ $logs->links() }}
        </div>
    </div>
</div>
