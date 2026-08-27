<?php

use App\Models\Invitation;
use Livewire\Attributes\On;
use Livewire\Volt\Component;
use Livewire\WithPagination;

new class extends Component
{
    use WithPagination;

    public function notifications()
    {
        return auth()->user()->notifications()->latest()->paginate(20);
    }

    #[On('notifications-updated')]
    public function refresh(): void
    {
        //
    }

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
}; ?>

<section class="w-full max-w-2xl">
    <div class="mb-8 flex items-center justify-between">
        <div>
            <flux:heading size="xl">Notifications</flux:heading>
            <flux:subheading>Updates on your quotes, invoices, and projects.</flux:subheading>
        </div>
        <flux:button wire:click="markAllRead" variant="ghost" size="sm">Mark all read</flux:button>
    </div>

    <div class="rounded-2xl border border-slate-200 dark:border-white/10 overflow-hidden divide-y divide-slate-100 dark:divide-white/5">
        @forelse($this->notifications() as $n)
            @php $isInvite = ($n->data['kind'] ?? null) === 'staff_invite'; @endphp
            @php $invite = $isInvite ? Invitation::find($n->data['invitation_id'] ?? null) : null; @endphp

            <div class="flex items-start gap-3 px-5 py-4 {{ $n->read_at ? '' : 'bg-cyan-50/60 dark:bg-cyan-500/5' }}">
                @if(! $n->read_at)
                    <span class="mt-1.5 w-2 h-2 rounded-full bg-cyan-500 shrink-0"></span>
                @else
                    <span class="mt-1.5 w-2 h-2 shrink-0"></span>
                @endif

                @if($isInvite)
                    <div class="min-w-0 flex-1">
                        <div class="font-semibold text-slate-900 dark:text-white">{{ $n->data['title'] ?? 'Notification' }}</div>
                        <div class="text-sm text-slate-500 dark:text-slate-400 mt-0.5">{{ $n->data['body'] ?? '' }}</div>
                        <div class="text-xs text-slate-400 dark:text-slate-500 mt-1.5 mb-3">{{ $n->created_at->diffForHumans() }}</div>

                        @if($invite && is_null($invite->accepted_at))
                            <div class="flex gap-2">
                                <flux:button size="sm" variant="primary" wire:click="acceptInvite('{{ $n->id }}')">Accept</flux:button>
                                <flux:button size="sm" variant="ghost" wire:click="declineInvite('{{ $n->id }}')">Decline</flux:button>
                            </div>
                        @elseif($invite && $invite->accepted_at)
                            <flux:badge color="green">Accepted</flux:badge>
                        @else
                            <flux:text class="text-zinc-400 text-sm">No longer available</flux:text>
                        @endif
                    </div>
                @else
                    <button
                        type="button"
                        wire:click="open('{{ $n->id }}')"
                        class="min-w-0 flex-1 text-left"
                    >
                        <div class="font-semibold text-slate-900 dark:text-white">{{ $n->data['title'] ?? 'Notification' }}</div>
                        <div class="text-sm text-slate-500 dark:text-slate-400 mt-0.5">{{ $n->data['body'] ?? '' }}</div>
                        <div class="text-xs text-slate-400 dark:text-slate-500 mt-1.5">{{ $n->created_at->diffForHumans() }}</div>
                    </button>
                @endif
            </div>
        @empty
            <div class="flex flex-col items-center justify-center py-16 text-center">
                <span class="flex items-center justify-center w-12 h-12 rounded-2xl bg-cyan-50 dark:bg-cyan-500/10 text-cyan-500 mb-3">
                    <flux:icon.bell class="size-6" />
                </span>
                <flux:text>Nothing yet — you'll see updates here as they happen.</flux:text>
            </div>
        @endforelse
    </div>

    <div class="mt-6">
        {{ $this->notifications()->links() }}
    </div>
</section>
