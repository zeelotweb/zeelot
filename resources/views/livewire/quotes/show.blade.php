<?php

use App\Models\Quote;
use Livewire\Volt\Component;

new class extends Component
{
    public Quote $quote;
    public string $signatureName = '';
    public string $declineReason = '';
    public bool $showDeclineForm = false;

    public function mount(string $token): void
    {
        $this->quote = Quote::with(['lineItems', 'lead'])->where('token', $token)->firstOrFail();
    }

    public function accept(): void
    {
        if ($this->quote->status !== 'sent' || $this->quote->isExpired()) {
            return;
        }

        if ($this->quote->requiresSignature()) {
            $this->validate([
                'signatureName' => 'required|min:2',
            ], [
                'signatureName.required' => 'Type your full name to sign this quote.',
            ]);
        }

        $this->quote->accept($this->quote->requiresSignature() ? $this->signatureName : null);
        $this->quote->refresh();
    }

    public function decline(): void
    {
        if ($this->quote->status !== 'sent' || $this->quote->isExpired()) {
            return;
        }

        $this->quote->decline($this->declineReason ?: null);
        $this->quote->refresh();
    }
}; ?>

<div class="space-y-8">
    @if($quote->status === 'accepted')
        <div class="text-center py-12">
            <div class="inline-flex items-center justify-center w-16 h-16 bg-cyan-500/20 text-cyan-400 rounded-full mb-6">
                <flux:icon.check class="size-8" />
            </div>
            <h1 class="text-2xl font-bold mb-2">Quote accepted</h1>
            <p class="text-slate-400 max-w-md mx-auto">
                Thanks{{ $quote->signature_name ? ', '.$quote->signature_name : '' }} — we're getting your project set up now.
                @if(! $quote->lead->converted_to_project_id)
                    Check your email for a link to create your account, and your project will appear as soon as you register.
                @else
                    You can check its progress from your dashboard.
                @endif
            </p>
        </div>
    @elseif($quote->status === 'declined')
        <div class="text-center py-12">
            <div class="inline-flex items-center justify-center w-16 h-16 bg-slate-800 text-slate-400 rounded-full mb-6">
                <flux:icon.x-mark class="size-8" />
            </div>
            <h1 class="text-2xl font-bold mb-2">Quote declined</h1>
            <p class="text-slate-400 max-w-md mx-auto">You declined this quote. If anything changes, feel free to reach back out.</p>
        </div>
    @elseif($quote->isExpired())
        <div class="text-center py-12">
            <div class="inline-flex items-center justify-center w-16 h-16 bg-amber-500/20 text-amber-400 rounded-full mb-6">
                <flux:icon.clock class="size-8" />
            </div>
            <h1 class="text-2xl font-bold mb-2">This quote has expired</h1>
            <p class="text-slate-400 max-w-md mx-auto">It was valid until {{ $quote->valid_until->format('F j, Y') }}. Reach out and we'll send a fresh one.</p>
        </div>
    @else
        <div>
            <span class="inline-block mb-3 text-xs font-mono font-bold tracking-wide text-cyan-400 uppercase bg-cyan-500/10 border border-cyan-500/30 rounded-full px-3 py-1">Quote</span>
            <h1 class="text-3xl font-bold mb-2">For {{ $quote->lead->company ?: $quote->lead->name }}</h1>
            @if($quote->valid_until)
                <p class="text-slate-400">Valid until {{ $quote->valid_until->format('F j, Y') }}</p>
            @endif
        </div>

        <div class="bg-white/[0.03] border border-white/10 rounded-2xl overflow-hidden">
            @foreach($quote->lineItems as $item)
                <div class="flex items-start justify-between gap-4 px-6 py-4 border-b border-white/10 last:border-0">
                    <div>
                        <div class="font-semibold">{{ $item->title }}</div>
                        @if($item->description)
                            <div class="text-sm text-slate-400 mt-0.5">{{ $item->description }}</div>
                        @endif
                    </div>
                    <div class="font-mono whitespace-nowrap">${{ number_format($item->amount, 2) }}</div>
                </div>
            @endforeach
            <div class="flex items-center justify-between gap-4 px-6 py-4 bg-white/[0.02]">
                <div class="font-bold">Total</div>
                <div class="font-mono font-bold text-cyan-400 text-lg">${{ number_format($quote->total(), 2) }}</div>
            </div>
        </div>

        @if($quote->note)
            <p class="text-slate-300">{{ $quote->note }}</p>
        @endif

        @if(! $showDeclineForm)
            <div class="space-y-4">
                @if($quote->requiresSignature())
                    <div>
                        <label class="block text-sm font-semibold text-slate-400 mb-2">Type your full name to sign &amp; accept</label>
                        <input wire:model="signatureName" type="text" placeholder="Full name" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-4 py-3 text-white focus:ring-2 focus:ring-cyan-500 outline-none transition">
                        @error('signatureName') <span class="text-red-400 text-xs mt-1 block">{{ $message }}</span> @enderror
                        <p class="text-xs text-slate-500 mt-2">By typing your name and accepting, you're agreeing to the scope and total above.</p>
                    </div>
                @endif

                <div class="flex flex-wrap gap-3">
                    <flux:button wire:click="accept" variant="primary" class="flex-1">
                        {{ $quote->requiresSignature() ? 'Sign & Accept' : 'Accept' }}
                    </flux:button>
                    <flux:button wire:click="$set('showDeclineForm', true)" variant="ghost">Decline</flux:button>
                </div>
            </div>
        @else
            <div class="space-y-4 bg-white/[0.03] border border-white/10 rounded-2xl p-6">
                <label class="block text-sm font-semibold text-slate-400">Anything you want us to know? (optional)</label>
                <textarea wire:model="declineReason" rows="3" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-4 py-3 text-white focus:ring-2 focus:ring-cyan-500 outline-none transition"></textarea>
                <div class="flex gap-3">
                    <flux:button wire:click="decline" variant="danger">Confirm Decline</flux:button>
                    <flux:button wire:click="$set('showDeclineForm', false)" variant="ghost">Back</flux:button>
                </div>
            </div>
        @endif
    @endif
</div>
