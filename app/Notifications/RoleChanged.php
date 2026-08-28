<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class RoleChanged extends Notification implements ShouldQueue
{
    use Queueable;

    private const RANK = ['customer' => 0, 'staff' => 1, 'admin' => 2, 'super_admin' => 3];

    public function __construct(public string $oldRole, public string $newRole, public ?string $changedBy = null)
    {
    }

    public function via($notifiable): array
    {
        return ['database', 'broadcast', 'mail'];
    }

    public function toArray($notifiable): array
    {
        $roleLabel = ucfirst(str_replace('_', ' ', $this->newRole));
        $by = $this->changedBy ? " by {$this->changedBy}" : '';

        if ($this->newRole === 'customer') {
            return [
                'title' => 'Team access removed',
                'body' => "Your staff access was removed{$by}.",
            ];
        }

        $promoted = (self::RANK[$this->newRole] ?? 0) > (self::RANK[$this->oldRole] ?? 0);

        return [
            'title' => $promoted ? 'You were promoted' : 'Your role changed',
            'body' => "You're now {$roleLabel}, ".($promoted ? 'promoted' : 'changed')."{$by}.",
        ];
    }

    public function toMail($notifiable): MailMessage
    {
        $data = $this->toArray($notifiable);

        return (new MailMessage)
            ->subject($data['title'])
            ->greeting('Hi '.$notifiable->name.',')
            ->line($data['body'])
            ->action('Go to Dashboard', route('dashboard'));
    }
}
