<?php

namespace App\Notifications;

use App\Models\ProjectMessage;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewProjectMessage extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public ProjectMessage $message)
    {
    }

    public function via($notifiable): array
    {
        return ['database', 'broadcast', 'mail'];
    }

    public function toArray($notifiable): array
    {
        return [
            'title' => 'New message',
            'body' => $this->message->user->name.' sent you a message on '.$this->message->project->name.'.',
            'url' => route('portal.projects.show', $this->message->project_id),
        ];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('New message on '.$this->message->project->name)
            ->greeting('Hi '.$notifiable->name.',')
            ->line($this->message->user->name.' sent you a message:')
            ->line('"'.$this->message->body.'"')
            ->action('Reply', route('portal.projects.show', $this->message->project_id))
            ->line('Thanks for working with ZeelotWeb.');
    }
}
