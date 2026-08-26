<?php

use App\Mail\InvitationMail;
use App\Models\Invitation;
use App\Models\User;
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

        Mail::to($this->inviteEmail)->send(new InvitationMail($invitation));

        $this->reset('inviteEmail');
        session()->flash('status', "Invitation sent to {$this->inviteEmail}.");
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
        <flux:callout variant="success" icon="check-circle" heading="{{ session('status') }}" />
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
