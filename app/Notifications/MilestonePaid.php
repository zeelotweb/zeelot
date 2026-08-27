<?php

namespace App\Notifications;

use App\Models\ProjectMilestone;
use Illuminate\Notifications\Notification;

class MilestonePaid extends Notification
{
    public function __construct(public ProjectMilestone $milestone)
    {
    }

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toArray($notifiable): array
    {
        return [
            'title' => 'Payment received',
            'body' => 'Thanks — we received your payment for "'.$this->milestone->title.'".',
            'url' => route('portal.projects.show', $this->milestone->project_id),
        ];
    }
}
