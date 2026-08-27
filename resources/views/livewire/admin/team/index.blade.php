<?php

use App\Mail\InvitationMail;
use App\Models\Invitation;
use App\Models\User;
use App\Notifications\StaffInviteReceived;
use Illuminate\Support\Facades\Mail;
use Livewire\Volt\Component;

new class extends Component
{
    public string $inviteEmail = '';
    public string $inviteRole = 'staff';

    public function mount(): void
    {
        abort_unless(auth()->user()->isAdmin(), 403);
    }

    public function invitableRoles(): array
    {
        return auth()->user()->isSuperAdmin() ? ['staff', 'admin'] : ['staff'];
    }

    public function staffRoster()
    {
        return User::whereIn('role', ['staff', 'admin', 'super_admin'])
            ->orderByRaw("CASE role WHEN 'super_admin' THEN 0 WHEN 'admin' THEN 1 WHEN 'staff' THEN 2 ELSE 3 END")
            ->orderBy('name')
            ->get();
    }

    public function sendInvite(): void
    {
        $this->validate([
            'inviteEmail' => ['required', 'email'],
            'inviteRole' => ['required', 'in:'.implode(',', $this->invitableRoles())],
        ]);

        $invitation = Invitation::createFor($this->inviteEmail, $this->inviteRole, auth()->user());

        $existingUser = User::where('email', $this->inviteEmail)->first();

        if ($existingUser) {
            $existingUser->notify(new StaffInviteReceived($invitation));
            session()->flash('status', "{$existingUser->name} already has an account — they'll see this invite in their notifications.");
        } else {
            Mail::to($this->inviteEmail)->send(new InvitationMail($invitation));
            session()->flash('status', "Invitation sent to {$this->inviteEmail}.");
        }

        $this->reset('inviteEmail');
    }

    public function pendingInvitations()
    {
        return Invitation::whereNull('accepted_at')
            ->with('invitedBy')
            ->latest()
            ->get();
    }

    public function canManageInvite(Invitation $invitation): bool
    {
        return auth()->user()->isSuperAdmin()
            || $invitation->invited_by_id === auth()->id()
            || (auth()->user()->isAdmin() && $invitation->role === 'staff');
    }

    public function revokeInvite(int $id): void
    {
        $invitation = Invitation::findOrFail($id);

        abort_unless($this->canManageInvite($invitation), 403);

        $invitation->delete();

        session()->flash('status', "Invitation to {$invitation->email} revoked.");
    }

    public function resendInvite(int $id): void
    {
        $invitation = Invitation::findOrFail($id);

        abort_unless($this->canManageInvite($invitation), 403);

        $invitation->update(['expires_at' => now()->addDays(7)]);

        Mail::to($invitation->email)->send(new InvitationMail($invitation));

        session()->flash('status', "Invitation resent to {$invitation->email}.");
    }

    public function changeRole(int $userId, string $newRole): void
    {
        $target = User::findOrFail($userId);

        abort_unless(auth()->user()->canManageRoleOf($target), 403);
        abort_unless(in_array($newRole, ['customer', 'staff', 'admin'], true), 403);

        if (! auth()->user()->isSuperAdmin() && $newRole === 'admin') {
            abort(403);
        }

        $target->update(['role' => $newRole]);
    }
}; ?>

