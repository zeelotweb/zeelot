<?php

namespace App\Mail;

use App\Models\Lead;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Mail\NewLeadAlert;
use Illuminate\Support\Facades\Mail;

class NewLeadAlert extends Mailable
{
    use Queueable, SerializesModels;

    public $lead;

    public function __construct(Lead $lead)
    {
        $this->lead = $lead;
    }

    public function build()
    {
        $priority = $this->lead->budget >= 5000 ? '🚨 HIGH PRIORITY' : 'New Lead';
        
        return $this->subject($priority . ': ' . $this->lead->name)
                    ->view('emails.new-lead');
    }




// Inside your submit() method, after Lead::create:
$lead = \App\Models\Lead::create($validatedData);

Mail::to('your-email@example.com')->send(new NewLeadAlert($lead));

}

