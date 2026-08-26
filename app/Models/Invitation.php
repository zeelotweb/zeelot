<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Invitation extends Model
{
    protected $fillable = [
        'email',
        'role',
        'token',
        'invited_by_id',
        'accepted_at',
        'expires_at',
    ];

    protected function casts(): array
    {
        return [
            'accepted_at' => 'datetime',
            'expires_at' => 'datetime',
        ];
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<User, $this>
     */
    public function invitedBy()
    {
        return $this->belongsTo(User::class, 'invited_by_id');
    }

    public function scopeValid(Builder $query): Builder
    {
        return $query->whereNull('accepted_at')
            ->where(function ($q) {
                $q->whereNull('expires_at')->orWhere('expires_at', '>', now());
            });
    }

    public static function createFor(string $email, string $role, ?User $invitedBy = null): self
    {
        return static::create([
            'email' => $email,
            'role' => $role,
            'token' => Str::random(64),
            'invited_by_id' => $invitedBy?->id,
            'expires_at' => now()->addDays(7),
        ]);
    }
}
