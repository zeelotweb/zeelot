<?php

use App\Models\Project;
use App\Models\ProjectMessage;
use App\Models\ProjectMilestone;
use App\Notifications\NewProjectMessage;
use Livewire\Volt\Component;

new class extends Component
{
    public Project $project;

    public string $newMilestoneTitle = '';
    public string $newMilestoneDescription = '';
    public string $newMilestoneAmount = '';

    public string $newMessageBody = '';

    public function mount(Project $project): void
    {
        $this->ensureStaff();

        $this->project = $project;
    }

    /**
     * Livewire only calls mount() on the initial page load — every action
     * call afterward hydrates straight from the signed snapshot, skipping
     * it entirely. Each mutating method below re-checks for that reason.
     */
    protected function ensureStaff(): void
    {
        abort_unless(auth()->user()?->isStaff(), 403);
    }

    public function milestones()
    {
        return $this->project->milestones()->get();
    }

    public function projectMessages()
    {
        return $this->project->messages()->with('user')->get();
    }

    public function addMilestone(): void
    {
        $this->ensureStaff();

        $this->validate([
            'newMilestoneTitle' => 'required|min:2',
            'newMilestoneAmount' => 'required|numeric|min:0',
        ]);

        ProjectMilestone::create([
            'project_id' => $this->project->id,
            'title' => $this->newMilestoneTitle,
            'description' => $this->newMilestoneDescription ?: null,
            'amount' => $this->newMilestoneAmount,
            'sort_order' => $this->project->milestones()->count(),
        ]);

        $this->reset(['newMilestoneTitle', 'newMilestoneDescription', 'newMilestoneAmount']);
        $this->modal('add-milestone')->close();
    }

    public function markInvoiced(int $milestoneId): void
    {
        $this->ensureStaff();

        $milestone = ProjectMilestone::findOrFail($milestoneId);

        if ($milestone->project_id === $this->project->id) {
            $milestone->markInvoiced();
        }
    }

    public function markPaidManually(int $milestoneId): void
    {
        $this->ensureStaff();

        $milestone = ProjectMilestone::findOrFail($milestoneId);

        if ($milestone->project_id === $this->project->id) {
            $milestone->markPaid();
        }
    }

    public function sendMessage(): void
    {
        $this->ensureStaff();

        $this->validate(['newMessageBody' => 'required|min:1']);

        $message = ProjectMessage::create([
            'project_id' => $this->project->id,
            'user_id' => auth()->id(),
            'body' => $this->newMessageBody,
        ]);

        $this->project->user->notify(new NewProjectMessage($message));

        $this->reset('newMessageBody');
    }
}; ?>

<section class="w-full space-y-8">
    <div>
        <flux:heading size="xl">{{ $project->name }}</flux:heading>
        <flux:subheading>
            {{ $project->user->name }} &middot; {{ $project->user->email }}
            @if($project->is_pro_bono)
                &middot; <flux:badge color="cyan">Pro Bono</flux:badge>
            @endif
        </flux:subheading>
    </div>

    <div>
        <div class="flex items-center justify-between mb-4">
            <flux:heading size="lg">Milestones</flux:heading>
            <flux:modal.trigger name="add-milestone">
                <flux:button size="sm">Add Milestone</flux:button>
            </flux:modal.trigger>
        </div>

        <flux:table>
            <flux:table.columns>
                <flux:table.column>Title</flux:table.column>
                <flux:table.column>Amount</flux:table.column>
                <flux:table.column>Status</flux:table.column>
                <flux:table.column>Actions</flux:table.column>
            </flux:table.columns>

            <flux:table.rows>
                @forelse($this->milestones() as $milestone)
                    <flux:table.row wire:key="milestone-{{ $milestone->id }}">
                        <flux:table.cell>
                            <div class="font-medium">{{ $milestone->title }}</div>
                            @if($milestone->description)
                                <div class="text-sm text-zinc-500">{{ $milestone->description }}</div>
                            @endif
                        </flux:table.cell>
                        <flux:table.cell>${{ number_format($milestone->amount, 2) }}</flux:table.cell>
                        <flux:table.cell>
                            <flux:badge :color="match($milestone->status) {
                                'pending' => 'zinc',
                                'invoiced' => 'amber',
                                'paid' => 'green',
                                'waived' => 'blue',
                                default => 'zinc',
                            }">{{ ucfirst($milestone->status) }}</flux:badge>
                        </flux:table.cell>
                        <flux:table.cell>
                            @if($milestone->status === 'pending')
                                <flux:button size="sm" wire:click="markInvoiced({{ $milestone->id }})">Mark Invoiced</flux:button>
                            @elseif($milestone->status === 'invoiced')
                                <flux:button size="sm" wire:click="markPaidManually({{ $milestone->id }})">Mark Paid Manually</flux:button>
                            @endif
                        </flux:table.cell>
                    </flux:table.row>
                @empty
                    <flux:table.row>
                        <flux:table.cell colspan="4">
                            <flux:text class="text-center py-8">No milestones yet.</flux:text>
                        </flux:table.cell>
                    </flux:table.row>
                @endforelse
            </flux:table.rows>
        </flux:table>
    </div>

    <flux:modal name="add-milestone" class="md:w-96">
        <form wire:submit="addMilestone" class="space-y-6">
            <flux:heading size="lg">Add Milestone</flux:heading>

            <flux:input label="Title" wire:model="newMilestoneTitle" />
            <flux:textarea label="Description (optional)" wire:model="newMilestoneDescription" />
            <flux:input label="Amount (USD)" type="number" step="0.01" wire:model="newMilestoneAmount" />

            <flux:button type="submit" variant="primary" class="w-full">Add Milestone</flux:button>
        </form>
    </flux:modal>

    <div>
        <flux:heading size="lg" class="mb-4">Messages</flux:heading>

        <div class="space-y-4 mb-4 max-h-96 overflow-y-auto">
            @forelse($this->projectMessages() as $message)
                <div class="p-3 rounded-lg {{ $message->user->isAdmin() ? 'bg-zinc-100 dark:bg-zinc-800 ms-8' : 'bg-cyan-50 dark:bg-cyan-500/10 me-8' }}">
                    <div class="text-xs text-zinc-500 mb-1">{{ $message->user->name }} &middot; {{ $message->created_at->diffForHumans() }}</div>
                    <div>{{ $message->body }}</div>
                </div>
            @empty
                <flux:text>No messages yet.</flux:text>
            @endforelse
        </div>

        <form wire:submit="sendMessage" class="flex gap-2">
            <flux:textarea wire:model="newMessageBody" placeholder="Reply to customer..." class="flex-1" rows="2" />
            <flux:button type="submit" variant="primary">Send</flux:button>
        </form>
    </div>
</section>
