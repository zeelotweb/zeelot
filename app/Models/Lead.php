<?php

namespace App\Models;

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
    ];

    public function decline(?string $reason = null): void
    {
        $this->update([
            'status' => 'declined',
            'decline_reason' => $reason,
        ]);
    }

    protected function casts(): array
    {
        return [
            'is_pro_bono' => 'boolean',
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
