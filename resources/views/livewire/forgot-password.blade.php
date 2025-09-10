<div class="min-h-screen flex items-center justify-center p-4">
    <div class="w-full max-w-xs bg-white rounded-lg shadow-xl p-6 relative overflow-hidden">
        <!-- Decorative wave elements -->
        <div class="absolute top-0 left-0 right-0 h-1 bg-gradient-to-r from-cyan-400 via-blue-500 to-cyan-400"></div>
        
        <!-- Water-themed title -->
        <h2 class="text-2xl font-bold text-center mb-6 text-blue-800">🌊 Forgot Password</h2>

        <div class="mb-4 text-sm text-blue-700">
            {{ __('Enter your username and email to receive a password reset link.') }}
        </div>

        <!-- Session Status -->
        <x-auth-session-status class="mb-4" :status="session('status')" />

        @if($emailSent)
            <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded">
                {{ __('Password reset link sent to your email!') }}
            </div>
        @else
            <form wire:submit="sendResetLink" class="space-y-4">
                <!-- Username -->
                <div>
                    <x-input-label for="username" :value="__('Username')" class="text-blue-800 font-medium text-sm" />
                    <x-text-input 
                        wire:model="username" 
                        id="username" 
                        class="block mt-1 w-full text-sm rounded-md border-blue-300 focus:border-blue-500 focus:ring-blue-500 shadow-sm" 
                        type="text" 
                        name="username" 
                        required 
                        autofocus 
                    />
                    <x-input-error :messages="$errors->get('username')" class="mt-1" />
                </div>

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
                    />
                    <x-input-error :messages="$errors->get('email')" class="mt-1" />
                </div>

                <div class="flex items-center justify-between">
                    <a href="{{ route('login') }}" 
                       class="text-blue-600 hover:text-blue-800 text-sm underline">
                        {{ __('Back to Login') }}
                    </a>

                    <x-primary-button class="bg-blue-600 hover:bg-blue-700 transition-colors duration-200 rounded-md px-4 py-2 text-sm">
                        {{ __('Send Reset Link') }}
                    </x-primary-button>
                </div>
            </form>
        @endif

        <!-- Decorative bottom wave -->
        <div class="absolute bottom-0 left-0 right-0 h-1 bg-gradient-to-r from-cyan-400 via-blue-500 to-cyan-400"></div>
    </div>
</div>
