<x-app-layout>
    <x-slot name="header">
    <div class="flex justify-between items-center">
            <h2 class="font-semibold text-2xl text-blue-800 dark:text-blue-300 leading-tight">
                {{ __('Rides Rate') }}
            </h2>
        </div>
    </x-slot>

    <div class="py-12">
        
        <div class="mx-auto max-w-xl px-8">
            <div class=" dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                @livewire('price-edit', ['id'=>$id])
            </div>
        </div>
    </div>
</x-app-layout>
