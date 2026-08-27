<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class DiscountCode extends Model
{
    protected $fillable = [
        'code',
        'discount_category_id',
        'created_by',
        'customer_email',
        'max_uses',
        'used_count',
        'expires_at',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'expires_at' => 'datetime',
            'is_active' => 'boolean',
        ];
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<DiscountCategory, $this>
     */
    public function category()
    {
        return $this->belongsTo(DiscountCategory::class, 'discount_category_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<User, $this>
     */
    public function issuedBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function isValidFor(?string $email = null): bool
    {
        if (! $this->is_active) {
            return false;
        }

        if ($this->expires_at && $this->expires_at->isPast()) {
            return false;
        }

        if ($this->max_uses !== null && $this->used_count >= $this->max_uses) {
            return false;
        }

        if ($this->customer_email && $email && strcasecmp($this->customer_email, $email) !== 0) {
            return false;
        }

        return true;
    }

    public function discountFor(float $amount): float
    {
        return $this->category->apply($amount);
    }

    public function redeem(): void
    {
        $this->increment('used_count');
    }

    public static function generateCode(): string
    {
        do {
            $code = Str::upper(Str::random(8));
        } while (static::where('code', $code)->exists());

        return $code;
    }
}
