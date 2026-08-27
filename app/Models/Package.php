<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Package extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'category',
        'description',
        'price',
        'features',
        'is_active',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'features' => 'array',
            'is_active' => 'boolean',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (Package $package) {
            if (! $package->slug) {
                $package->slug = Str::slug($package->name);
            }
        });
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany<Lead, $this>
     */
    public function leads()
    {
        return $this->belongsToMany(Lead::class);
    }
}
