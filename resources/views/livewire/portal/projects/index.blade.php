<?php

use Livewire\Volt\Component;

new class extends Component
{
    public function projects()
    {
        return auth()->user()->projects()->with('milestones')->latest()->get();
    }
}; ?>

<section class="w-full">
    <div class="mb-8">
        <flux:heading size="xl">Your Projects</flux:heading>
        <flux:subheading>Track progress and pay milestones as they come due.</flux:subheading>
    </div>

    @if($this->projects()->isEmpty())
        <div class="flex flex-col items-center justify-center rounded-2xl border border-slate-200 dark:border-white/10 py-16 px-6 text-center">
            <span class="flex items-center justify-center w-14 h-14 rounded-2xl bg-cyan-50 dark:bg-cyan-500/10 text-cyan-500 mb-4">
                <flux:icon.briefcase class="size-7" />
            </span>
            <flux:heading size="lg">No projects yet</flux:heading>
            <flux:subheading class="max-w-sm mt-1">
                Once your project kicks off, you'll track milestones and payments right here.
            </flux:subheading>
            <flux:button :href="route('home')" class="mt-6">Back to Homepage</flux:button>
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            @foreach($this->projects() as $project)
                @php
                    $total = $project->totalAmount();
                    $paid = $project->paidAmount();
                    $pct = $total > 0 ? min(100, round(($paid / $total) * 100)) : 0;
                @endphp
                <a
                    href="{{ route('portal.projects.show', $project) }}"
                    wire:navigate
                    class="group block rounded-2xl border border-slate-200 dark:border-white/10 bg-white dark:bg-white/[0.03] p-6 space-y-4 hover:border-cyan-300 dark:hover:border-cyan-500/40 hover:shadow-lg hover:shadow-cyan-100 dark:hover:shadow-cyan-500/10 hover:-translate-y-0.5 transition-all"
                >
                    <div class="flex items-center justify-between gap-3">
                        <flux:heading size="lg" class="group-hover:text-cyan-600 dark:group-hover:text-cyan-400 transition">{{ $project->name }}</flux:heading>
                        <flux:badge :color="match($project->status) {
                            'active' => 'green',
                            'on_hold' => 'amber',
                            'completed' => 'blue',
                            'cancelled' => 'red',
                            default => 'zinc',
                        }">{{ ucfirst(str_replace('_', ' ', $project->status)) }}</flux:badge>
                    </div>

                    @if($project->is_pro_bono)
                        <flux:badge color="cyan">Pro Bono</flux:badge>
                    @endif

                    <div>
                        <div class="flex items-center justify-between text-sm mb-1.5">
                            <span class="text-slate-500 dark:text-slate-400">${{ number_format($paid, 2) }} paid</span>
                            <span class="text-slate-400 dark:text-slate-500">of ${{ number_format($total, 2) }}</span>
                        </div>
                        <div class="h-1.5 rounded-full bg-slate-100 dark:bg-white/10 overflow-hidden">
                            <div class="h-full rounded-full bg-cyan-500" style="width: {{ $pct }}%"></div>
                        </div>
                    </div>

                    <div class="flex items-center gap-1 text-sm font-semibold text-cyan-600 dark:text-cyan-400">
                        View Project
                        <flux:icon.arrow-right class="size-4 group-hover:translate-x-1 transition-transform" />
                    </div>
                </a>
            @endforeach
        </div>
    @endif
</section>
