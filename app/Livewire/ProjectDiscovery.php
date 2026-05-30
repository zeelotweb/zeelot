<?php

namespace App\Livewire;

use Livewire\Component;

class ProjectDiscovery extends Component
{
    public $name;
    public $email;
    public $company;
    public $budget = '5000';
    public $message;
    public $success = false;

    protected $rules = [
        'name' => 'required|min:3',
        'email' => 'required|email',
        'message' => 'required|min:10',
    ];




public function submit()
{
    // 1. Validate the incoming data based on the $rules
    $validatedData = $this->validate();

    // 2. Persist the data to the 'leads' table
    \App\Models\Lead::create([
        'name' => $this->name,
        'email' => $this->email,
        'company' => $this->company,
        'budget' => $this->budget,
        'message' => $this->message,
    ]);

    // 3. Trigger success state and reset form
    $this->success = true;
    $this->reset(['name', 'email', 'company', 'message', 'budget']);
}



    public function render()
    {
        return view('livewire.project-discovery');
    }
}
