<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Staff Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        
        <div class="mx-auto max-w-7xl px-8">
            <div class=" dark:bg-gray-800 overflow-hidden sm:rounded-lg">
                @livewire('create-rides-rental')
            </div>
        </div>
    </div>
</x-app-layout>
