<?php

use App\Models\DiscountCategory;
use App\Models\DiscountCode;
use Livewire\Volt\Component;

new class extends Component
{
    public ?int $categoryEditingId = null;
    public string $categoryName = '';
    public string $categoryType = 'percentage';
    public string $categoryValue = '';
    public bool $categoryActive = true;

    public ?int $codeCategoryId = null;
    public string $codeCustomerEmail = '';
    public string $codeMaxUses = '';
    public string $codeExpiresAt = '';

    public function mount(): void
    {
        abort_unless(auth()->user()->isAdmin(), 403);
    }

    public function categories()
    {
        return DiscountCategory::orderBy('name')->get();
    }

    public function codes()
    {
        return DiscountCode::with(['category', 'issuedBy'])->latest()->limit(50)->get();
    }

    public function startCategoryCreate(): void
    {
        abort_unless(auth()->user()->isSuperAdmin(), 403);

        $this->reset(['categoryEditingId', 'categoryName', 'categoryValue']);
        $this->categoryType = 'percentage';
        $this->categoryActive = true;
        $this->modal('category-form')->show();
    }

    public function startCategoryEdit(int $id): void
    {
        abort_unless(auth()->user()->isSuperAdmin(), 403);

        $category = DiscountCategory::findOrFail($id);

        $this->categoryEditingId = $category->id;
        $this->categoryName = $category->name;
        $this->categoryType = $category->type;
        $this->categoryValue = (string) $category->value;
        $this->categoryActive = $category->is_active;

        $this->modal('category-form')->show();
    }

    public function saveCategory(): void
    {
        abort_unless(auth()->user()->isSuperAdmin(), 403);

        $this->validate([
            'categoryName' => 'required|min:2',
            'categoryType' => 'required|in:'.implode(',', config('discounts.types')),
            'categoryValue' => 'required|numeric|min:0|max:'.($this->categoryType === 'percentage' ? config('discounts.max_percentage') : 999999),
        ]);

        $data = [
            'name' => $this->categoryName,
            'type' => $this->categoryType,
            'value' => $this->categoryValue,
            'is_active' => $this->categoryActive,
        ];

        if ($this->categoryEditingId) {
            DiscountCategory::whereKey($this->categoryEditingId)->update($data);
        } else {
            DiscountCategory::create($data);
        }

        $this->modal('category-form')->close();
        session()->flash('status', 'Discount category saved.');
    }

    public function deleteCategory(int $id): void
    {
        abort_unless(auth()->user()->isSuperAdmin(), 403);

        DiscountCategory::whereKey($id)->delete();
        session()->flash('status', 'Discount category deleted.');
    }

    public function startCodeIssue(): void
    {
        abort_unless(auth()->user()->canIssueDiscounts(), 403);

        $this->reset(['codeCategoryId', 'codeCustomerEmail', 'codeMaxUses', 'codeExpiresAt']);
        $this->modal('code-form')->show();
    }

    public function issueCode(): void
    {
        abort_unless(auth()->user()->canIssueDiscounts(), 403);

        $this->validate([
            'codeCategoryId' => 'required|exists:discount_categories,id',
            'codeCustomerEmail' => 'nullable|email',
            'codeMaxUses' => 'nullable|integer|min:1',
            'codeExpiresAt' => 'nullable|date|after:now',
        ]);

        DiscountCode::create([
            'code' => DiscountCode::generateCode(),
            'discount_category_id' => $this->codeCategoryId,
            'created_by' => auth()->id(),
            'customer_email' => $this->codeCustomerEmail ?: null,
            'max_uses' => $this->codeMaxUses ?: null,
            'expires_at' => $this->codeExpiresAt ?: null,
        ]);

        $this->modal('code-form')->close();
        session()->flash('status', 'Discount code issued.');
    }

    public function revokeCode(int $id): void
    {
        abort_unless(auth()->user()->canIssueDiscounts(), 403);

        DiscountCode::whereKey($id)->update(['is_active' => false]);
        session()->flash('status', 'Discount code revoked.');
    }
}; ?>

