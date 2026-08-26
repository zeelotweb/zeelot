<?php

namespace App\Mail;

use App\Models\Lead;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class NewLeadAlert extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(public Lead $lead)
    {
    }

    public function build()
    {
        $priority = $this->lead->budget >= 5000 ? '🚨 HIGH PRIORITY' : 'New Lead';

        if ($this->lead->is_pro_bono) {
            $priority = '🤝 Pro Bono Application';
        }

        return $this->subject($priority.': '.$this->lead->name)
            ->view('emails.new-lead');
    }
}
