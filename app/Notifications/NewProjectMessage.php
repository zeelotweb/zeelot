<?php

namespace App\Notifications;

use App\Models\ProjectMessage;
use Illuminate\Notifications\Notification;

class NewProjectMessage extends Notification
{
    public function __construct(public ProjectMessage $message)
    {
    }

    public function via($notifiable): array
    {
        return ['database', 'broadcast'];
    }

    public function toArray($notifiable): array
    {
        return [
            'title' => 'New message',
            'body' => $this->message->user->name.' sent you a message on '.$this->message->project->name.'.',
            'url' => route('portal.projects.show', $this->message->project_id),
        ];
    }
}
