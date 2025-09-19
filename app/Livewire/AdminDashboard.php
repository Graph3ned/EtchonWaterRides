<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\prices;

class AdminDashboard extends Component
{
    public $prices = [];

    public function mount(){
        $this->prices=prices::all();
    }
    public function render()
    {
        return view('livewire.admin-dashboard');
    }
    public function deleteride(prices $load)
    {
        $load->delete();
        return redirect()->route('AdminDashboard');
    }
}
