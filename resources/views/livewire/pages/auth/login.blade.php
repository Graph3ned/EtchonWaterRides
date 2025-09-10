<?php

use App\Livewire\Forms\LoginForm;
use Illuminate\Support\Facades\Session;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component
{
    public LoginForm $form;

    /**
     * Handle an incoming authentication request.
     */
    public function login(): void
    {
        $this->validate();

        $this->form->authenticate();

        Session::regenerate();

        $this->redirectIntended(default: route('dashboard', absolute: false));
    }
}; ?>

<div class="min-h-screen flex items-center justify-center p-4">
    <div class="w-full max-w-xs bg-white rounded-lg shadow-xl p-6 relative overflow-hidden">
        <!-- Decorative wave elements -->
        <div class="absolute top-0 left-0 right-0 h-1 bg-gradient-to-r from-cyan-400 via-blue-500 to-cyan-400"></div>
        
        <!-- Water-themed title -->
        <h2 class="text-2xl font-bold text-center mb-6 text-blue-800">🌊 Etchon Water Rides</h2>

        <!-- Session Status -->
        <x-auth-session-status class="mb-4" :status="session('status')" />

        <form wire:submit="login" class="space-y-4">
            <!-- Username -->
            <div>
                <x-input-label for="username" :value="__('Username')" class="text-blue-800 font-medium text-sm" />
                <x-text-input wire:model="form.username" 
                    id="username" 
                    class="block mt-1 w-full text-sm rounded-md border-blue-300 focus:border-blue-500 focus:ring-blue-500 shadow-sm" 
                    type="text" 
                    name="username" 
                    required 
                    autofocus 
                    autocomplete="username" />
                <x-input-error :messages="$errors->get('form.username')" class="mt-1" />
            </div>

            <!-- Password -->
            <div>
                <x-input-label for="password" :value="__('Password')" class="text-blue-800 font-medium text-sm" />
                <x-text-input wire:model="form.password" 
                    id="password" 
                    class="block mt-1 w-full text-sm rounded-md border-blue-300 focus:border-blue-500 focus:ring-blue-500 shadow-sm"
                    type="password"
                    name="password"
                    required 
                    autocomplete="current-password" />
                <x-input-error :messages="$errors->get('form.password')" class="mt-1" />
            </div>

            <div class="flex items-center justify-between">
                <p class="text-xs text-blue-600 text-center">
                    Let's get you in!
                </p>

                <x-primary-button class="bg-blue-600 hover:bg-blue-700 transition-colors duration-200 rounded-md px-4 py-2 text-sm">
                    {{ __('Dive In') }}
                </x-primary-button>
            </div>
        </form>

        <!-- Decorative bottom wave -->
        <div class="absolute bottom-0 left-0 right-0 h-1 bg-gradient-to-r from-cyan-400 via-blue-500 to-cyan-400"></div>
    </div>
</div>
