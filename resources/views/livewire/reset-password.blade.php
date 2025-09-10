<div class="min-h-screen flex items-center justify-center p-4">
    <div class="w-full max-w-xs bg-white rounded-lg shadow-xl p-6 relative overflow-hidden">
        <!-- Decorative wave elements -->
        <div class="absolute top-0 left-0 right-0 h-1 bg-gradient-to-r from-cyan-400 via-blue-500 to-cyan-400"></div>
        
        <!-- Water-themed title -->
        <h2 class="text-2xl font-bold text-center mb-6 text-blue-800">🌊 Reset Password</h2>

        <div class="mb-4 text-sm text-blue-700">
            {{ __('Enter your new password below.') }}
        </div>

        <!-- Session Status -->
        <x-auth-session-status class="mb-4" :status="session('status')" />

        <form wire:submit="resetPassword" class="space-y-4">
            <!-- Email Address (hidden) -->
            <input type="hidden" wire:model="email" />

            <!-- Password -->
            <div>
                <x-input-label for="password" :value="__('New Password')" class="text-blue-800 font-medium text-sm" />
                <x-text-input 
                    wire:model="password" 
                    id="password" 
                    class="block mt-1 w-full text-sm rounded-md border-blue-300 focus:border-blue-500 focus:ring-blue-500 shadow-sm" 
                    type="password" 
                    name="password" 
                    required 
                    autofocus 
                />
                <x-input-error :messages="$errors->get('password')" class="mt-1" />
            </div>

            <!-- Confirm Password -->
            <div>
                <x-input-label for="password_confirmation" :value="__('Confirm Password')" class="text-blue-800 font-medium text-sm" />
                <x-text-input 
                    wire:model="password_confirmation" 
                    id="password_confirmation" 
                    class="block mt-1 w-full text-sm rounded-md border-blue-300 focus:border-blue-500 focus:ring-blue-500 shadow-sm" 
                    type="password" 
                    name="password_confirmation" 
                    required 
                />
                <x-input-error :messages="$errors->get('password_confirmation')" class="mt-1" />
            </div>

            <div class="flex items-center justify-end">
                <x-primary-button class="bg-blue-600 hover:bg-blue-700 transition-colors duration-200 rounded-md px-4 py-2 text-sm">
                    {{ __('Reset Password') }}
                </x-primary-button>
            </div>
        </form>

        <!-- Decorative bottom wave -->
        <div class="absolute bottom-0 left-0 right-0 h-1 bg-gradient-to-r from-cyan-400 via-blue-500 to-cyan-400"></div>
    </div>
</div>
