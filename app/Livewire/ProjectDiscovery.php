<?php

namespace App\Livewire;

use App\Mail\NewLeadAlert;
use App\Models\Lead;
use Illuminate\Support\Facades\Mail;
use Livewire\Attributes\On;
use Livewire\Component;

class ProjectDiscovery extends Component
{
    public $name;
    public $email;
    public $company;
    public $budget = '5000';
    public $message;
    public $success = false;
    public bool $isProBono = false;

    protected $rules = [
        'name' => 'required|min:3',
        'email' => 'required|email',
        'message' => 'required|min:10',
    ];

    public function mount(): void
    {
        $this->isProBono = request()->query('intent') === 'probono';

        $this->prefillFromAuthUser();
    }

    protected function prefillFromAuthUser(): void
    {
        if ($this->isProBono) {
            $this->budget = '0';
        }

        if (auth()->check()) {
            $this->name = auth()->user()->name;
            $this->email = auth()->user()->email;
        }
    }

    public function submit()
    {
        $this->validate();

        $lead = Lead::create([
            'name' => $this->name,
            'email' => $this->email,
            'company' => $this->company,
            'budget' => $this->budget,
            'message' => $this->message,
            'is_pro_bono' => $this->isProBono,
        ]);

        Mail::to(config('mail.admin_address'))->send(new NewLeadAlert($lead));

        $this->success = true;
        $this->reset(['name', 'email', 'company', 'message', 'budget']);
    }

    #[On('resetDiscoveryForm')]
    public function resetForm(): void
    {
        $this->success = false;
        $this->reset(['name', 'email', 'company', 'message', 'budget']);
        $this->prefillFromAuthUser();
    }

    public function render()
    {
        return view('livewire.project-discovery');
    }
}
