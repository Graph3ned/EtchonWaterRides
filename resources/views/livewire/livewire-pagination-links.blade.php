@if ($paginator->hasPages())
    <ul class="flex justify-center items-center space-x-1 sm:space-x-2 mb-6">
        {{-- Previous Page --}}
        @if ($paginator->onFirstPage())
            <li class="disabled text-gray-500 cursor-not-allowed">
                <a href="javascript:;" class="text-xs sm:text-sm px-2 py-1 sm:px-4 sm:py-2 border rounded-lg bg-gray-200 text-gray-400 cursor-not-allowed">Prev</a>
            </li>
        @else
            <li>
                <a href="javascript:void(0);" wire:click="previousPage" wire:click="$dispatch('refreshPage')" class="text-xs sm:text-sm px-2 py-1 sm:px-4 sm:py-2 border rounded-lg bg-blue-500 text-white hover:bg-blue-600">Prev</a>
            </li>
        @endif

        {{-- First Page --}}
        @if($paginator->currentPage() > 3)
            <li>
                <a href="javascript:void(0);" wire:click="gotoPage(1)" wire:click="$dispatch('refreshPage')" class="text-xs sm:text-sm px-2 py-1 sm:px-4 sm:py-2 border rounded-lg bg-gray-200 text-gray-700 hover:bg-blue-200">1</a>
            </li>
            <li class="text-xs sm:text-sm px-2 py-1">
                <span class="text-gray-500">...</span>
            </li>
        @endif

        {{-- Pagination Elements --}}
        @foreach ($elements as $element)
            @if (is_array($element))
                @foreach ($element as $page => $url)
                    @if ($page == $paginator->currentPage() || 
                        $page == $paginator->currentPage() - 1 || 
                        $page == $paginator->currentPage() + 1)
                        <li>
                            <a href="javascript:void(0);" 
                               wire:click="gotoPage({{ $page }})" 
                               wire:click="$dispatch('refreshPage')"
                               class="text-xs sm:text-sm px-2 py-1 sm:px-4 sm:py-2 border rounded-lg {{ $page == $paginator->currentPage() ? 'bg-blue-500 text-white font-semibold' : 'bg-gray-200 text-gray-700 hover:bg-blue-200' }}">
                                {{ $page }}
                            </a>
                        </li>
                    @endif
                @endforeach
            @endif
        @endforeach

        {{-- Last Page --}}
        @if($paginator->currentPage() < $paginator->lastPage() - 2)
            <li class="text-xs sm:text-sm px-2 py-1">
                <span class="text-gray-500">...</span>
            </li>
            <li>
                <a href="javascript:void(0);" wire:click="gotoPage({{ $paginator->lastPage() }})" wire:click="$dispatch('refreshPage')" class="text-xs sm:text-sm px-2 py-1 sm:px-4 sm:py-2 border rounded-lg bg-gray-200 text-gray-700 hover:bg-blue-200">{{ $paginator->lastPage() }}</a>
            </li>
        @endif

        {{-- Next Page --}}
        @if ($paginator->hasMorePages())
            <li>
                <a href="javascript:void(0);" wire:click="nextPage" wire:click="$dispatch('refreshPage')" class="text-xs sm:text-sm px-2 py-1 sm:px-4 sm:py-2 border rounded-lg bg-blue-500 text-white hover:bg-blue-600">Next</a>
            </li>
        @else
            <li class="disabled text-gray-500 cursor-not-allowed">
                <a href="javascript:;" class="text-xs sm:text-sm px-2 py-1 sm:px-4 sm:py-2 border rounded-lg bg-gray-200 text-gray-400 cursor-not-allowed">Next</a>
            </li>
        @endif
    </ul>
@endif
