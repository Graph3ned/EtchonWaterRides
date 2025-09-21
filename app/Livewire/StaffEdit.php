<?php

namespace App\Livewire;

use App\Models\User;
use Livewire\Component;
use Illuminate\Support\Facades\Hash;

class StaffEdit extends Component
{
    public $staffId;
    public $name;
    public $email;
    public $username;
    public $password;
    public $password_confirmation;

    public function mount($id)
    {
        $staff = User::findOrFail($id);
        $this->staffId = $staff->id;
        $this->name = $staff->name;
        $this->email = $staff->email;
        $this->username = $staff->username;
    }

    public function updateStaff()
    {
        $validationRules = [
            'name' => 'required|string|max:255|unique:users,name,' . $this->staffId,
            'email' => 'required|email|unique:users,email,' . $this->staffId,
            'username' => 'required|string|max:255|unique:users,username,' . $this->staffId,
        ];

        // Only validate password if it's being changed
        if ($this->password) {
            $validationRules['password'] = 'required|min:8|confirmed';
        }

        $this->validate($validationRules);

        $staff = User::find($this->staffId);
        $updateData = [
            'name' => $this->name,
            'email' => $this->email,
            'username' => $this->username,
        ];

        // Only update password if a new one is provided
        if ($this->password) {
            $updateData['password'] = Hash::make($this->password);
        }

        $staff->update($updateData);

        session()->flash('message', 'Staff updated successfully.');
        return $this->redirect('/admin/staffs', navigate: true);
    }

    public function render()
    {
        return view('livewire.staff-edit');
    }
} 