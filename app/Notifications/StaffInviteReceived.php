<?php

namespace App\Notifications;

use App\Models\Invitation;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class StaffInviteReceived extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public Invitation $invitation)
    {
    }

    public function via($notifiable): array
    {
        return ['database', 'broadcast', 'mail'];
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

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Invitation to join the ZeelotWeb team')
            ->greeting('Hi '.$notifiable->name.',')
            ->line('You\'ve been invited to join as '.ucfirst($this->invitation->role).' by '.($this->invitation->invitedBy?->name ?? 'a team admin').'.')
            ->action('Review Invitation', route('notifications.index'))
            ->line('You can accept or decline from your notifications.');
    }
}
