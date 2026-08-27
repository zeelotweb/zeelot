<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class DiscountCategory extends Model
{
    protected $fillable = [
        'name',
        'type',
        'value',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'value' => 'decimal:2',
            'is_active' => 'boolean',
        ];
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<DiscountCode, $this>
     */
    public function codes()
    {
        return $this->hasMany(DiscountCode::class);
    }

    public function apply(float $amount): float
    {
        $discount = $this->type === 'percentage'
            ? $amount * ($this->value / 100)
            : (float) $this->value;

        return min($discount, $amount);
    }
}
