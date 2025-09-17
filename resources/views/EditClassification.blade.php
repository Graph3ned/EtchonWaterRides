<x-app-layout>
    <x-slot name="header">
    <div class="flex justify-between items-center">
            <h2 class="font-semibold text-2xl text-blue-800 dark:text-blue-300 leading-tight">
                {{ __('Edit Classification') }}
            </h2>
        </div>
    </x-slot>

    <div class="py-12">
        
        <div class="mx-auto max-w-7xl px-8">
            <div class=" dark:bg-gray-800 overflow-hidden sm:rounded-lg">
                @livewire('edit-classification', ['classificationId' => $classificationId])
            </div>
        </div>
    </div>
</x-app-layout>
