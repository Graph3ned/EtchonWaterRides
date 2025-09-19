<?php

namespace App\Livewire;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;

class ResetPassword extends Component
{
    public string $token = '';
    public string $username = '';
    public string $password = '';
    public string $password_confirmation = '';
    public bool $passwordReset = false;

    public function mount($token, $username)
    {
        $this->token = $token;
        $this->username = $username;
        
        // Verify the token is valid
        $user = User::where('username', $username)->first();
        if (!$user || !Hash::check($token, $user->remember_token)) {
            session()->flash('error', 'Invalid or expired reset token.');
            return redirect()->route('login');
        }
    }

    protected $rules = [
        'password' => 'required|string|min:8|confirmed',
        'password_confirmation' => 'required|string|min:8',
    ];

    protected $messages = [
        'password.required' => 'Password is required.',
        'password.min' => 'Password must be at least 8 characters.',
        'password.confirmed' => 'Password confirmation does not match.',
        'password_confirmation.required' => 'Password confirmation is required.',
    ];

    public function resetPassword()
    {
        $this->validate();

        $user = User::where('username', $this->username)->first();
        
        if (!$user || !Hash::check($this->token, $user->remember_token)) {
            $this->addError('password', 'Invalid or expired reset token.');
            return;
        }

        // Update the password and clear the reset token
        $user->update([
            'password' => Hash::make($this->password),
            'remember_token' => null,
        ]);

        $this->passwordReset = true;
        session()->flash('status', 'Password reset successfully! You can now login with your new password.');
    }

    public function render()
    {
        return view('livewire.reset-password');
    }
}