<?php

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\Rule;
use Livewire\Volt\Component;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Models\ProfileChangeRequest;
use App\Mail\AdminProfileChangeOtp;

new class extends Component
{
    public string $name = '';
    public string $email = '';
    public string $username = '';
    public string $otp = '';
    public string $pendingNewEmail = '';
    public string $current_password = '';
    public string $new_password = '';
    public string $new_password_confirmation = '';
    public bool $readyForOtp = false;
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
                ProfileChangeRequest::where('user_id', $user->id)->whereNull('consumed_at')->delete();

                // Create request first, then attempt to send
                $request = ProfileChangeRequest::create([
                    'user_id' => $user->id,
                    'new_email' => $validated['email'],
                    'otp_code_hash' => Hash::make($otp),
                    'expires_at' => $expiresAt,
                ]);

                try {
                    if (empty($user->email)) {
                        throw new \RuntimeException('Current email is empty; cannot verify.');
                    }
                    // Send OTP to the CURRENT (old) email for ownership verification
                    Mail::to($user->email)->send(new AdminProfileChangeOtp($otp, $user->name));
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
                'email' => ['nullable', 'string', 'lowercase', 'email', 'max:255', Rule::unique(User::class, 'email')->ignore($user->id)],
            ]);

            $payload = ['updates' => []];
            if ($validated['username'] !== $user->username) {
                $payload['updates']['username'] = $validated['username'];
            }

            $targetEmail = $validated['email'] ?? null;
            if ($targetEmail !== ($user->email ?? null)) {
                // Validate email domain for staff as well
                if ($targetEmail && ! $this->validateEmailDomain($targetEmail)) {
                    $this->addError('email', 'This email domain is not valid or cannot receive emails.');
                    return;
                }
                $payload['updates']['email'] = $targetEmail;
            }

            // If only username changed and no email change, update immediately
            if (empty($payload['updates']['email']) && isset($payload['updates']['username'])) {
                \App\Models\User::query()->where('id', $user->id)->update([
                    'username' => $validated['username'],
                ]);
                $this->dispatch('profile-updated', name: $user->name);
                return;
            }

            // If email is changing, send OTP to current email and store request
            if (!empty($payload['updates']['email'])) {
                $otp = (string) random_int(100000, 999999);
                $expiresAt = now()->addMinutes(10);

                ProfileChangeRequest::where('user_id', $user->id)->whereNull('consumed_at')->delete();

                $request = ProfileChangeRequest::create([
                    'user_id' => $user->id,
                    'new_email' => $targetEmail,
                    'payload' => $payload,
                    'otp_code_hash' => Hash::make($otp),
                    'expires_at' => $expiresAt,
                ]);

                try {
                    if (empty($user->email)) {
                        throw new \RuntimeException('Current email is empty; cannot verify.');
                    }
                    Mail::to($user->email)->send(new AdminProfileChangeOtp($otp, $user->name));
                    Session::flash('status', 'email-change-otp-sent');
                    if ($targetEmail) {
                        $this->pendingNewEmail = (string) $targetEmail;
                        $this->email = $this->pendingNewEmail;
                    }
                    return;
                } catch (\Throwable $e) {
                    optional($request)->delete();
                    $this->addError('email', 'Unable to send OTP to current email. Please check mail settings and try again.');
                    return;
                }
            }

            // Nothing to change
            $this->dispatch('profile-updated', name: $user->name);
        }

        $this->dispatch('profile-updated', name: $user->name);
    }

    public function validateProfileChanges(): void
    {
        $user = Auth::user();
        $this->readyForOtp = false;

        if ($user->userType == 1) {
            $validated = $this->validate([
                'name' => ['required', 'string', 'max:255'],
                'username' => ['required', 'string', 'max:255', Rule::unique(User::class)->ignore($user->id)],
                'email' => ['nullable', 'string', 'lowercase', 'email', 'max:255', Rule::unique(User::class, 'email')->ignore($user->id)],
                'current_password' => ['nullable', 'string', 'min:8'],
                'new_password' => ['nullable', 'string', 'min:8', 'same:new_password_confirmation'],
                'new_password_confirmation' => ['nullable', 'string', 'min:8'],
            ]);

            // Check if email domain is valid (if email is being changed)
            $targetEmail = $validated['email'] ?? null;
            if ($targetEmail && $targetEmail !== ($user->email ?? null)) {
                if (!$this->validateEmailDomain($targetEmail)) {
                    $this->addError('email', 'This email domain is not valid or cannot receive emails.');
                    return;
                }
            }

            // Build payload of intended updates
            $payload = ['updates' => []];
            $targetEmail = $validated['email'] ?? null;
            if ($validated['name'] !== $user->name) {
                $payload['updates']['name'] = $validated['name'];
            }
            if ($validated['username'] !== $user->username) {
                $payload['updates']['username'] = $validated['username'];
            }
            if ($targetEmail !== ($user->email ?? null)) {
                $payload['updates']['email'] = $targetEmail;
            }
            if (!empty($validated['new_password'])) {
                if (empty($validated['current_password']) || ! Hash::check($validated['current_password'], $user->password)) {
                    $this->addError('current_password', 'Current password is incorrect.');
                    return;
                }
                $payload['updates']['password'] = Hash::make($validated['new_password']);
            }

            // Nothing to change → done
            if (empty($payload['updates'])) {
                $this->dispatch('profile-updated', name: $user->name);
                return;
            }

            // For any changes, send OTP immediately to CURRENT email (like the working email logic)
            if (!empty($payload['updates'])) {
                $otp = (string) random_int(100000, 999999);
                $expiresAt = now()->addMinutes(10);

                ProfileChangeRequest::where('user_id', $user->id)->whereNull('consumed_at')->delete();

                $request = ProfileChangeRequest::create([
                    'user_id' => $user->id,
                    'new_email' => $targetEmail,
                    'payload' => $payload,
                    'otp_code_hash' => Hash::make($otp),
                    'expires_at' => $expiresAt,
                ]);

                try {
                    if (empty($user->email)) {
                        throw new \RuntimeException('Current email is empty; cannot verify.');
                    }
                    Mail::to($user->email)->send(new AdminProfileChangeOtp($otp, $user->name));
                    Session::flash('status', 'email-change-otp-sent');
                    // Keep showing target new email while waiting for OTP (if email is changing)
                    if ($targetEmail) {
                        $this->pendingNewEmail = (string) $targetEmail;
                        $this->email = $this->pendingNewEmail;
                    }
                    return;
                } catch (\Throwable $e) {
                    optional($request)->delete();
                    $this->addError('email', 'Unable to send OTP to current email. Please check mail settings and try again.');
                    return;
                }
            }
        } else {
            $validated = $this->validate([
                'username' => ['required', 'string', 'max:255', Rule::unique(User::class)->ignore($user->id)],
            ]);

            \App\Models\User::query()->where('id', $user->id)->update([
                'username' => $validated['username'],
            ]);

            $this->dispatch('profile-updated', name: $user->name);
        }
    }

    public function sendOtpForAdminChanges(): void
    {
        $user = Auth::user();

        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:255', Rule::unique(User::class)->ignore($user->id)],
            'email' => ['nullable', 'string', 'lowercase', 'email', 'max:255', Rule::unique(User::class, 'email')->ignore($user->id)],
            'current_password' => ['nullable', 'string', 'min:8'],
            'new_password' => ['nullable', 'string', 'min:8', 'same:new_password_confirmation'],
            'new_password_confirmation' => ['nullable', 'string', 'min:8'],
        ]);

        $payload = ['updates' => []];
        if ($validated['name'] !== $user->name) {
            $payload['updates']['name'] = $validated['name'];
        }
        if ($validated['username'] !== $user->username) {
            $payload['updates']['username'] = $validated['username'];
        }
        $targetEmail = $validated['email'] ?? null;
        if ($targetEmail !== ($user->email ?? null)) {
            $payload['updates']['email'] = $targetEmail;
        }
        if (!empty($validated['new_password'])) {
            if (empty($validated['current_password']) || ! Hash::check($validated['current_password'], $user->password)) {
                $this->addError('current_password', 'Current password is incorrect.');
                return;
            }
            $payload['updates']['password'] = Hash::make($validated['new_password']);
        }

        if (empty($payload['updates'])) {
            $this->dispatch('profile-updated', name: $user->name);
            $this->readyForOtp = false;
            return;
        }

        $otp = (string) random_int(100000, 999999);
        $expiresAt = now()->addMinutes(10);

        ProfileChangeRequest::where('user_id', $user->id)->whereNull('consumed_at')->delete();

        $request = ProfileChangeRequest::create([
            'user_id' => $user->id,
            'new_email' => $targetEmail,
            'payload' => $payload,
            'otp_code_hash' => Hash::make($otp),
            'expires_at' => $expiresAt,
        ]);

        try {
            if (empty($user->email)) {
                throw new \RuntimeException('Current email is empty; cannot verify.');
            }
            Mail::to($user->email)->send(new AdminProfileChangeOtp($otp, $user->name));
            Session::flash('status', 'email-change-otp-sent');
            if ($targetEmail) {
                $this->pendingNewEmail = (string) $targetEmail;
                $this->email = $this->pendingNewEmail;
            }
        } catch (\Throwable $e) {
            optional($request)->delete();
            $this->addError('email', 'Unable to send OTP to current email. Please check mail settings and try again.');
        }
    }

    public function confirmEmailChange(): void
    {
        $user = Auth::user();
        if ($user->userType != 1) {
            return;
        }

        try {
            $this->validate([
                'otp' => ['required', 'digits:6'],
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Keep the OTP input visible when validation fails
            Session::flash('status', 'email-change-otp-invalid');
            throw $e;
        }

        $request = ProfileChangeRequest::where('user_id', $user->id)
            ->whereNull('consumed_at')
            ->latest()
            ->first();

        $provided = trim($this->otp ?? '');
        if (! $request || $request->expires_at->isPast() || ! Hash::check($provided, $request->otp_code_hash)) {
            $this->addError('otp', 'Invalid or expired code.');
            Session::flash('status', 'email-change-otp-invalid');
            // keep showing the target new email until success or cancel
            return;
        }

        $updates = $request->payload['updates'] ?? [];
        if (!empty($updates)) {
            \App\Models\User::query()->where('id', $user->id)->update($updates);
        }

        $request->update(['consumed_at' => now()]);

        $this->otp = '';
        $refetched = \App\Models\User::query()->find($user->id);
        $this->email = (string) (($refetched->email ?? ''));
        $this->pendingNewEmail = '';
        Session::flash('status', 'email-change-confirmed');
        $this->dispatch('profile-updated', name: $user->name);
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
    

    <form wire:submit="validateProfileChanges" class="mt-6 space-y-6">
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

        @if (session('status') === 'email-change-otp-sent' || session('status') === 'email-change-otp-invalid' || $pendingNewEmail)
            <div class="mt-4">
                <x-input-label for="otp" :value="__('Enter OTP sent to your current email')" />
                @if ($pendingNewEmail)
                    <p class="text-xs text-gray-500">Pending change to: <span class="font-semibold">{{ $pendingNewEmail }}</span></p>
                @endif
                <x-text-input wire:model="otp" id="otp" name="otp" type="text" class="mt-1 block w-full" maxlength="6" />
                <x-input-error class="mt-2" :messages="$errors->get('otp')" />

                <x-primary-button wire:click.prevent="confirmEmailChange" class="mt-3">{{ __('Confirm Changes') }}</x-primary-button>
            </div>
        @endif

        <div class="flex items-center gap-4">
            @if (!session('status') || session('status') === 'email-change-confirmed')
                <x-primary-button wire:loading.attr="disabled" wire:target="validateProfileChanges">
                    <span wire:loading.remove wire:target="validateProfileChanges">{{ __('Save') }}</span>
                    <span wire:loading wire:target="validateProfileChanges" class="flex items-center">
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
