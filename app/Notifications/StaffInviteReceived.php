<?php

namespace App\Notifications;

use App\Models\Invitation;
use Illuminate\Notifications\Notification;

class StaffInviteReceived extends Notification
{
    public function __construct(public Invitation $invitation)
    {
    }

    public function via($notifiable): array
    {
        return ['database', 'broadcast'];
    }

    public function toArray($notifiable): array
    {
        return [
            'kind' => 'staff_invite',
            'invitation_id' => $this->invitation->id,
            'title' => 'Invitation to join the team',
            'body' => 'You\'ve been invited to join as '.ucfirst($this->invitation->role).' by '.($this->invitation->invitedBy?->name ?? 'a team admin').'.',
        ];
    }
}
