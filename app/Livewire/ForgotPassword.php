<?php

namespace App\Livewire;

use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;

class ForgotPassword extends Component
{
    public string $username = '';
    public string $email = '';
    public bool $emailSent = false;
    public string $resetToken = '';

    protected $rules = [
        'username' => 'required|string|exists:users,username',
        'email' => 'required|email|exists:users,email',
    ];

    protected $messages = [
        'username.required' => 'Username is required.',
        'username.exists' => 'Username not found.',
        'email.required' => 'Email is required.',
        'email.email' => 'Please enter a valid email address.',
        'email.exists' => 'Email not found.',
    ];

    public function sendResetLink()
    {
        $this->validate();

        // Find the user by username and email
        $user = User::where('username', $this->username)
                   ->where('email', $this->email)
                   ->first();

        if (!$user) {
            $this->addError('email', 'Username and email combination not found.');
            return;
        }

        // Generate a reset token
        $this->resetToken = Str::random(64);
        
        // Store the reset token in the user's remember_token field temporarily
        $user->update(['remember_token' => Hash::make($this->resetToken)]);

        // Send the reset email
        try {
            Mail::send('emails.password-reset', [
                'user' => $user,
                'resetToken' => $this->resetToken,
                'resetUrl' => route('password.reset', ['token' => $this->resetToken, 'username' => $user->username])
            ], function ($message) use ($user) {
                $message->to($user->email)
                       ->subject('Password Reset Request - Etchon Water Rides');
            });

            $this->emailSent = true;
            session()->flash('status', 'Password reset link sent to your email!');
        } catch (\Exception $e) {
            $this->addError('email', 'Unable to send reset email. Please try again later.');
        }
    }

    public function render()
    {
        return view('livewire.forgot-password');
    }
}