<?php

namespace App\Models;

use App\Notifications\MilestoneInvoiced;
use App\Notifications\MilestonePaid;
use Illuminate\Database\Eloquent\Model;

class ProjectMilestone extends Model
{
    protected $fillable = [
        'project_id',
        'title',
        'description',
        'amount',
        'sort_order',
        'status',
        'invoiced_at',
        'paid_at',
        'stripe_checkout_session_id',
        'stripe_payment_intent_id',
        'discount_code_id',
        'discount_amount',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'invoiced_at' => 'datetime',
            'paid_at' => 'datetime',
            'discount_amount' => 'decimal:2',
        ];
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<Project, $this>
     */
    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function markInvoiced(): void
    {
        $this->forceFill([
            'status' => 'invoiced',
            'invoiced_at' => now(),
        ])->save();

        $this->project->user->notify(new MilestoneInvoiced($this));
    }

    public function markPaid(?string $paymentIntentId = null): void
    {
        if ($this->status === 'paid') {
            return;
        }

        $this->forceFill([
            'status' => 'paid',
            'paid_at' => now(),
            'stripe_payment_intent_id' => $paymentIntentId ?? $this->stripe_payment_intent_id,
        ])->save();

        $this->project->user->notify(new MilestonePaid($this));
    }
}
