<?php

namespace App\Notifications;

use App\Models\ProjectMilestone;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class MilestonePaid extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public ProjectMilestone $milestone)
    {
    }

    public function via($notifiable): array
    {
        return ['database', 'broadcast', 'mail'];
    }

    public function toArray($notifiable): array
    {
        return [
            'title' => 'Payment received',
            'body' => 'Thanks — we received your payment for "'.$this->milestone->title.'".',
            'url' => route('portal.projects.show', $this->milestone->project_id),
        ];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Payment received — '.$this->milestone->title)
            ->greeting('Hi '.$notifiable->name.',')
            ->line('We received your payment for "'.$this->milestone->title.'".')
            ->line('Amount paid: $'.number_format($this->milestone->amount, 2))
            ->action('View Project', route('portal.projects.show', $this->milestone->project_id))
            ->line('Thanks for working with ZeelotWeb.');
    }
}
