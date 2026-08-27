<?php

use App\Models\Package;
use Livewire\Volt\Component;

new class extends Component
{
    public ?int $editingId = null;
    public string $name = '';
    public string $category = 'static';
    public string $description = '';
    public string $price = '';
    public string $featuresText = '';
    public bool $isActive = true;
    public int $sortOrder = 0;

    public function mount(): void
    {
        abort_unless(auth()->user()->isAdmin(), 403);
    }

    public function categoryOptions(): array
    {
        return [
            'static' => 'Simple Static',
            'static_auth' => 'Static + Auth',
            'social' => 'Social Network',
            'media' => 'Media',
            'ecommerce' => 'E-commerce',
            'portal' => 'Staff/Client Portal',
        ];
    }

    public function packages()
    {
        return Package::orderBy('sort_order')->orderBy('name')->get();
    }

    public function startCreate(): void
    {
        $this->reset(['editingId', 'name', 'description', 'price', 'featuresText', 'sortOrder']);
        $this->category = 'static';
        $this->isActive = true;
        $this->modal('package-form')->show();
    }

    public function startEdit(int $id): void
    {
        $package = Package::findOrFail($id);

        $this->editingId = $package->id;
        $this->name = $package->name;
        $this->category = $package->category ?? 'static';
        $this->description = (string) $package->description;
        $this->price = (string) $package->price;
        $this->featuresText = implode("\n", $package->features ?? []);
        $this->isActive = $package->is_active;
        $this->sortOrder = $package->sort_order;

        $this->modal('package-form')->show();
    }

    public function save(): void
    {
        $this->validate([
            'name' => 'required|min:2',
            'category' => 'required|in:'.implode(',', array_keys($this->categoryOptions())),
            'price' => 'required|numeric|min:0',
            'sortOrder' => 'integer|min:0',
        ]);

        $features = collect(explode("\n", $this->featuresText))
            ->map(fn ($line) => trim($line))
            ->filter()
            ->values()
            ->all();

        $data = [
            'name' => $this->name,
            'category' => $this->category,
            'description' => $this->description ?: null,
            'price' => $this->price,
            'features' => $features,
            'is_active' => $this->isActive,
            'sort_order' => $this->sortOrder,
        ];

        if ($this->editingId) {
            Package::whereKey($this->editingId)->update($data);
        } else {
            Package::create($data);
        }

        $this->modal('package-form')->close();
        session()->flash('status', 'Package saved.');
    }

    public function toggleActive(int $id): void
    {
        $package = Package::findOrFail($id);
        $package->update(['is_active' => ! $package->is_active]);
    }

    public function delete(int $id): void
    {
        Package::whereKey($id)->delete();
        session()->flash('status', 'Package deleted.');
    }
}; ?>

<section class="w-full space-y-8">
    <div class="flex items-center justify-between">
        <div>
            <flux:heading size="xl">Packages</flux:heading>
            <flux:subheading>The solutions customers can pick from on the discovery form.</flux:subheading>
        </div>
        <flux:button variant="primary" wire:click="startCreate">Add Package</flux:button>
    </div>

    @if (session('status'))
        <flux:callout variant="success" icon="check-circle" :heading="session('status')" />
    @endif

    <flux:table>
        <flux:table.columns>
            <flux:table.column>Name</flux:table.column>
            <flux:table.column>Category</flux:table.column>
            <flux:table.column>Price</flux:table.column>
            <flux:table.column>Status</flux:table.column>
            <flux:table.column>Actions</flux:table.column>
        </flux:table.columns>

        <flux:table.rows>
            @forelse ($this->packages() as $package)
                <flux:table.row wire:key="package-{{ $package->id }}">
                    <flux:table.cell>
                        <div class="font-medium">{{ $package->name }}</div>
                        @if($package->description)
                            <div class="text-sm text-zinc-500 max-w-sm truncate">{{ $package->description }}</div>
                        @endif
                    </flux:table.cell>
                    <flux:table.cell>{{ $this->categoryOptions()[$package->category] ?? $package->category }}</flux:table.cell>
                    <flux:table.cell>${{ number_format($package->price, 2) }}</flux:table.cell>
                    <flux:table.cell>
                        <button type="button" wire:click="toggleActive({{ $package->id }})">
                            <flux:badge :color="$package->is_active ? 'green' : 'zinc'">{{ $package->is_active ? 'Active' : 'Inactive' }}</flux:badge>
                        </button>
                    </flux:table.cell>
                    <flux:table.cell>
                        <div class="flex gap-2">
                            <flux:button size="sm" wire:click="startEdit({{ $package->id }})">Edit</flux:button>
                            <flux:button size="sm" variant="danger" wire:click="delete({{ $package->id }})">Delete</flux:button>
                        </div>
                    </flux:table.cell>
                </flux:table.row>
            @empty
                <flux:table.row>
                    <flux:table.cell colspan="5">
                        <div class="flex flex-col items-center justify-center py-12 text-center">
                            <flux:text>No packages yet — add the first one.</flux:text>
                        </div>
                    </flux:table.cell>
                </flux:table.row>
            @endforelse
        </flux:table.rows>
    </flux:table>

    <flux:modal name="package-form" class="md:w-[32rem]">
        <form wire:submit="save" class="space-y-4">
            <flux:heading size="lg">{{ $editingId ? 'Edit Package' : 'Add Package' }}</flux:heading>

            <flux:input label="Name" wire:model="name" />

            <flux:select label="Category" wire:model="category">
                @foreach ($this->categoryOptions() as $value => $label)
                    <flux:select.option value="{{ $value }}">{{ $label }}</flux:select.option>
                @endforeach
            </flux:select>

            <flux:textarea label="Description" wire:model="description" rows="2" />

            <flux:input label="Price (USD)" type="number" step="0.01" wire:model="price" />

            <flux:textarea label="Features (one per line)" wire:model="featuresText" rows="4" placeholder="Responsive design&#10;Contact form&#10;Basic SEO setup" />

            <flux:input label="Sort order" type="number" wire:model="sortOrder" />

            <flux:checkbox label="Active" wire:model="isActive" />

            <flux:button type="submit" variant="primary" class="w-full">Save Package</flux:button>
        </form>
    </flux:modal>
</section>
