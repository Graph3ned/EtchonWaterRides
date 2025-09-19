<?php

use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Locked;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component
{
    #[Locked]
    public string $token = '';
    public string $email = '';
    public string $password = '';
    public string $password_confirmation = '';

    /**
     * Mount the component.
     */
    public function mount(string $token): void
    {
        $this->token = $token;

        $this->email = request()->string('email');
    }

    /**
     * Reset the password for the given user.
     */
    public function resetPassword(): void
    {
        $this->validate([
            'token' => ['required'],
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string', 'confirmed', Rules\Password::defaults()],
        ]);

        // Here we will attempt to reset the user's password. If it is successful we
        // will update the password on an actual user model and persist it to the
        // database. Otherwise we will parse the error and return the response.
        $status = Password::reset(
            $this->only('email', 'password', 'password_confirmation', 'token'),
            function ($user) {
                $user->forceFill([
                    'password' => Hash::make($this->password),
                    'remember_token' => Str::random(60),
                ])->save();

                event(new PasswordReset($user));
            }
        );

        // If the password was successfully reset, we will redirect the user back to
        // the application's home authenticated view. If there is an error we can
        // redirect them back to where they came from with their error message.
        if ($status != Password::PASSWORD_RESET) {
            $this->addError('email', __($status));

            return;
        }

        Session::flash('status', __($status));

        $this->redirectRoute('login', navigate: true);
    }
}; ?>

<div class="min-h-screen flex items-center justify-center p-4">
    <div class="w-full max-w-xs bg-white rounded-lg shadow-xl p-6 relative overflow-hidden">
        <!-- Decorative wave elements -->
        <div class="absolute top-0 left-0 right-0 h-1 bg-gradient-to-r from-cyan-400 via-blue-500 to-cyan-400"></div>
        
        <!-- Water-themed title -->
        <h2 class="text-2xl font-bold text-center mb-6 text-blue-800">🌊 Reset Password</h2>

        <form wire:submit="resetPassword" class="space-y-4">
            <!-- Password -->
            <div>
                <x-input-label for="password" :value="__('Password')" class="text-blue-800 font-medium text-sm" />
                <x-text-input 
                    wire:model="password"
                    id="password"
                    class="block mt-1 w-full text-sm rounded-md border-blue-300 focus:border-blue-500 focus:ring-blue-500 shadow-sm"
                    type="password"
                    name="password"
                    required
                    autocomplete="new-password"
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
                    autocomplete="new-password"
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