<section class="w-full space-y-8">
    <div>
        <flux:heading size="xl">Team</flux:heading>
        <flux:subheading>Manage staff and admin access.</flux:subheading>
    </div>

    @if (session('status'))
        <flux:callout variant="success" icon="check-circle" :heading="session('status')" />
    @endif

    <flux:card class="space-y-4">
        <flux:heading size="lg">Invite someone</flux:heading>
        <form wire:submit="sendInvite" class="flex flex-wrap items-end gap-4">
            <flux:input label="Email address" wire:model="inviteEmail" type="email" class="max-w-xs" />
            <flux:select label="Role" wire:model="inviteRole" class="max-w-xs">
                @foreach ($this->invitableRoles() as $role)
                    <flux:select.option value="{{ $role }}">{{ ucfirst($role) }}</flux:select.option>
                @endforeach
            </flux:select>
            <flux:button type="submit" variant="primary">Send Invite</flux:button>
        </form>
    </flux:card>

    @if ($this->pendingInvitations()->isNotEmpty())
        <div>
            <flux:heading size="lg" class="mb-4">Pending Invitations</flux:heading>

            <flux:table>
                <flux:table.columns>
                    <flux:table.column>Email</flux:table.column>
                    <flux:table.column>Role</flux:table.column>
                    <flux:table.column>Invited By</flux:table.column>
                    <flux:table.column>Sent</flux:table.column>
                    <flux:table.column>Status</flux:table.column>
                    <flux:table.column>Actions</flux:table.column>
                </flux:table.columns>

                <flux:table.rows>
                    @foreach ($this->pendingInvitations() as $invitation)
                        @php $expired = $invitation->expires_at && $invitation->expires_at->isPast(); @endphp
                        <flux:table.row wire:key="invite-{{ $invitation->id }}">
                            <flux:table.cell>{{ $invitation->email }}</flux:table.cell>
                            <flux:table.cell>
                                <flux:badge :color="match($invitation->role) {
                                    'admin' => 'blue',
                                    'staff' => 'zinc',
                                    default => 'zinc',
                                }">{{ ucfirst($invitation->role) }}</flux:badge>
                            </flux:table.cell>
                            <flux:table.cell>{{ $invitation->invitedBy?->name ?? '—' }}</flux:table.cell>
                            <flux:table.cell>{{ $invitation->created_at->diffForHumans() }}</flux:table.cell>
                            <flux:table.cell>
                                @if ($expired)
                                    <flux:badge color="red">Expired</flux:badge>
                                @else
                                    <flux:badge color="amber">Pending</flux:badge>
                                @endif
                            </flux:table.cell>
                            <flux:table.cell>
                                @if ($this->canManageInvite($invitation))
                                    <div class="flex gap-2">
                                        <flux:button size="sm" wire:click="resendInvite({{ $invitation->id }})">Resend</flux:button>
                                        <flux:button size="sm" variant="danger" wire:click="revokeInvite({{ $invitation->id }})">Revoke</flux:button>
                                    </div>
                                @else
                                    <flux:text class="text-zinc-400">—</flux:text>
                                @endif
                            </flux:table.cell>
                        </flux:table.row>
                    @endforeach
                </flux:table.rows>
            </flux:table>
        </div>
    @endif

    <flux:table>
        <flux:table.columns>
            <flux:table.column>Name</flux:table.column>
            <flux:table.column>Email</flux:table.column>
            <flux:table.column>Role</flux:table.column>
            <flux:table.column>Actions</flux:table.column>
        </flux:table.columns>

        <flux:table.rows>
            @foreach ($this->staffRoster() as $member)
                <flux:table.row wire:key="member-{{ $member->id }}">
                    <flux:table.cell>{{ $member->name }}</flux:table.cell>
                    <flux:table.cell>{{ $member->email }}</flux:table.cell>
                    <flux:table.cell>
                        <flux:badge :color="match($member->role) {
                            'super_admin' => 'amber',
                            'admin' => 'blue',
                            'staff' => 'zinc',
                            default => 'zinc',
                        }">{{ ucfirst(str_replace('_', ' ', $member->role)) }}</flux:badge>
                    </flux:table.cell>
                    <flux:table.cell>
                        @if ($member->isSuperAdmin())
                            <flux:text class="text-zinc-400">Permanent</flux:text>
                        @elseif (auth()->user()->canManageRoleOf($member))
                            <div class="flex gap-2">
                                @if ($member->role === 'staff' && auth()->user()->isSuperAdmin())
                                    <flux:button size="sm" wire:click="changeRole({{ $member->id }}, 'admin')">Promote to Admin</flux:button>
                                @elseif ($member->role === 'admin')
                                    <flux:button size="sm" wire:click="changeRole({{ $member->id }}, 'staff')">Demote to Staff</flux:button>
                                @endif

                                @if ($member->role === 'staff')
                                    <flux:button size="sm" variant="danger" wire:click="changeRole({{ $member->id }}, 'customer')">Remove Access</flux:button>
                                @endif
                            </div>
                        @else
                            <flux:text class="text-zinc-400">—</flux:text>
                        @endif
                    </flux:table.cell>
                </flux:table.row>
            @endforeach
        </flux:table.rows>
    </flux:table>
</section>
