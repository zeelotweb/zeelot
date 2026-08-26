<?php

use App\Models\Project;
use Livewire\Volt\Component;

new class extends Component
{
    public function projects()
    {
        return Project::with(['user', 'milestones'])->latest()->get();
    }
}; ?>

<section class="w-full">
    <div class="mb-8">
        <flux:heading size="xl">Projects</flux:heading>
        <flux:subheading>All customer engagements.</flux:subheading>
    </div>

    <flux:table>
        <flux:table.columns>
            <flux:table.column>Project</flux:table.column>
            <flux:table.column>Customer</flux:table.column>
            <flux:table.column>Status</flux:table.column>
            <flux:table.column>Type</flux:table.column>
            <flux:table.column>Paid / Total</flux:table.column>
        </flux:table.columns>

        <flux:table.rows>
            @forelse($this->projects() as $project)
                <flux:table.row wire:key="project-{{ $project->id }}">
                    <flux:table.cell>
                        <flux:link :href="route('admin.projects.show', $project)" wire:navigate>{{ $project->name }}</flux:link>
                    </flux:table.cell>
                    <flux:table.cell>{{ $project->user->name }}</flux:table.cell>
                    <flux:table.cell>
                        <flux:badge :color="match($project->status) {
                            'active' => 'green',
                            'on_hold' => 'amber',
                            'completed' => 'blue',
                            'cancelled' => 'red',
                            default => 'zinc',
                        }">{{ ucfirst(str_replace('_', ' ', $project->status)) }}</flux:badge>
                    </flux:table.cell>
                    <flux:table.cell>
                        @if($project->is_pro_bono)
                            <flux:badge color="cyan">Pro Bono</flux:badge>
                        @else
                            <flux:badge color="zinc">Paid</flux:badge>
                        @endif
                    </flux:table.cell>
                    <flux:table.cell>${{ number_format($project->paidAmount(), 2) }} / ${{ number_format($project->totalAmount(), 2) }}</flux:table.cell>
                </flux:table.row>
            @empty
                <flux:table.row>
                    <flux:table.cell colspan="5">
                        <flux:text class="text-center py-8">No projects yet.</flux:text>
                    </flux:table.cell>
                </flux:table.row>
            @endforelse
        </flux:table.rows>
    </flux:table>
</section>
