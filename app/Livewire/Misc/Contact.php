<?php

namespace App\Livewire\Misc;

use Livewire\Component;

class Contact extends Component
{

    public $name, $email, $subject, $message;
    public $successMessage = '';

    protected $rules = [
        'name' => 'required|min:2',
        'email' => 'required|email',
        'subject' => 'required|min:3',
        'message' => 'required|min:10',
    ];



    public function send()
    {
       // dd($this->name);
        $this->validate();

        Contact::create([
            'name' => $this->name,
            'email' => $this->email,
            'subject' => $this->subject,
            'message' => $this->message,
        ]);

        $this->reset(['name','email','subject','message']);
        $this->successMessage = "Thanks for reaching out! We'll get back to you soon.";
    }



    public function render()
    {
        return view('livewire.misc.contact');
    }
}
