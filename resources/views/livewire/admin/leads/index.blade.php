<?php

use App\Models\Lead;
use App\Models\Project;
use App\Models\User;
use Livewire\Attributes\Computed;
use Livewire\Volt\Component;

new class extends Component
{
    public string $statusFilter = 'all';
    public bool $proBonoOnly = false;
    public string $conversionError = '';
    public string $conversionSuccess = '';

    #[Computed]
    public function leads()
    {
        return Lead::query()
            ->when($this->statusFilter !== 'all', fn ($q) => $q->where('status', $this->statusFilter))
            ->when($this->proBonoOnly, fn ($q) => $q->proBono())
            ->latest()
            ->get();
    }

    public function updateStatus(int $leadId, string $status): void
    {
        Lead::whereKey($leadId)->update(['status' => $status]);
        unset($this->leads);
    }

    public function convertToProject(int $leadId)
    {
        $this->conversionError = '';
        $this->conversionSuccess = '';

        $lead = Lead::findOrFail($leadId);

        $user = User::where('email', $lead->email)->first();

        if (! $user) {
            $this->conversionError = "No account exists yet for {$lead->email}. Ask the customer to register at /register first, then convert again.";

            return;
        }

        $project = Project::create([
            'user_id' => $user->id,
            'lead_id' => $lead->id,
            'name' => ($lead->company ?: $lead->name).' — Project',
            'status' => 'active',
            'is_pro_bono' => $lead->is_pro_bono,
        ]);

        $lead->update([
            'status' => 'converted',
            'converted_to_project_id' => $project->id,
        ]);

        $this->redirect(route('admin.projects.show', $project), navigate: true);
    }
}; ?>

<section class="w-full">
    <div class="mb-8 flex items-center justify-between">
        <div>
            <flux:heading size="xl">Leads Inbox</flux:heading>
            <flux:subheading>Incoming inquiries and pro bono applications.</flux:subheading>
        </div>
    </div>

    @if($conversionError)
        <flux:callout variant="danger" class="mb-6" icon="exclamation-triangle" heading="Couldn't convert lead">
            {{ $conversionError }}
        </flux:callout>
    @endif

    <div class="flex flex-wrap items-center gap-4 mb-6">
        <flux:select wire:model.live="statusFilter" class="max-w-xs">
            <flux:select.option value="all">All statuses</flux:select.option>
            <flux:select.option value="new">New</flux:select.option>
            <flux:select.option value="reviewing">Reviewing</flux:select.option>
            <flux:select.option value="accepted">Accepted</flux:select.option>
            <flux:select.option value="declined">Declined</flux:select.option>
            <flux:select.option value="converted">Converted</flux:select.option>
        </flux:select>

        <flux:checkbox wire:model.live="proBonoOnly" label="Pro bono only" />
    </div>

    <flux:table>
        <flux:table.columns>
            <flux:table.column>Name</flux:table.column>
            <flux:table.column>Contact</flux:table.column>
            <flux:table.column>Budget</flux:table.column>
            <flux:table.column>Type</flux:table.column>
            <flux:table.column>Status</flux:table.column>
            <flux:table.column>Received</flux:table.column>
            <flux:table.column>Actions</flux:table.column>
        </flux:table.columns>

        <flux:table.rows>
            @forelse($this->leads as $lead)
                <flux:table.row wire:key="lead-{{ $lead->id }}">
                    <flux:table.cell>
                        <div class="font-medium">{{ $lead->name }}</div>
                        @if($lead->company)
                            <div class="text-sm text-zinc-500">{{ $lead->company }}</div>
                        @endif
                    </flux:table.cell>
                    <flux:table.cell>{{ $lead->email }}</flux:table.cell>
                    <flux:table.cell>${{ number_format($lead->budget, 0) }}</flux:table.cell>
                    <flux:table.cell>
                        @if($lead->is_pro_bono)
                            <flux:badge color="cyan">Pro Bono</flux:badge>
                        @else
                            <flux:badge color="zinc">Paid</flux:badge>
                        @endif
                    </flux:table.cell>
                    <flux:table.cell>
                        <flux:badge :color="match($lead->status) {
                            'new' => 'blue',
                            'reviewing' => 'amber',
                            'accepted' => 'green',
                            'declined' => 'red',
                            'converted' => 'purple',
                            default => 'zinc',
                        }">{{ ucfirst($lead->status) }}</flux:badge>
                    </flux:table.cell>
                    <flux:table.cell>{{ $lead->created_at->diffForHumans() }}</flux:table.cell>
                    <flux:table.cell>
                        <flux:modal.trigger name="lead-{{ $lead->id }}">
                            <flux:button size="sm">View</flux:button>
                        </flux:modal.trigger>
                    </flux:table.cell>
                </flux:table.row>

                <flux:modal name="lead-{{ $lead->id }}" class="md:w-[32rem]">
                    <div class="space-y-6">
                        <div>
                            <flux:heading size="lg">{{ $lead->name }}</flux:heading>
                            <flux:subheading>{{ $lead->email }} @if($lead->company) &middot; {{ $lead->company }} @endif</flux:subheading>
                        </div>

                        @if($lead->is_pro_bono)
                            <flux:badge color="cyan">Pro Bono Application</flux:badge>
                        @endif

                        <flux:text>{{ $lead->message }}</flux:text>

                        <div class="flex flex-wrap gap-2">
                            <flux:button size="sm" wire:click="updateStatus({{ $lead->id }}, 'reviewing')">Mark Reviewing</flux:button>
                            <flux:button size="sm" wire:click="updateStatus({{ $lead->id }}, 'accepted')" variant="primary">Mark Accepted</flux:button>
                            <flux:button size="sm" wire:click="updateStatus({{ $lead->id }}, 'declined')" variant="danger">Decline</flux:button>
                        </div>

                        @if($lead->status === 'accepted' && ! $lead->converted_to_project_id)
                            <flux:button wire:click="convertToProject({{ $lead->id }})" variant="primary" class="w-full">
                                Convert to Project
                            </flux:button>
                        @elseif($lead->converted_to_project_id)
                            <flux:button :href="route('admin.projects.show', $lead->converted_to_project_id)" wire:navigate class="w-full">
                                View Project
                            </flux:button>
                        @endif
                    </div>
                </flux:modal>
            @empty
                <flux:table.row>
                    <flux:table.cell colspan="7">
                        <flux:text class="text-center py-8">No leads yet.</flux:text>
                    </flux:table.cell>
                </flux:table.row>
            @endforelse
        </flux:table.rows>
    </flux:table>
</section>
