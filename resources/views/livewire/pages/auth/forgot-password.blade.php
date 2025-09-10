<?php

use Illuminate\Support\Facades\Password;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component
{
    public string $email = '';

    /**
     * Send a password reset link to the provided email address.
     */
    public function sendPasswordResetLink(): void
    {
        $this->validate([
            'email' => ['required', 'string', 'email'],
        ]);

        // We will send the password reset link to this user. Once we have attempted
        // to send the link, we will examine the response then see the message we
        // need to show to the user. Finally, we'll send out a proper response.
        $status = Password::sendResetLink(
            $this->only('email')
        );

        if ($status != Password::RESET_LINK_SENT) {
            $this->addError('email', __($status));

            return;
        }

        $this->reset('email');

        session()->flash('status', __($status));
    }
}; ?>

<div class="min-h-screen flex items-center justify-center p-4">
    <div class="w-full max-w-xs bg-white rounded-lg shadow-xl p-6 relative overflow-hidden">
        <!-- Decorative wave elements -->
        <div class="absolute top-0 left-0 right-0 h-1 bg-gradient-to-r from-cyan-400 via-blue-500 to-cyan-400"></div>
        
        <!-- Water-themed title -->
        <h2 class="text-2xl font-bold text-center mb-6 text-blue-800">🌊 Forgot Password</h2>

        <div class="mb-4 text-sm text-blue-700">
            {{ __('No problem! Just enter your email and we’ll send you a password reset link.') }}
        </div>

        <!-- Session Status -->
        <x-auth-session-status class="mb-4" :status="session('status')" />

        <form wire:submit="sendPasswordResetLink" class="space-y-4">
            <!-- Email Address -->
            <div>
                <x-input-label for="email" :value="__('Email')" class="text-blue-800 font-medium text-sm" />
                <x-text-input 
                    wire:model="email" 
                    id="email" 
                    class="block mt-1 w-full text-sm rounded-md border-blue-300 focus:border-blue-500 focus:ring-blue-500 shadow-sm" 
                    type="email" 
                    name="email" 
                    required 
                    autofocus 
                />
                <x-input-error :messages="$errors->get('email')" class="mt-1" />
            </div>

            <div class="flex items-center justify-end">
                <x-primary-button class="bg-blue-600 hover:bg-blue-700 transition-colors duration-200 rounded-md px-4 py-2 text-sm">
                    {{ __('Email Password Reset Link') }}
                </x-primary-button>
            </div>
        </form>

        <!-- Decorative bottom wave -->
        <div class="absolute bottom-0 left-0 right-0 h-1 bg-gradient-to-r from-cyan-400 via-blue-500 to-cyan-400"></div>
    </div>
</div>

