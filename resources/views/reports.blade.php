<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-2xl text-blue-800 dark:text-blue-300 leading-tight">
                {{ __('Reports Dashboard') }}
            </h2>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="mx-auto max-w-8xl px-4 sm:px-6 lg:px-8">
            
                
                @livewire('reports-dashboard')
                
                <!-- Decorative bottom element -->
                <!-- <div class="h-1 bg-gradient-to-r from-blue-400 via-cyan-500 to-blue-400"></div> -->
            </div>
        </div>
    </div>
</x-app-layout>
