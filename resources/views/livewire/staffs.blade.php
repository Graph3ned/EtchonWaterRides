<div class="min-h-full p-4 border-double border-4 border-[#00A3E0]">
    <div class="w-full rounded-lg p-6 relative overflow-hidden">
        <!-- Success Message -->
        @if (session()->has('message'))
            <div class="mb-4 p-4 bg-green-100 text-green-700 rounded-lg">
                {{ session('message') }}
            </div>
        @endif

        <!-- Clean title with logo blue -->
        <h2 class="text-2xl font-bold text-center mb-6 text-[#00A3E0]">🌊 Staff Management</h2>

        <!-- Add Staff Button -->
        <div class="mb-6">
            <button wire:navigate 
                    href="/admin/staff-register" 
                    class="bg-[#00A3E0] text-white py-2.5 px-6 rounded-lg font-medium 
                           transform transition-all duration-200 hover:-translate-y-1 
                           hover:shadow-lg hover:bg-[#0093CC]">
                <span class="flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Add Staff
                </span>
            </button>
        </div>

        <!-- Staff Table -->
        <div class="overflow-x-auto relative shadow-md sm:rounded-lg">
            <div class="overflow-x-auto">
                <div class="inline-block min-w-full align-middle">
                    <div class="overflow-hidden border border-gray-200 rounded-lg">
                        <table class="min-w-full divide-y divide-gray-200 table-fixed">
                            <thead class="bg-[#00A3E0]">
                                <tr>
                                    <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-white uppercase">
                                        Name
                                    </th>
                                    <th scope="col" class="px-6 py-4 text-center text-xs font-semibold text-white uppercase hidden sm:table-cell">
                                        Username
                                    </th>
                                    <th scope="col" class="px-6 py-4 text-right text-xs font-semibold text-white uppercase">
                                        Actions
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($staffs as $staff)
                                    @if($staff->userType == 0)
                                        <tr class="hover:bg-gray-50 transition-colors duration-200">
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm font-medium text-gray-900">
                                                    {{$staff->name}}
                                                </div>
                                                <!-- Show email on mobile -->
                                                <div class="text-sm text-gray-500 sm:hidden">
                                                    {{$staff->username}}
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-center hidden sm:table-cell">
                                                <div class="text-sm text-gray-500">
                                                    {{$staff->username}}
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-right">
                                                <div class="flex justify-end space-x-2">
                                                    <button wire:navigate 
                                                            href="/admin/staff-edit/{{$staff->id}}"
                                                            class="inline-flex items-center px-3 py-1.5
                                                                   bg-[#00A3E0] hover:bg-[#0093CC] text-white 
                                                                   rounded-lg transition-all duration-200 text-sm
                                                                   shadow hover:shadow-md transform hover:-translate-y-0.5">
                                                        <span class="hidden sm:inline">Edit</span>
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 sm:ml-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                        </svg>
                                                    </button>
                                                    <button wire:click="confirmDelete({{$staff->id}})"
                                                            class="inline-flex items-center px-3 py-1.5
                                                                   bg-red-500 hover:bg-red-600 text-white 
                                                                   rounded-lg transition-all duration-200 text-sm
                                                                   shadow hover:shadow-md transform hover:-translate-y-0.5">
                                                        <span class="hidden sm:inline">Delete</span>
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 sm:ml-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                        </svg>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    @endif
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Modal remains unchanged -->
    @if ($showModal)
    <div class="fixed z-50 inset-0 bg-gray-900 bg-opacity-60 flex items-center justify-center">
        <div class="bg-white rounded-lg shadow-xl p-6 max-w-md mx-auto">
            <div class="text-center">
                <svg class="w-16 h-16 text-red-600 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <h3 class="text-xl font-normal text-gray-500 mb-6">{{ $modalDetails }}</h3>
                <div class="flex justify-center space-x-3">
                    <button wire:click="deleteStaff"
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
</div>
