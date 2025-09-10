<?php

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\Rule;
use Livewire\Volt\Component;
use Illuminate\Support\Facades\Mail;
use App\Models\EmailChangeRequest;
use App\Mail\AdminEmailChangeOtp;

new class extends Component
{
    public string $name = '';
    public string $email = '';
    public string $username = '';
    public string $otp = '';
    public string $pendingNewEmail = '';
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

        if ($user->userType == 1) {
            $validated = $this->validate([
                'name' => ['required', 'string', 'max:255'],
                'username' => ['required', 'string', 'max:255', Rule::unique(User::class)->ignore($user->id)],
                'email' => ['nullable', 'string', 'lowercase', 'email', 'max:255', Rule::unique(User::class, 'email')->ignore($user->id)],
            ]);

            \App\Models\User::query()->where('id', $user->id)->update([
                'name' => $validated['name'],
                'username' => $validated['username'],
            ]);

            // If email is changing, send OTP to CURRENT email and store pending request instead of immediate update
            if (($validated['email'] ?? null) !== ($user->email ?? null)) {
                $otp = (string) random_int(100000, 999999);
                $expiresAt = now()->addMinutes(10);

                // Clear prior pending
                EmailChangeRequest::where('user_id', $user->id)->whereNull('consumed_at')->delete();

                // Create request first, then attempt to send
                $request = EmailChangeRequest::create([
                    'user_id' => $user->id,
                    'new_email' => $validated['email'],
                    'otp_code' => $otp,
                    'expires_at' => $expiresAt,
                ]);

                try {
                    if (empty($user->email)) {
                        throw new \RuntimeException('Current email is empty; cannot verify.');
                    }
                    // Send OTP to the CURRENT (old) email for ownership verification
                    Mail::to($user->email)->send(new AdminEmailChangeOtp($otp, $user->name));
                    Session::flash('status', 'email-change-otp-sent');
                    // track pending new email and keep the input showing the target new email
                    $this->pendingNewEmail = (string) ($validated['email'] ?? '');
                    $this->email = $this->pendingNewEmail;
                } catch (\Throwable $e) {
                    // Cleanup if email send fails
                    optional($request)->delete();
                    $this->addError('email', 'Unable to send OTP to current email. Please check mail settings and try again.');
                }
            }
        } else {
            $validated = $this->validate([
                'username' => ['required', 'string', 'max:255', Rule::unique(User::class)->ignore($user->id)],
            ]);

            \App\Models\User::query()->where('id', $user->id)->update([
                'username' => $validated['username'],
            ]);
        }

        $this->dispatch('profile-updated', name: $user->name);
    }

    public function confirmEmailChange(): void
    {
        $user = Auth::user();
        if ($user->userType != 1) {
            return;
        }

        $this->validate([
            'otp' => ['required', 'digits:6'],
        ]);

        $request = EmailChangeRequest::where('user_id', $user->id)
            ->whereNull('consumed_at')
            ->latest()
            ->first();

        $provided = trim($this->otp ?? '');
        $expected = (string) ($request->otp_code ?? '');
        if (! $request || $request->expires_at->isPast() || ! hash_equals($expected, $provided)) {
            $this->addError('otp', 'Invalid or expired code.');
            Session::flash('status', 'email-change-otp-invalid');
            // keep showing the target new email until success or cancel
            return;
        }

        \App\Models\User::query()->where('id', $user->id)->update([
            'email' => $request->new_email,
        ]);

        $request->update(['consumed_at' => now()]);

        $this->otp = '';
        $refetched = \App\Models\User::query()->find($user->id);
        $this->email = (string) (($refetched->email ?? ''));
        $this->pendingNewEmail = '';
        Session::flash('status', 'email-change-confirmed');
        $this->dispatch('profile-updated', name: $user->name);
    }

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
            {{ __("Update your account's name and username.") }}
        </p>
        @endif
        
        @if(auth()->check() && auth()->user()->userType == 0)
        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
            {{ __("Update your account's username.") }}
        </p>
        @endif
    </header>
    

    <form wire:submit="updateProfileInformation" class="mt-6 space-y-6">

    <div>
        <x-input-label for="name" :value="__('Name')" />
        <x-text-input wire:model="name" id="name" name="name" type="text" class="mt-1 block w-full" required autofocus autocomplete="name" />
        <x-input-error class="mt-2" :messages="$errors->get('name')" />
    </div>

    <div>
        <x-input-label for="username" :value="__('Username')" />
        <x-text-input wire:model="username" id="username" name="username" type="text" class="mt-1 block w-full" required autofocus autocomplete="username" />
        <x-input-error class="mt-2" :messages="$errors->get('username')" />
    </div>

    @if(auth()->check() && auth()->user()->userType == 1)
        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input wire:model="email" id="email" name="email" type="email" class="mt-1 block w-full" autocomplete="email" />
            <x-input-error class="mt-2" :messages="$errors->get('email')" />

            @if (session('status') === 'email-change-otp-sent' || session('status') === 'email-change-otp-invalid' || $pendingNewEmail)
                <div class="mt-4">
                    <x-input-label for="otp" :value="__('Enter OTP sent to your current email')" />
                    @if ($pendingNewEmail)
                        <p class="text-xs text-gray-500">Pending change to: <span class="font-semibold">{{ $pendingNewEmail }}</span></p>
                    @endif
                    <x-text-input wire:model="otp" id="otp" name="otp" type="text" class="mt-1 block w-full" maxlength="6" />
                    <x-input-error class="mt-2" :messages="$errors->get('otp')" />

                    <x-primary-button wire:click.prevent="confirmEmailChange" class="mt-3">{{ __('Confirm Email Change') }}</x-primary-button>
                </div>
            @endif
        </div>
    @endif

        <div class="flex items-center gap-4">
            <x-primary-button>{{ __('Save') }}</x-primary-button>

            <x-action-message class="me-3" on="profile-updated">
                {{ __('Saved.') }}
            </x-action-message>

            @if (session('status') === 'email-change-confirmed')
                <span class="text-sm text-green-600">{{ __('Saved.') }}</span>
            @endif
        </div>
    </form>
</section>
