<?php

use App\Models\Lead;
use App\Models\Project;
use App\Models\ProjectMilestone;
use Livewire\Volt\Component;

new class extends Component
{
    public int $leadsThisMonth = 0;
    public int $proBonoThisMonth = 0;
    public int $activeProjects = 0;
    public float $unpaidInvoiced = 0;

    public function mount(): void
    {
        $this->leadsThisMonth = Lead::whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth()])->count();
        $this->proBonoThisMonth = Lead::proBonoCountThisMonth();
        $this->activeProjects = Project::where('status', 'active')->count();
        $this->unpaidInvoiced = (float) ProjectMilestone::where('status', 'invoiced')->sum('amount');
    }
}; ?>

<section class="w-full">
    <div class="mb-8">
        <flux:heading size="xl">Admin Dashboard</flux:heading>
        <flux:subheading>Overview of leads, pro bono slots, and active projects.</flux:subheading>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
        <flux:card class="space-y-1">
            <flux:text>Leads this month</flux:text>
            <flux:heading size="lg">{{ $leadsThisMonth }}</flux:heading>
        </flux:card>
        <flux:card class="space-y-1">
            <flux:text>Pro bono applications this month</flux:text>
            <flux:heading size="lg">{{ $proBonoThisMonth }} / 2</flux:heading>
        </flux:card>
        <flux:card class="space-y-1">
            <flux:text>Active projects</flux:text>
            <flux:heading size="lg">{{ $activeProjects }}</flux:heading>
        </flux:card>
        <flux:card class="space-y-1">
            <flux:text>Unpaid invoiced</flux:text>
            <flux:heading size="lg">${{ number_format($unpaidInvoiced, 2) }}</flux:heading>
        </flux:card>
    </div>

    <div class="flex gap-4">
        <flux:button :href="route('admin.leads.index')" wire:navigate>View Leads Inbox</flux:button>
        <flux:button :href="route('admin.projects.index')" variant="filled" wire:navigate>View Projects</flux:button>
    </div>
</section>
