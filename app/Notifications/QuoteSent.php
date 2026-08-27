<?php

namespace App\Notifications;

use App\Models\Quote;
use Illuminate\Notifications\Notification;

class QuoteSent extends Notification
{
    public function __construct(public Quote $quote)
    {
    }

    public function via($notifiable): array
    {
        return ['database', 'broadcast'];
    }

    public function toArray($notifiable): array
    {
        return [
            'title' => 'New quote ready',
            'body' => 'Your quote for $'.number_format($this->quote->total(), 2).' is ready to review.',
            'url' => url('/quotes/'.$this->quote->token),
        ];
    }
}
