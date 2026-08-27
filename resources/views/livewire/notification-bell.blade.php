<div>
    <flux:dropdown position="bottom" align="end">
        <span class="relative inline-flex">
            <flux:button icon="bell" variant="ghost" size="sm" data-test="notification-bell" />
            @if($unreadCount > 0)
                <span class="absolute -top-1 -right-1 flex items-center justify-center min-w-[18px] h-[18px] px-1 rounded-full bg-cyan-500 text-white text-[10px] font-bold leading-none">
                    {{ $unreadCount > 9 ? '9+' : $unreadCount }}
                </span>
            @endif
        </span>

        <flux:menu class="w-80 !p-0">
            <div class="flex items-center justify-between px-4 py-3 border-b border-zinc-200 dark:border-zinc-700">
                <span class="text-sm font-semibold">Notifications</span>
                @if($unreadCount > 0)
                    <button type="button" wire:click="markAllRead" class="text-xs text-cyan-600 dark:text-cyan-400 hover:underline">Mark all read</button>
                @endif
            </div>

            <div class="max-h-96 overflow-y-auto">
                @forelse($notifications as $n)
                    @php $isInvite = ($n->data['kind'] ?? null) === 'staff_invite'; @endphp
                    @php $invite = $isInvite ? \App\Models\Invitation::find($n->data['invitation_id'] ?? null) : null; @endphp

                    <div class="flex items-start gap-2.5 px-4 py-3 border-b border-zinc-100 dark:border-zinc-800 last:border-0 {{ $n->read_at ? '' : 'bg-cyan-50/60 dark:bg-cyan-500/5' }}">
                        @if(! $n->read_at)
                            <span class="mt-1.5 w-1.5 h-1.5 rounded-full bg-cyan-500 shrink-0"></span>
                        @else
                            <span class="mt-1.5 w-1.5 h-1.5 shrink-0"></span>
                        @endif

                        @if($isInvite)
                            <div class="min-w-0 flex-1">
                                <div class="text-sm font-medium text-zinc-900 dark:text-white">{{ $n->data['title'] ?? 'Notification' }}</div>
                                <div class="text-xs text-zinc-500 dark:text-zinc-400 mt-0.5">{{ $n->data['body'] ?? '' }}</div>
                                <div class="text-xs text-zinc-400 dark:text-zinc-500 mt-1 mb-2">{{ $n->created_at->diffForHumans() }}</div>

                                @if($invite && is_null($invite->accepted_at))
                                    <div class="flex gap-2">
                                        <button type="button" wire:click="acceptInvite('{{ $n->id }}')" class="text-xs font-semibold px-2.5 py-1 rounded-md bg-cyan-500 text-white hover:bg-cyan-400 transition">Accept</button>
                                        <button type="button" wire:click="declineInvite('{{ $n->id }}')" class="text-xs font-semibold px-2.5 py-1 rounded-md bg-zinc-100 dark:bg-zinc-800 hover:bg-zinc-200 dark:hover:bg-zinc-700 transition">Decline</button>
                                    </div>
                                @elseif($invite && $invite->accepted_at)
                                    <span class="text-xs text-green-600 dark:text-green-400 font-medium">Accepted</span>
                                @else
                                    <span class="text-xs text-zinc-400">No longer available</span>
                                @endif
                            </div>
                        @else
                            <button type="button" wire:click="open('{{ $n->id }}')" class="min-w-0 flex-1 text-left">
                                <div class="text-sm font-medium text-zinc-900 dark:text-white truncate">{{ $n->data['title'] ?? 'Notification' }}</div>
                                <div class="text-xs text-zinc-500 dark:text-zinc-400 mt-0.5 line-clamp-2">{{ $n->data['body'] ?? '' }}</div>
                                <div class="text-xs text-zinc-400 dark:text-zinc-500 mt-1">{{ $n->created_at->diffForHumans() }}</div>
                            </button>
                        @endif
                    </div>
                @empty
                    <div class="px-4 py-8 text-center text-sm text-zinc-500">You're all caught up.</div>
                @endforelse
            </div>

            <a href="{{ route('notifications.index') }}" wire:navigate class="block text-center text-sm text-cyan-600 dark:text-cyan-400 hover:underline px-4 py-3 border-t border-zinc-200 dark:border-zinc-700">
                View all notifications
            </a>
        </flux:menu>
    </flux:dropdown>
</div>
