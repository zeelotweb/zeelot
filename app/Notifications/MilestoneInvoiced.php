<?php

namespace App\Notifications;

use App\Models\ProjectMilestone;
use Illuminate\Notifications\Notification;

class MilestoneInvoiced extends Notification
{
    public function __construct(public ProjectMilestone $milestone)
    {
    }

    public function via($notifiable): array
    {
        return ['database', 'broadcast'];
    }

    public function toArray($notifiable): array
    {
        return [
            'title' => 'Invoice ready',
            'body' => '"'.$this->milestone->title.'" ($'.number_format($this->milestone->amount, 2).') is ready for payment.',
            'url' => route('portal.projects.show', $this->milestone->project_id),
        ];
    }
}
