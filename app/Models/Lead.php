<?php

namespace App\Models;

use App\Notifications\LeadDeclined;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Lead extends Model
{
    protected $fillable = [
        'name',
        'email',
        'company',
        'budget',
        'message',
        'status',
        'decline_reason',
        'is_pro_bono',
        'converted_to_project_id',
        'discount_code_id',
        'discount_amount',
    ];

    public function decline(?string $reason = null): void
    {
        $this->update([
            'status' => 'declined',
            'decline_reason' => $reason,
        ]);

        User::where('email', $this->email)->first()?->notify(new LeadDeclined($this));
    }

    protected function casts(): array
    {
        return [
            'is_pro_bono' => 'boolean',
            'discount_amount' => 'decimal:2',
        ];
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<Project, $this>
     */
    public function project()
    {
        return $this->belongsTo(Project::class, 'converted_to_project_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany<Package, $this>
     */
    public function packages()
    {
        return $this->belongsToMany(Package::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<DiscountCode, $this>
     */
    public function discountCode()
    {
        return $this->belongsTo(DiscountCode::class);
    }

    public function packagesSubtotal(): float
    {
        return (float) $this->packages()->sum('price');
    }

    public function total(): float
    {
        return max(0, $this->packagesSubtotal() - (float) ($this->discount_amount ?? 0));
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<Quote, $this>
     */
    public function quotes()
    {
        return $this->hasMany(Quote::class)->latest();
    }

    public function currentQuote(): ?Quote
    {
        return $this->quotes()->first();
    }

    public function acceptedQuote(): ?Quote
    {
        return $this->quotes()->where('status', 'accepted')->first();
    }

    public function scopeProBono(Builder $query): Builder
    {
        return $query->where('is_pro_bono', true);
    }

    public static function proBonoCountThisMonth(): int
    {
        return static::proBono()
            ->whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth()])
            ->count();
    }
}