<section class="w-full space-y-10">
    <div>
        <flux:heading size="xl">Discounts</flux:heading>
        <flux:subheading>Discount categories and the codes issued from them.</flux:subheading>
    </div>

    @if (session('status'))
        <flux:callout variant="success" icon="check-circle" :heading="session('status')" />
    @endif

    @if (auth()->user()->isSuperAdmin())
        <div>
            <div class="flex items-center justify-between mb-4">
                <flux:heading size="lg">Categories</flux:heading>
                <flux:button size="sm" variant="primary" wire:click="startCategoryCreate">Add Category</flux:button>
            </div>

            <flux:table>
                <flux:table.columns>
                    <flux:table.column>Name</flux:table.column>
                    <flux:table.column>Type</flux:table.column>
                    <flux:table.column>Value</flux:table.column>
                    <flux:table.column>Status</flux:table.column>
                    <flux:table.column>Actions</flux:table.column>
                </flux:table.columns>
                <flux:table.rows>
                    @forelse ($this->categories() as $category)
                        <flux:table.row wire:key="cat-{{ $category->id }}">
                            <flux:table.cell>{{ $category->name }}</flux:table.cell>
                            <flux:table.cell>{{ ucfirst($category->type) }}</flux:table.cell>
                            <flux:table.cell>{{ $category->type === 'percentage' ? $category->value.'%' : '$'.number_format($category->value, 2) }}</flux:table.cell>
                            <flux:table.cell>
                                <flux:badge :color="$category->is_active ? 'green' : 'zinc'">{{ $category->is_active ? 'Active' : 'Inactive' }}</flux:badge>
                            </flux:table.cell>
                            <flux:table.cell>
                                <div class="flex gap-2">
                                    <flux:button size="sm" wire:click="startCategoryEdit({{ $category->id }})">Edit</flux:button>
                                    <flux:button size="sm" variant="danger" wire:click="deleteCategory({{ $category->id }})">Delete</flux:button>
                                </div>
                            </flux:table.cell>
                        </flux:table.row>
                    @empty
                        <flux:table.row>
                            <flux:table.cell colspan="5">
                                <div class="py-8 text-center"><flux:text>No discount categories yet.</flux:text></div>
                            </flux:table.cell>
                        </flux:table.row>
                    @endforelse
                </flux:table.rows>
            </flux:table>
        </div>
    @endif

    <div>
        <div class="flex items-center justify-between mb-4">
            <div>
                <flux:heading size="lg">Codes</flux:heading>
                <flux:subheading>Every job request gets a default {{ config('discounts.default_percentage') }}% off automatically — codes stack on top of that.</flux:subheading>
            </div>
            @if (auth()->user()->canIssueDiscounts())
                <flux:button size="sm" variant="primary" wire:click="startCodeIssue">Issue Code</flux:button>
            @endif
        </div>

        @if (! auth()->user()->canIssueDiscounts())
            <flux:callout variant="secondary" icon="lock-closed" heading="You can't issue discount codes">
                Ask a super admin to grant you access from the Team page.
            </flux:callout>
        @else
            <flux:table>
                <flux:table.columns>
                    <flux:table.column>Code</flux:table.column>
                    <flux:table.column>Category</flux:table.column>
                    <flux:table.column>For</flux:table.column>
                    <flux:table.column>Usage</flux:table.column>
                    <flux:table.column>Issued By</flux:table.column>
                    <flux:table.column>Status</flux:table.column>
                    <flux:table.column>Actions</flux:table.column>
                </flux:table.columns>
                <flux:table.rows>
                    @forelse ($this->codes() as $code)
                        <flux:table.row wire:key="code-{{ $code->id }}">
                            <flux:table.cell class="font-mono">{{ $code->code }}</flux:table.cell>
                            <flux:table.cell>{{ $code->category->name }}</flux:table.cell>
                            <flux:table.cell>{{ $code->customer_email ?: 'Anyone' }}</flux:table.cell>
                            <flux:table.cell>{{ $code->used_count }}{{ $code->max_uses ? ' / '.$code->max_uses : '' }}</flux:table.cell>
                            <flux:table.cell>{{ $code->issuedBy?->name ?? '—' }}</flux:table.cell>
                            <flux:table.cell>
                                @if (! $code->is_active)
                                    <flux:badge color="zinc">Revoked</flux:badge>
                                @elseif ($code->expires_at && $code->expires_at->isPast())
                                    <flux:badge color="amber">Expired</flux:badge>
                                @else
                                    <flux:badge color="green">Active</flux:badge>
                                @endif
                            </flux:table.cell>
                            <flux:table.cell>
                                @if ($code->is_active)
                                    <flux:button size="sm" variant="danger" wire:click="revokeCode({{ $code->id }})">Revoke</flux:button>
                                @endif
                            </flux:table.cell>
                        </flux:table.row>
                    @empty
                        <flux:table.row>
                            <flux:table.cell colspan="7">
                                <div class="py-8 text-center"><flux:text>No codes issued yet.</flux:text></div>
                            </flux:table.cell>
                        </flux:table.row>
                    @endforelse
                </flux:table.rows>
            </flux:table>
        @endif
    </div>

    <flux:modal name="category-form" class="md:w-96">
        <form wire:submit="saveCategory" class="space-y-4">
            <flux:heading size="lg">{{ $categoryEditingId ? 'Edit Category' : 'Add Category' }}</flux:heading>
            <flux:input label="Name" wire:model="categoryName" placeholder="Referral, Promotional, Loyalty..." />
            <flux:select label="Type" wire:model="categoryType">
                @foreach (config('discounts.types') as $type)
                    <flux:select.option value="{{ $type }}">{{ ucfirst($type) }}</flux:select.option>
                @endforeach
            </flux:select>
            <flux:input label="Value" type="number" step="0.01" wire:model="categoryValue" />
            <flux:checkbox label="Active" wire:model="categoryActive" />
            <flux:button type="submit" variant="primary" class="w-full">Save Category</flux:button>
        </form>
    </flux:modal>

    <flux:modal name="code-form" class="md:w-96">
        <form wire:submit="issueCode" class="space-y-4">
            <flux:heading size="lg">Issue Discount Code</flux:heading>
            <flux:select label="Category" wire:model="codeCategoryId">
                <flux:select.option value="">Select a category</flux:select.option>
                @foreach ($this->categories()->where('is_active', true) as $category)
                    <flux:select.option value="{{ $category->id }}">{{ $category->name }} ({{ $category->type === 'percentage' ? $category->value.'%' : '$'.$category->value }})</flux:select.option>
                @endforeach
            </flux:select>
            <flux:input label="Restrict to customer email (optional)" wire:model="codeCustomerEmail" />
            <flux:input label="Max uses (optional)" type="number" wire:model="codeMaxUses" />
            <flux:input label="Expires at (optional)" type="date" wire:model="codeExpiresAt" />
            <flux:button type="submit" variant="primary" class="w-full">Issue Code</flux:button>
        </form>
    </flux:modal>
</section>
