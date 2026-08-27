<?php

use App\Mail\LeadDeclinedMail;
use App\Mail\QuoteMail;
use App\Models\Lead;
use App\Models\Quote;
use Illuminate\Support\Facades\Mail;
use Livewire\Attributes\Computed;
use Livewire\Volt\Component;

new class extends Component
{
    public string $statusFilter = 'all';
    public bool $proBonoOnly = false;

    public ?int $draftingForLeadId = null;
    public array $quoteItems = [];
    public string $quoteNote = '';
    public int $quoteValidDays = 14;

    public ?int $decliningLeadId = null;
    public string $declineReasonInput = '';

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

    public function startQuoteDraft(int $leadId): void
    {
        $lead = Lead::with('packages')->findOrFail($leadId);

        $this->draftingForLeadId = $leadId;
        $this->quoteNote = '';
        $this->quoteValidDays = 14;

        if ($lead->packages->isEmpty()) {
            $this->quoteItems = [['title' => '', 'description' => '', 'amount' => '']];

            return;
        }

        $this->quoteItems = $lead->packages->map(fn ($package) => [
            'title' => $package->name,
            'description' => $package->description,
            'amount' => (string) $package->price,
        ])->all();

        if ($lead->discount_amount > 0) {
            $this->quoteItems[] = [
                'title' => 'Discount'.($lead->discountCode ? ' ('.$lead->discountCode->code.')' : ''),
                'description' => null,
                'amount' => (string) (-1 * (float) $lead->discount_amount),
            ];
        }
    }

    public function cancelQuoteDraft(): void
    {
        $this->draftingForLeadId = null;
        $this->quoteItems = [];
    }

    public function addQuoteItem(): void
    {
        $this->quoteItems[] = ['title' => '', 'description' => '', 'amount' => ''];
    }

    public function removeQuoteItem(int $index): void
    {
        unset($this->quoteItems[$index]);
        $this->quoteItems = array_values($this->quoteItems);
    }

    public function sendQuote(): void
    {
        $this->validate([
            'quoteItems' => 'required|array|min:1',
            'quoteItems.*.title' => 'required|string|min:2',
            'quoteItems.*.amount' => 'required|numeric|min:0',
            'quoteValidDays' => 'required|integer|min:1',
        ]);

        $lead = Lead::findOrFail($this->draftingForLeadId);

        $quote = Quote::createFor($lead, $this->quoteItems, auth()->user(), $this->quoteValidDays, $this->quoteNote ?: null);

        Mail::to($lead->email)->send(new QuoteMail($quote));

        $this->draftingForLeadId = null;
        $this->quoteItems = [];
        unset($this->leads);
    }

    public function startDecline(int $leadId): void
    {
        $this->decliningLeadId = $leadId;
        $this->declineReasonInput = '';
    }

    public function cancelDecline(): void
    {
        $this->decliningLeadId = null;
    }

    public function confirmDecline(): void
    {
        $lead = Lead::findOrFail($this->decliningLeadId);
        $lead->decline($this->declineReasonInput ?: null);

        Mail::to($lead->email)->send(new LeadDeclinedMail($lead));

        $this->decliningLeadId = null;
        unset($this->leads);
    }
}; ?>

