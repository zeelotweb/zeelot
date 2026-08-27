<?php

use App\Models\Project;
use App\Models\ProjectMessage;
use App\Models\ProjectMilestone;
use Livewire\Volt\Component;

new class extends Component
{
    public Project $project;
    public string $newMessageBody = '';
    public ?string $checkoutStatus = null;

    public function mount(Project $project): void
    {
        $this->authorize('view', $project);

        $this->project = $project;
        $this->checkoutStatus = request()->query('checkout');
    }

    public function milestones()
    {
        return $this->project->milestones()->get();
    }

    public function projectMessages()
    {
        return $this->project->messages()->with('user')->get();
    }

    public function sendMessage(): void
    {
        $this->validate(['newMessageBody' => 'required|min:1']);

        ProjectMessage::create([
            'project_id' => $this->project->id,
            'user_id' => auth()->id(),
            'body' => $this->newMessageBody,
        ]);

        $this->reset('newMessageBody');
    }

    public function payMilestone(int $milestoneId)
    {
        $milestone = ProjectMilestone::findOrFail($milestoneId);

        abort_unless($milestone->project_id === $this->project->id, 403);
        abort_unless($milestone->status === 'invoiced', 403);

        $stripe = new \Stripe\StripeClient(config('services.stripe.secret'));

        $session = $stripe->checkout->sessions->create([
            'mode' => 'payment',
            'line_items' => [[
                'price_data' => [
                    'currency' => 'usd',
                    'product_data' => ['name' => $milestone->title],
                    'unit_amount' => (int) round($milestone->amount * 100),
                ],
                'quantity' => 1,
            ]],
            'metadata' => ['milestone_id' => $milestone->id],
            'success_url' => route('portal.projects.show', $this->project).'?checkout=success',
            'cancel_url' => route('portal.projects.show', $this->project).'?checkout=cancelled',
        ]);

        $milestone->update(['stripe_checkout_session_id' => $session->id]);

        return $this->redirect($session->url);
    }
}; ?>

<section class="w-full space-y-8">
    <div>
        <flux:link :href="route('portal.projects.index')" wire:navigate class="inline-flex items-center gap-1 text-sm text-slate-500 dark:text-slate-400 hover:text-cyan-600 dark:hover:text-cyan-400 mb-3">
            <flux:icon.arrow-left class="size-4" />
            Your Projects
        </flux:link>
        <div class="flex items-center gap-3 flex-wrap">
            <flux:heading size="xl">{{ $project->name }}</flux:heading>
            @if($project->is_pro_bono)
                <flux:badge color="cyan">Pro Bono</flux:badge>
            @endif
        </div>

        @php
            $total = $project->totalAmount();
            $paid = $project->paidAmount();
            $pct = $total > 0 ? min(100, round(($paid / $total) * 100)) : 0;
        @endphp
        <div class="max-w-sm mt-4">
            <div class="flex items-center justify-between text-sm mb-1.5">
                <span class="text-slate-500 dark:text-slate-400">${{ number_format($paid, 2) }} paid</span>
                <span class="text-slate-400 dark:text-slate-500">of ${{ number_format($total, 2) }}</span>
            </div>
            <div class="h-1.5 rounded-full bg-slate-100 dark:bg-white/10 overflow-hidden">
                <div class="h-full rounded-full bg-cyan-500" style="width: {{ $pct }}%"></div>
            </div>
        </div>
    </div>

    @if($checkoutStatus === 'success')
        <flux:callout variant="success" icon="check-circle" heading="Payment received">
            Thanks! We'll update this milestone as soon as it's confirmed.
        </flux:callout>
    @elseif($checkoutStatus === 'cancelled')
        <flux:callout variant="warning" icon="exclamation-triangle" heading="Payment cancelled">
            No charge was made. You can try again below whenever you're ready.
        </flux:callout>
    @endif

    <div>
        <flux:heading size="lg" class="mb-4">Milestones</flux:heading>

        <div class="space-y-3">
            @forelse($this->milestones() as $milestone)
                <div class="flex items-center justify-between gap-4 rounded-2xl border border-slate-200 dark:border-white/10 bg-white dark:bg-white/[0.03] px-5 py-4 hover:border-cyan-300 dark:hover:border-cyan-500/40 transition">
                    <div>
                        <div class="font-bold text-slate-900 dark:text-white">{{ $milestone->title }}</div>
                        @if($milestone->description)
                            <div class="text-sm text-slate-500 dark:text-slate-400">{{ $milestone->description }}</div>
                        @endif
                        <div class="text-sm text-slate-500 dark:text-slate-400">${{ number_format($milestone->amount, 2) }}</div>
                    </div>
                    <div class="flex items-center gap-3">
                        <flux:badge :color="match($milestone->status) {
                            'pending' => 'zinc',
                            'invoiced' => 'amber',
                            'paid' => 'green',
                            'waived' => 'blue',
                            default => 'zinc',
                        }">{{ $milestone->status === 'invoiced' ? 'Awaiting Payment' : ucfirst($milestone->status) }}</flux:badge>

                        @if($milestone->status === 'invoiced')
                            <flux:button size="sm" variant="primary" wire:click="payMilestone({{ $milestone->id }})">Pay Now</flux:button>
                        @endif
                    </div>
                </div>
            @empty
                <div class="rounded-2xl border border-slate-200 dark:border-white/10 py-10 text-center">
                    <flux:text>No milestones yet — check back soon.</flux:text>
                </div>
            @endforelse
        </div>
    </div>

    <div>
        <flux:heading size="lg" class="mb-4">Messages</flux:heading>

        <div class="space-y-4 mb-4 max-h-96 overflow-y-auto">
            @forelse($this->projectMessages() as $message)
                <div class="p-3 rounded-lg {{ $message->user_id === auth()->id() ? 'bg-cyan-50 dark:bg-cyan-500/10 ms-8' : 'bg-zinc-100 dark:bg-zinc-800 me-8' }}">
                    <div class="text-xs text-zinc-500 mb-1">{{ $message->user->name }} &middot; {{ $message->created_at->diffForHumans() }}</div>
                    <div>{{ $message->body }}</div>
                </div>
            @empty
                <flux:text>No messages yet.</flux:text>
            @endforelse
        </div>

        <form wire:submit="sendMessage" class="flex gap-2">
            <flux:textarea wire:model="newMessageBody" placeholder="Message the ZeelotWeb team..." class="flex-1" rows="2" />
            <flux:button type="submit" variant="primary">Send</flux:button>
        </form>
    </div>
</section>
