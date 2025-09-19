<x-app-layout>
    <x-slot name="header">
    <div class="flex justify-between items-center">
            <h2 class="font-semibold text-2xl text-blue-800 dark:text-blue-300 leading-tight">
                {{ __('Staffs') }}
            </h2>
        </div>
    </x-slot>

    <div class="py-12">
        
        <div class="mx-auto max-w-7xl px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                @livewire('staffs')
            </div>
        </div>
    </div>
</x-app-layout>
