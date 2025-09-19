<?php

use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component
{
    public string $name = '';
    public string $username = '';
    public string $email = '';
    public string $password = '';
    public string $password_confirmation = '';

    /**
     * Handle an incoming registration request.
     */
    public function register(): void
    {
        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:255', 'alpha_dash', 'unique:'.User::class],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'string', 'confirmed', Rules\Password::defaults()],
        ]);

        // Check if email domain is valid
        if (!$this->validateEmailDomain($validated['email'])) {
            $this->addError('email', 'This email domain is not valid or cannot receive emails.');
            return;
        }

        $validated['password'] = Hash::make($validated['password']);

        event(new Registered($user = User::create($validated)));

        // Auth::login($user);

        $this->redirect('staffs');

        // event(new Registered($user = User::create($validated)));

        // Auth::login($user);

        // $this->redirect(route('dashboard', absolute: false), navigate: true);
    }

    /**
     * Validate email domain by checking MX records
     */
    private function validateEmailDomain(string $email): bool
    {
        try {
            // First check if email format is valid
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                return false;
            }

            // Extract domain from email
            $domain = substr(strrchr($email, "@"), 1);
            
            // Check if domain has valid MX records (can receive emails)
            if (!checkdnsrr($domain, 'MX')) {
                return false;
            }

            // Check for common disposable email domains
            $disposableDomains = [
                '10minutemail.com', 'tempmail.org', 'guerrillamail.com', 
                'mailinator.com', 'temp-mail.org', 'throwaway.email',
                'yopmail.com', 'maildrop.cc', 'sharklasers.com'
            ];
            
            if (in_array(strtolower($domain), $disposableDomains)) {
                return false;
            }

            // Additional validation: check if domain is not obviously fake
            if (strlen($domain) < 3 || strpos($domain, '.') === false) {
                return false;
            }

            return true;
        } catch (\Exception $e) {
            return false;
        }
    }
}; ?>

<div class="min-h-screen flex items-center justify-center p-4">
    <div class="w-full max-w-md bg-white rounded-lg shadow-xl p-6 relative overflow-hidden">
        <!-- Decorative wave elements -->
        <div class="absolute top-0 left-0 right-0 h-1 bg-gradient-to-r from-cyan-400 via-blue-500 to-cyan-400"></div>
        
        <!-- Water-themed title -->
        <h2 class="text-2xl font-bold text-center mb-6 text-blue-800">🌊 Add Staff</h2>

        <form wire:submit="register" class="space-y-4">
            <!-- Name -->
            <div>
                <x-input-label for="name" :value="__('Name')" class="text-blue-800 font-medium text-sm" />
                <x-text-input wire:model="name" 
                    id="name" 
                    class="block mt-1 w-full text-sm rounded-md border-blue-300 focus:border-blue-500 focus:ring-blue-500 shadow-sm" 
                    type="text" 
                    name="name" 
                    required 
                    autofocus 
                    autocomplete="name" />
                <x-input-error :messages="$errors->get('name')" class="mt-1" />
            </div>

            <!-- Username -->
            <div>
                <x-input-label for="username" :value="__('Username')" class="text-blue-800 font-medium text-sm" />
                <x-text-input wire:model="username" 
                    id="username" 
                    class="block mt-1 w-full text-sm rounded-md border-blue-300 focus:border-blue-500 focus:ring-blue-500 shadow-sm" 
                    type="text" 
                    name="username" 
                    required 
                    autocomplete="username" />
                <x-input-error :messages="$errors->get('username')" class="mt-1" />
            </div>

            <!-- Email -->
            <div>
                <x-input-label for="email" :value="__('Email')" class="text-blue-800 font-medium text-sm" />
                <x-text-input wire:model="email" 
                    id="email" 
                    class="block mt-1 w-full text-sm rounded-md border-blue-300 focus:border-blue-500 focus:ring-blue-500 shadow-sm" 
                    type="email" 
                    name="email" 
                    required 
                    autocomplete="email" />
                <x-input-error :messages="$errors->get('email')" class="mt-1" />
            </div>

            <!-- Password -->
            <div>
                <x-input-label for="password" :value="__('Password')" class="text-blue-800 font-medium text-sm" />
                <x-text-input wire:model="password" 
                    id="password" 
                    class="block mt-1 w-full text-sm rounded-md border-blue-300 focus:border-blue-500 focus:ring-blue-500 shadow-sm"
                    type="password"
                    name="password"
                    required 
                    autocomplete="new-password" />
                <x-input-error :messages="$errors->get('password')" class="mt-1" />
            </div>

            <!-- Confirm Password -->
            <div>
                <x-input-label for="password_confirmation" :value="__('Confirm Password')" class="text-blue-800 font-medium text-sm" />
                <x-text-input wire:model="password_confirmation" 
                    id="password_confirmation" 
                    class="block mt-1 w-full text-sm rounded-md border-blue-300 focus:border-blue-500 focus:ring-blue-500 shadow-sm"
                    type="password"
                    name="password_confirmation" 
                    required 
                    autocomplete="new-password" />
                <x-input-error :messages="$errors->get('password_confirmation')" class="mt-1" />
            </div>

            <div class="flex items-center justify-between mt-6">
                <button type="button"
                    wire:navigate 
                    href="/admin/staffs"
                    class="bg-[#FF8C00] text-white px-4 py-2 rounded-md text-sm
                           transform transition-all duration-200 hover:-translate-y-1 
                           hover:shadow-md hover:bg-[#E67E00]">
                    {{ __('Cancel') }}
                </button>

                <x-primary-button class="bg-blue-600 hover:bg-blue-700 transition-colors duration-200 rounded-md px-4 py-2 text-sm">
                    {{ __('Register') }}
                </x-primary-button>
            </div>
        </form>

        <!-- Decorative bottom wave -->
        <div class="absolute bottom-0 left-0 right-0 h-1 bg-gradient-to-r from-cyan-400 via-blue-500 to-cyan-400"></div>
    </div>
</div>