<section class="w-full">
    <div class="mb-8 flex items-center justify-between">
        <div>
            <flux:heading size="xl">Leads Inbox</flux:heading>
            <flux:subheading>Incoming inquiries and pro bono applications.</flux:subheading>
        </div>
    </div>

    <div class="flex flex-wrap items-center gap-4 mb-6">
        <flux:select wire:model.live="statusFilter" class="max-w-xs">
            <flux:select.option value="all">All statuses</flux:select.option>
            <flux:select.option value="new">New</flux:select.option>
            <flux:select.option value="reviewing">Reviewing</flux:select.option>
            <flux:select.option value="quoted">Quoted</flux:select.option>
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
                @php $quote = $lead->currentQuote(); @endphp
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
                            'quoted' => 'cyan',
                            'declined' => 'red',
                            'converted' => 'teal',
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

                <flux:modal name="lead-{{ $lead->id }}" class="md:w-[36rem]">
                    <div class="space-y-6">
                        <div>
                            <flux:heading size="lg">{{ $lead->name }}</flux:heading>
                            <flux:subheading>{{ $lead->email }} @if($lead->company) &middot; {{ $lead->company }} @endif</flux:subheading>
                        </div>

                        @if($lead->is_pro_bono)
                            <flux:badge color="cyan">Pro Bono Application</flux:badge>
                        @endif

                        <flux:text>{{ $lead->message }}</flux:text>

                        @if($lead->status === 'declined' && $lead->decline_reason)
                            <flux:callout variant="secondary" icon="x-circle" heading="Declined">
                                {{ $lead->decline_reason }}
                            </flux:callout>
                        @endif

                        {{-- Quote summary, once one exists --}}
                        @if($quote)
                            <div class="rounded-xl border border-zinc-200 dark:border-zinc-700 p-4 space-y-2">
                                <div class="flex items-center justify-between">
                                    <flux:heading size="sm">Quote &mdash; ${{ number_format($quote->total(), 2) }}</flux:heading>
                                    <flux:badge :color="match($quote->status) {
                                        'accepted' => 'green',
                                        'declined' => 'red',
                                        default => $quote->isExpired() ? 'zinc' : 'amber',
                                    }">{{ $quote->isExpired() && $quote->status === 'sent' ? 'Expired' : ucfirst($quote->status) }}</flux:badge>
                                </div>
                                <div class="text-sm text-zinc-500">
                                    Sent {{ $quote->sent_at?->diffForHumans() }}
                                    @if($quote->status === 'accepted' && $quote->signature_name)
                                        &middot; Signed by {{ $quote->signature_name }}
                                    @endif
                                    @if($quote->status === 'declined' && $quote->decline_reason)
                                        &middot; "{{ $quote->decline_reason }}"
                                    @endif
                                </div>
                                <flux:link :href="url('/quotes/'.$quote->token)" target="_blank" class="text-sm">View quote page &rarr;</flux:link>
                            </div>
                        @endif

                        @if($lead->converted_to_project_id)
                            <flux:button :href="route('admin.projects.show', $lead->converted_to_project_id)" wire:navigate class="w-full">
                                View Project
                            </flux:button>
                        @elseif(in_array($lead->status, ['new', 'reviewing']))
                            @if($draftingForLeadId === $lead->id)
                                {{-- Quote drafting form --}}
                                <div class="space-y-4 border-t border-zinc-200 dark:border-zinc-700 pt-4">
                                    <flux:heading size="sm">Draft Quote</flux:heading>

                                    @foreach($quoteItems as $i => $item)
                                        <div class="flex gap-2 items-start">
                                            <div class="flex-1 space-y-2">
                                                <flux:input placeholder="Line item title" wire:model="quoteItems.{{ $i }}.title" />
                                                <flux:input placeholder="Amount (USD)" type="number" step="0.01" wire:model="quoteItems.{{ $i }}.amount" />
                                            </div>
                                            @if(count($quoteItems) > 1)
                                                <flux:button size="sm" variant="ghost" icon="trash" wire:click="removeQuoteItem({{ $i }})" />
                                            @endif
                                        </div>
                                    @endforeach

                                    <flux:button size="sm" variant="ghost" wire:click="addQuoteItem">+ Add line item</flux:button>

                                    <flux:input label="Valid for (days)" type="number" wire:model="quoteValidDays" />
                                    <flux:textarea label="Note to customer (optional)" wire:model="quoteNote" rows="2" />

                                    <div class="flex gap-2">
                                        <flux:button wire:click="sendQuote" variant="primary" class="flex-1">Send Quote</flux:button>
                                        <flux:button wire:click="cancelQuoteDraft" variant="ghost">Cancel</flux:button>
                                    </div>
                                </div>
                            @elseif($decliningLeadId === $lead->id)
                                <div class="space-y-3 border-t border-zinc-200 dark:border-zinc-700 pt-4">
                                    <flux:textarea label="Reason (optional, included in the email to them)" wire:model="declineReasonInput" rows="2" />
                                    <div class="flex gap-2">
                                        <flux:button wire:click="confirmDecline" variant="danger" class="flex-1">Confirm Decline</flux:button>
                                        <flux:button wire:click="cancelDecline" variant="ghost">Back</flux:button>
                                    </div>
                                </div>
                            @else
                                <div class="flex flex-wrap gap-2">
                                    @if($lead->status === 'new')
                                        <flux:button size="sm" wire:click="updateStatus({{ $lead->id }}, 'reviewing')">Mark Reviewing</flux:button>
                                    @endif
                                    <flux:button size="sm" variant="primary" wire:click="startQuoteDraft({{ $lead->id }})">Draft Quote</flux:button>
                                    <flux:button size="sm" variant="danger" wire:click="startDecline({{ $lead->id }})">Decline</flux:button>
                                </div>
                            @endif
                        @endif
                    </div>
                </flux:modal>
            @empty
                <flux:table.row>
                    <flux:table.cell colspan="7">
                        <div class="flex flex-col items-center justify-center py-12 text-center">
                            <span class="flex items-center justify-center w-12 h-12 rounded-2xl bg-cyan-50 dark:bg-cyan-500/10 text-cyan-500 mb-3">
                                <flux:icon.inbox class="size-6" />
                            </span>
                            <flux:text>No leads match this filter.</flux:text>
                        </div>
                    </flux:table.cell>
                </flux:table.row>
            @endforelse
        </flux:table.rows>
    </flux:table>
</section>
