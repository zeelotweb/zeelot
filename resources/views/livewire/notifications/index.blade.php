<?php

use Livewire\Volt\Component;
use Livewire\WithPagination;

new class extends Component
{
    use WithPagination;

    public function notifications()
    {
        return auth()->user()->notifications()->latest()->paginate(20);
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
            <button
                type="button"
                wire:click="open('{{ $n->id }}')"
                class="w-full text-left flex items-start gap-3 px-5 py-4 hover:bg-slate-50 dark:hover:bg-white/[0.03] transition {{ $n->read_at ? '' : 'bg-cyan-50/60 dark:bg-cyan-500/5' }}"
            >
                @if(! $n->read_at)
                    <span class="mt-1.5 w-2 h-2 rounded-full bg-cyan-500 shrink-0"></span>
                @else
                    <span class="mt-1.5 w-2 h-2 shrink-0"></span>
                @endif
                <div class="min-w-0">
                    <div class="font-semibold text-slate-900 dark:text-white">{{ $n->data['title'] ?? 'Notification' }}</div>
                    <div class="text-sm text-slate-500 dark:text-slate-400 mt-0.5">{{ $n->data['body'] ?? '' }}</div>
                    <div class="text-xs text-slate-400 dark:text-slate-500 mt-1.5">{{ $n->created_at->diffForHumans() }}</div>
                </div>
            </button>
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
