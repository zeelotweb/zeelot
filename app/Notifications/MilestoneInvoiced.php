<?php

namespace App\Notifications;

use App\Models\ProjectMilestone;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class MilestoneInvoiced extends Notification implements ShouldQueue
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
            'title' => 'Invoice ready',
            'body' => '"'.$this->milestone->title.'" ($'.number_format($this->milestone->amount, 2).') is ready for payment.',
            'url' => route('portal.projects.show', $this->milestone->project_id),
        ];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Invoice ready — '.$this->milestone->title)
            ->greeting('Hi '.$notifiable->name.',')
            ->line('"'.$this->milestone->title.'" is ready for payment.')
            ->line('Amount due: $'.number_format($this->milestone->amount, 2))
            ->action('View & Pay', route('portal.projects.show', $this->milestone->project_id))
            ->line('Thanks for working with ZeelotWeb.');
    }
}
