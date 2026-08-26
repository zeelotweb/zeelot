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
        <flux:card class="text-center py-12">
            <flux:text>No active projects yet. Reach out via the contact form on the homepage to get started.</flux:text>
        </flux:card>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            @foreach($this->projects() as $project)
                <flux:card class="space-y-3">
                    <div class="flex items-center justify-between">
                        <flux:heading size="lg">{{ $project->name }}</flux:heading>
                        <flux:badge :color="match($project->status) {
                            'active' => 'green',
                            'on_hold' => 'amber',
                            'completed' => 'blue',
                            'cancelled' => 'red',
                            default => 'zinc',
                        }">{{ ucfirst(str_replace('_', ' ', $project->status)) }}</flux:badge>
                    </div>
                    <flux:text>${{ number_format($project->paidAmount(), 2) }} paid of ${{ number_format($project->totalAmount(), 2) }}</flux:text>
                    <flux:button :href="route('portal.projects.show', $project)" wire:navigate>View Project</flux:button>
                </flux:card>
            @endforeach
        </div>
    @endif
</section>
