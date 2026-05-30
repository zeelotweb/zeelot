<?php

namespace App\Livewire\Misc;

use Livewire\Component;
use Illuminate\Support\Facades\Mail;

class ContactA extends Component
{
    public $name;
    public $email;
    public $package;
    public $message;

    protected $rules = [
        'name' => 'required|string|min:2',
        'email' => 'required|email',
        'package' => 'required|string',
        'message' => 'nullable|string|max:1000',
    ];

    public function submit()
    {
        $this->validate();

        // Send email to yourself
        Mail::raw(
            "Name: {$this->name}\nEmail: {$this->email}\nPackage: {$this->package}\nMessage: {$this->message}", 
            function($msg) {
                $msg->to('you@yourdomain.com')
                    ->subject('New ZeelotWeb Inquiry');
            }
        );

        session()->flash('success', 'Thanks! We received your inquiry.');

        $this->reset(['name','email','package','message']);
    }

    public function render()
    {
       return view('livewire.misc.contact-a');
    }
}













