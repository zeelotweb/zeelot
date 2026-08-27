<?php

namespace App\Notifications;

use App\Models\Lead;
use Illuminate\Notifications\Notification;

class LeadDeclined extends Notification
{
    public function __construct(public Lead $lead)
    {
    }

    public function via($notifiable): array
    {
        return ['database', 'broadcast'];
    }

    public function toArray($notifiable): array
    {
        return [
            'title' => 'Update on your project inquiry',
            'body' => $this->lead->decline_reason
                ?: 'We reviewed your inquiry and it\'s not a fit we can take on right now.',
        ];
    }
}
