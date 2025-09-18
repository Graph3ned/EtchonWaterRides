<div class="relative flex flex-col w-full h-full text-slate-300 shadow-md rounded-lg bg-clip-border">
    <div class="flex justify-between items-center p-6 pb-0">
        <div>
            <a wire:navigate href="/admin/add-ride">
                <button class="rounded-md bg-gradient-to-tr from-slate-800 to-slate-700 py-2 px-4 border border-transparent text-center text-sm text-white transition-all shadow-md hover:shadow-lg focus:bg-slate-700 focus:shadow-none active:bg-slate-700 hover:bg-slate-100 active:shadow-none disabled:pointer-events-none disabled:opacity-50 disabled:shadow-none" type="button">
                    Add Ride Price
                </button>
            </a>
        </div>

    </div>
    
    <table class="text-xs text-gray-500 dark:text-gray-400 m-6 border border-slate-600">
        <thead class="text-xs text-gray-700 bg-gray-50 dark:bg-gray-700 dark:text-gray-400 text-center uppercase">
            <tr>
                <th class="p-4 border-b border-slate-600 bg-slate-700 text-center">
                    <p class="text-sm font-normal leading-none text-white">
                        Number
                    </p>
                </th>
                <th class="p-2 border-b border-slate-600 bg-slate-700 text-center">
                    <p class="text-sm font-normal leading-none text-white">
                        Ride Type
                    </p>
                </th>
                <th class="p-2 border-b border-slate-600 bg-slate-700">
                    <p class="text-sm font-normal leading-none text-white">
                        Classification
                    </p>
                </th>
                <th class="p-2 border-b border-slate-600 bg-slate-700">
                    <p class="text-sm font-normal leading-none text-white">
                        Price Per Hour
                    </p>
                </th>
                <th class="p-2 border-b border-slate-600 bg-slate-700">
                    <p class="text-sm font-normal leading-none text-white">
                        Action
                    </p>
                </th>
            </tr>
        </thead>
        <tbody>
        @foreach ($prices as $price)
            <tr class="odd:bg-white odd:dark:bg-gray-900 even:bg-gray-50 even:dark:bg-gray-800 border-b dark:border-gray-700 border border-slate-600 text-black text-center">
                <td class="px-2 py-2 text-center">{{ $loop->iteration }}</td>
                <td class="px-2 py-2 bold text-sm text-center">{{ $price->ride_type }}</td>
                <td class="px-2 py-2">{{ $price->classification }}</td>
                <td class="px-2 py-2">{{ $price->price_per_hour }}</td>
                <td class="px-2 py-2">
                    <button wire:navigate href="/admin/{{$price->id}}/edit" type="button" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-3 py-1 me-2 mb-2 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800">edit</button>
                    <button onclick="openModal('modelConfirm')" type="button" class="focus:outline-none text-white bg-red-700 hover:bg-red-800 focus:ring-4 focus:ring-red-300 font-medium rounded-lg text-sm px-3 py-1 me-2 mb-2 dark:bg-red-600 dark:hover:bg-red-700 dark:focus:ring-red-900">delete</button>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
    <div class="relative overflow-hidden shadow-md mx-auto max-w-full">
                <div id="modelConfirm" class="fixed hidden z-50 inset-0 bg-gray-900 bg-opacity-60 overflow-y-auto h-full w-full px-4 ">
                    <div class="relative top-40 mx-auto shadow-xl rounded-md bg-white max-w-md">

                        <div class="flex justify-end p-2">
                            <button onclick="closeModal('modelConfirm')" type="button"
                                class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm p-1.5 ml-auto inline-flex items-center">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd"
                                        d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                                        clip-rule="evenodd"></path>
                                </svg>
                            </button>
                        </div>

                        <div class="p-6 pt-0 text-center">
                            <svg class="w-20 h-20 text-red-600 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <h3 class="text-xl font-normal text-gray-500 mt-5 mb-6">Are you sure you want to delete this price?</h3>
                            <a href="#"  onclick="closeModal('modelConfirm')" wire:click="deleteride({{$price->id}})"
                                class="text-white bg-red-600 hover:bg-red-800 focus:ring-4 focus:ring-red-300 font-medium rounded-lg text-base inline-flex items-center px-3 py-2.5 text-center mr-2">
                                Yes, I'm sure
                            </a>
                            <a href="#" onclick="closeModal('modelConfirm')"
                                class="text-gray-900 bg-white hover:bg-gray-100 focus:ring-4 focus:ring-cyan-200 border border-gray-200 font-medium inline-flex items-center rounded-lg text-base px-3 py-2.5 text-center"
                                data-modal-toggle="delete-user-modal">
                                No, cancel
                            </a>
                        </div>

                </div>
            </div>
</div>
<script type="text/javascript">
    window.openModal = function(modalId) {
        document.getElementById(modalId).style.display = 'block'
        document.getElementsByTagName('body')[0].classList.add('overflow-y-hidden')
    }

    window.closeModal = function(modalId) {
        document.getElementById(modalId).style.display = 'none'
        document.getElementsByTagName('body')[0].classList.remove('overflow-y-hidden')
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
        }
    };
</script>