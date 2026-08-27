<?php

namespace App\Livewire;

use App\Models\Invitation;
use Livewire\Attributes\On;
use Livewire\Component;

class NotificationBell extends Component
{
    public function open(string $id)
    {
        $notification = auth()->user()->notifications()->where('id', $id)->first();

        if (! $notification) {
            return;
        }

        $notification->markAsRead();

        $this->dispatch('notifications-updated');

        return $this->redirect($notification->data['url'] ?? route('dashboard'));
    }

    public function markAllRead(): void
    {
        auth()->user()->unreadNotifications->markAsRead();

        $this->dispatch('notifications-updated');
    }

    public function acceptInvite(string $notificationId)
    {
        $notification = auth()->user()->notifications()->where('id', $notificationId)->first();
        $invitation = $notification ? Invitation::find($notification->data['invitation_id'] ?? null) : null;

        if ($invitation && is_null($invitation->accepted_at)) {
            $invitation->acceptFor(auth()->user());
        }

        $notification?->markAsRead();

        return $this->redirect(route('admin.dashboard'));
    }

    public function declineInvite(string $notificationId): void
    {
        $notification = auth()->user()->notifications()->where('id', $notificationId)->first();
        $invitation = $notification ? Invitation::find($notification->data['invitation_id'] ?? null) : null;

        $invitation?->delete();

        $notification?->markAsRead();

        $this->dispatch('notifications-updated');
    }

    #[On('notifications-updated')]
    public function refresh(): void
    {
        //
    }

    public function render()
    {
        return view('livewire.notification-bell', [
            'notifications' => auth()->user()->notifications()->latest()->limit(8)->get(),
            'unreadCount' => auth()->user()->unreadNotifications()->count(),
        ]);
    }
}
