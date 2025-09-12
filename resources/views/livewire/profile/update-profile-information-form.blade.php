<?php

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\Rule;
use Livewire\Volt\Component;
use Illuminate\Support\Facades\Hash;
// cleaned imports: Mail/ProfileChangeRequest/AdminProfileChangeOtp no longer needed

new class extends Component
{
    public string $name = '';
    public string $email = '';
    public string $username = '';
    public string $current_password = '';
    public string $new_password = '';
    public string $new_password_confirmation = '';
    /**
     * Mount the component.
     */
    public function mount(): void
    {
        $this->name = Auth::user()->name;
        $this->email = (string) (Auth::user()->email ?? '');
        $this->username = Auth::user()->username;
    }

    /**
     * Update the profile information for the currently authenticated user.
     */
    public function updateProfileInformation(): void
    {
        $user = Auth::user();

        // Simple, direct updates without OTP or pending state
        if ($user->userType == 1) {
            $validated = $this->validate([
                'name' => ['required', 'string', 'max:255'],
                'username' => ['required', 'string', 'max:255', Rule::unique(User::class)->ignore($user->id)],
                'email' => ['nullable', 'string', 'lowercase', 'email', 'max:255', Rule::unique(User::class, 'email')->ignore($user->id)],
            ]);

            \App\Models\User::query()->where('id', $user->id)->update([
                'name' => $validated['name'],
                'username' => $validated['username'],
                'email' => $validated['email'] ?? $user->email,
            ]);
        } else {
            $validated = $this->validate([
                'username' => ['required', 'string', 'max:255', Rule::unique(User::class)->ignore($user->id)],
                'email' => ['nullable', 'string', 'lowercase', 'email', 'max:255', Rule::unique(User::class, 'email')->ignore($user->id)],
            ]);

            \App\Models\User::query()->where('id', $user->id)->update([
                'username' => $validated['username'],
                'email' => $validated['email'] ?? $user->email,
            ]);
        }

        $this->dispatch('profile-updated', name: $user->name);
    }

    // Removed validateProfileChanges: no longer needed in non-OTP flow

    // Removed sendOtpForAdminChanges: replaced by direct update flow

    // Removed confirmEmailChange: no OTP confirmation in restored flow

    /**
     * Validate email domain by checking MX records
     */
    // Removed validateEmailDomain: not used in restored flow

    /**
     * Send an email verification notification to the current user.
     */
    // public function sendVerification(): void
    // {
    //     $user = Auth::user();

    //     if ($user->hasVerifiedEmail()) {
    //         $this->redirectIntended(default: route('dashboard', absolute: false));

    //         return;
    //     }

    //     $user->sendEmailVerificationNotification();

    //     Session::flash('status', 'verification-link-sent');
    // }
}; ?>

<section>
    
    <header>
        <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
            {{ __('Profile Information') }}
        </h2>

        @if(auth()->check() && auth()->user()->userType == 1)
        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
            {{ __('Choose only the fields you want to change and leave the others unchanged or blank. Changes require OTP verification sent to your current email.') }}
        </p>
        @endif
        
        @if(auth()->check() && auth()->user()->userType == 0)
        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
            {{ __("Choose only the fields you want to change and leave the others unchanged. Email change require OTP verification sent to your current email.") }}
        </p>
        @endif
    </header>
    

    <form wire:submit="updateProfileInformation" class="mt-6 space-y-6">
    <div>
        <x-input-label for="username" :value="__('Username')" />
        <x-text-input wire:model="username" id="username" name="username" type="text" class="mt-1 block w-full" required autofocus autocomplete="username" />
        <x-input-error class="mt-2" :messages="$errors->get('username')" />
    </div>

    <div>
        <x-input-label for="email" :value="__('Email')" />
        <x-text-input wire:model="email" id="email" name="email" type="email" class="mt-1 block w-full" autocomplete="email" />
        <x-input-error class="mt-2" :messages="$errors->get('email')" />
    </div>

    @if(auth()->check() && auth()->user()->userType == 1)
        <div>
            <x-input-label for="name" :value="__('Name')" />
            <x-text-input wire:model="name" id="name" name="name" type="text" class="mt-1 block w-full" required autofocus autocomplete="name" />
            <x-input-error class="mt-2" :messages="$errors->get('name')" />
        </div>

        <div>
            <x-input-label for="current_password" :value="__('Current Password')" />
            <x-text-input wire:model="current_password" id="current_password" name="current_password" type="password" class="mt-1 block w-full" autocomplete="current-password" />
            <x-input-error class="mt-2" :messages="$errors->get('current_password')" />
        </div>
        
        <div>   
            <x-input-label for="new_password" :value="__('New Password')" />
            <x-text-input wire:model="new_password" id="new_password" name="new_password" type="password" class="mt-1 block w-full" autocomplete="new-password" />
            <x-input-error class="mt-2" :messages="$errors->get('new_password')" />
        </div>

        <div>
            <x-input-label for="new_password_confirmation" :value="__('Confirm New Password')" />
            <x-text-input wire:model="new_password_confirmation" id="new_password_confirmation" name="new_password_confirmation" type="password" class="mt-1 block w-full" autocomplete="new-password" />
            <x-input-error class="mt-2" :messages="$errors->get('new_password_confirmation')" />
        </div>
        <div>
            
        </div>
    @endif

        

        <div class="flex items-center gap-4">
            @if (!session('status') || session('status') === 'email-change-confirmed')
                <x-primary-button wire:loading.attr="disabled" wire:target="updateProfileInformation">
                    <span wire:loading.remove wire:target="updateProfileInformation">{{ __('Save') }}</span>
                    <span wire:loading wire:target="updateProfileInformation" class="flex items-center">
                        <!-- <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg> -->
                        {{ __('Saving...') }}
                    </span>
                </x-primary-button>
            @endif

            <x-action-message class="me-3" on="profile-updated, email-change-confirmed">
                {{ __('Saved.') }}
            </x-action-message>
        </div>
    </form>
</section>
