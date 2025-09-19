<?php

namespace App\Http\Livewire\Admin;

use App\Models\User;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class UpdateStaffProfile extends Component
{
    public $userId;
    public $name = '';
    public $email = '';
    public $username = '';
    public $userType;

    private $originalAdminId;

    /**
     * Mount the component with the user's data.
     */
    public function mount($id)
    {
        // Ensure the admin is logged in.
        if (!Auth::check()) {
            abort(403, 'You must be logged in to access this page.');
        }

        // Store the current admin ID.
        $this->originalAdminId = Auth::id();

        // Find the user and log in as that user.
        $user = User::where('id', $id)->first();

        if (!$user) {
            abort(404, 'User not found');
        }

        // Log in as the user being edited (temporary login).
        Auth::login($user);

        // Log the temporarily logged-in user to check if it's successful
        $tempLoggedInUser = Auth::user();  // This will return the currently logged-in user
        
        // Log the login attempt
        Log::info('Attempting to log in as: ' . $user->name . ' (ID: ' . $user->id . ')');
        
        // Check if the currently logged-in user matches the one we just logged in as
        if ($tempLoggedInUser && $tempLoggedInUser->id === $user->id) {
            // Temporary login successful
            Log::info('Temporarily logged in as user: ' . $tempLoggedInUser->name);
        } else {
            Log::error('Failed to log in as user: ' . $user->name);
        }

        // Populate form fields with the user's data.
        $this->userId = $user->id;
        $this->name = $user->name;
        $this->email = $user->email;
        $this->username = $user->username;
        $this->userType = $user->userType;
    }

    /**
     * Update the user's information and revert to the admin login.
     */
    public function updateUser()
    {
        // Validate the data
        $validatedData = $this->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $this->userId,
            'username' => 'required|string|max:255|unique:users,username,' . $this->userId,
            'userType' => 'required|integer',
        ]);

        // Update the user's information.
        $user = User::find($this->userId);
        if (!$user) {
            abort(404, 'User not found');
        }

        // Update the user data.
        $user->update($validatedData);

        // Flash success message.
        session()->flash('success', 'User updated successfully.');

        // Logout of the current user and log in back to the admin.
        Auth::logout();  // Log out the temporarily logged-in user.
        Auth::loginUsingId($this->originalAdminId);  // Log in the admin back.

        // Redirect back to the users listing.
        return redirect()->route('admin.users');
    }

    public function render()
    {
        return view('livewire.admin.update-staff-profile');
    }
}
