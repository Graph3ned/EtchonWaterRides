<?php

namespace App\Livewire;

use App\Models\User;
use Livewire\Component;

class Staffs extends Component
{
    public $staffs;
    public $showModal = false;
    public $staffToDelete;
    public $modalDetails;

    public $showRegister = false;

    public function mount()
    {
        $this->staffs = User::all();
    }

    public function confirmDelete($staffId)
    {
        $staff = User::find($staffId);
        $this->staffToDelete = $staffId;
        $this->modalDetails = "Are you sure you want to delete {$staff->name}?";
        $this->showModal = true;
    }

    public function deleteStaff()
    {
        User::find($this->staffToDelete)->delete();
        $this->staffs = User::all();
        $this->showModal = false;
        session()->flash('message', 'Staff deleted successfully.');
    }

    public function closeModal()
    {
        $this->showModal = false;
    }

    public function render()
    {
        return view('livewire.staffs');
    }
}
