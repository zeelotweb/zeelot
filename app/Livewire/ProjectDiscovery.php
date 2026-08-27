<?php

namespace App\Livewire;

use App\Mail\NewLeadAlert;
use App\Models\DiscountCode;
use App\Models\Lead;
use App\Models\Package;
use Illuminate\Support\Facades\Mail;
use Livewire\Attributes\On;
use Livewire\Component;

class ProjectDiscovery extends Component
{
    public $name;
    public $email;
    public $company;
    public $message;
    public $success = false;
    public bool $isProBono = false;

    public array $selectedPackageIds = [];
    public string $discountCodeInput = '';
    public ?array $appliedDiscount = null;
    public string $discountError = '';

    protected $rules = [
        'name' => 'required|min:3',
        'email' => 'required|email',
        'message' => 'required|min:10',
    ];

    public function mount(): void
    {
        $this->isProBono = request()->query('intent') === 'probono';

        $this->prefillFromAuthUser();
    }

    protected function prefillFromAuthUser(): void
    {
        if (auth()->check()) {
            $this->name = auth()->user()->name;
            $this->email = auth()->user()->email;
        }
    }

    public function packages()
    {
        return Package::active()->orderBy('sort_order')->orderBy('name')->get();
    }

    public function subtotal(): float
    {
        return (float) Package::whereIn('id', $this->selectedPackageIds)->sum('price');
    }

    public function defaultDiscountAmount(): float
    {
        return $this->subtotal() * (config('discounts.default_percentage') / 100);
    }

    public function codeDiscountAmount(): float
    {
        return $this->appliedDiscount['amount'] ?? 0;
    }

    public function totalDiscount(): float
    {
        return $this->defaultDiscountAmount() + $this->codeDiscountAmount();
    }

    public function total(): float
    {
        return max(0, $this->subtotal() - $this->totalDiscount());
    }

    public function applyDiscountCode(): void
    {
        $this->discountError = '';
        $this->appliedDiscount = null;

        if (! $this->discountCodeInput) {
            return;
        }

        $code = DiscountCode::where('code', strtoupper(trim($this->discountCodeInput)))->first();

        if (! $code || ! $code->isValidFor($this->email)) {
            $this->discountError = 'That code is not valid.';

            return;
        }

        $this->appliedDiscount = [
            'code_id' => $code->id,
            'amount' => $code->discountFor($this->subtotal()),
        ];
    }

    public function submit()
    {
        $this->validate();

        if (! $this->isProBono) {
            $this->validate([
                'selectedPackageIds' => 'required|array|min:1',
            ], [
                'selectedPackageIds.required' => 'Select at least one package.',
            ]);
        }

        $discountAmount = $this->isProBono ? 0 : $this->totalDiscount();

        $lead = Lead::create([
            'name' => $this->name,
            'email' => $this->email,
            'company' => $this->company,
            'budget' => $this->isProBono ? 0 : $this->subtotal(),
            'message' => $this->message,
            'is_pro_bono' => $this->isProBono,
            'discount_code_id' => $this->appliedDiscount['code_id'] ?? null,
            'discount_amount' => $discountAmount,
        ]);

        if (! $this->isProBono) {
            $lead->packages()->sync($this->selectedPackageIds);
        }

        if (isset($this->appliedDiscount['code_id'])) {
            DiscountCode::find($this->appliedDiscount['code_id'])?->redeem();
        }

        Mail::to(config('mail.admin_address'))->send(new NewLeadAlert($lead));

        $this->success = true;
        $this->reset(['name', 'email', 'company', 'message', 'selectedPackageIds', 'discountCodeInput', 'appliedDiscount', 'discountError']);
    }

    #[On('resetDiscoveryForm')]
    public function resetForm(): void
    {
        $this->success = false;
        $this->reset(['name', 'email', 'company', 'message', 'selectedPackageIds', 'discountCodeInput', 'appliedDiscount', 'discountError']);
        $this->prefillFromAuthUser();
    }

    public function render()
    {
        return view('livewire.project-discovery');
    }
}
