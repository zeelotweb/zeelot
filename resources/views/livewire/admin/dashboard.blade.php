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
        <div class="rounded-2xl border border-slate-200 dark:border-white/10 bg-white dark:bg-white/[0.03] p-5 space-y-2">
            <span class="flex items-center justify-center w-9 h-9 rounded-lg bg-cyan-50 dark:bg-cyan-500/10 text-cyan-500">
                <flux:icon.inbox class="size-5" />
            </span>
            <div class="text-xs font-mono uppercase tracking-widest text-slate-400 dark:text-slate-500">Leads this month</div>
            <div class="text-3xl font-black text-slate-900 dark:text-white">{{ $leadsThisMonth }}</div>
        </div>

        <div class="rounded-2xl border border-slate-200 dark:border-white/10 bg-white dark:bg-white/[0.03] p-5 space-y-2">
            <span class="flex items-center justify-center w-9 h-9 rounded-lg bg-cyan-50 dark:bg-cyan-500/10 text-cyan-500">
                <flux:icon.heart class="size-5" />
            </span>
            <div class="text-xs font-mono uppercase tracking-widest text-slate-400 dark:text-slate-500">Pro Bono this month</div>
            <div class="text-3xl font-black text-slate-900 dark:text-white">{{ $proBonoThisMonth }} <span class="text-lg text-slate-400 dark:text-slate-500">/ 2</span></div>
        </div>

        <div class="rounded-2xl border border-slate-200 dark:border-white/10 bg-white dark:bg-white/[0.03] p-5 space-y-2">
            <span class="flex items-center justify-center w-9 h-9 rounded-lg bg-cyan-50 dark:bg-cyan-500/10 text-cyan-500">
                <flux:icon.folder class="size-5" />
            </span>
            <div class="text-xs font-mono uppercase tracking-widest text-slate-400 dark:text-slate-500">Active Projects</div>
            <div class="text-3xl font-black text-slate-900 dark:text-white">{{ $activeProjects }}</div>
        </div>

        <div class="rounded-2xl border border-cyan-200 dark:border-cyan-500/30 bg-cyan-50 dark:bg-cyan-500/10 p-5 space-y-2">
            <span class="flex items-center justify-center w-9 h-9 rounded-lg bg-cyan-500 text-white">
                <flux:icon.currency-dollar class="size-5" />
            </span>
            <div class="text-xs font-mono uppercase tracking-widest text-cyan-700 dark:text-cyan-300">Unpaid Invoiced</div>
            <div class="text-3xl font-black text-slate-900 dark:text-white">${{ number_format($unpaidInvoiced, 2) }}</div>
        </div>
    </div>

    <div class="flex gap-4">
        <flux:button :href="route('admin.leads.index')" wire:navigate icon="inbox">View Leads Inbox</flux:button>
        <flux:button :href="route('admin.projects.index')" variant="primary" wire:navigate icon="folder">View Projects</flux:button>
    </div>
</section>
