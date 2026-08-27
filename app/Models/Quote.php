<?php

namespace App\Models;

use App\Mail\InvitationMail;
use App\Notifications\QuoteSent;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class Quote extends Model
{
    protected $fillable = [
        'lead_id',
        'created_by',
        'token',
        'status',
        'note',
        'valid_until',
        'sent_at',
        'responded_at',
        'decline_reason',
        'signature_name',
        'signed_at',
    ];

    protected function casts(): array
    {
        return [
            'valid_until' => 'datetime',
            'sent_at' => 'datetime',
            'responded_at' => 'datetime',
            'signed_at' => 'datetime',
        ];
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<Lead, $this>
     */
    public function lead()
    {
        return $this->belongsTo(Lead::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<User, $this>
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<QuoteLineItem, $this>
     */
    public function lineItems()
    {
        return $this->hasMany(QuoteLineItem::class)->orderBy('sort_order');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne<Project, $this>
     */
    public function project()
    {
        return $this->hasOne(Project::class);
    }

    public function total(): float
    {
        return (float) $this->lineItems()->sum('amount');
    }

    public function requiresSignature(): bool
    {
        return $this->total() > 0;
    }

    public function isExpired(): bool
    {
        return $this->status === 'sent' && $this->valid_until && $this->valid_until->isPast();
    }

    /**
     * @param  array<int, array{title: string, description?: string|null, amount: float|string}>  $lineItems
     */
    public static function createFor(Lead $lead, array $lineItems, ?User $createdBy = null, ?int $validDays = 14, ?string $note = null): self
    {
        $quote = static::create([
            'lead_id' => $lead->id,
            'created_by' => $createdBy?->id,
            'token' => Str::random(48),
            'status' => 'sent',
            'note' => $note,
            'valid_until' => $validDays ? now()->addDays($validDays) : null,
            'sent_at' => now(),
        ]);

        foreach (array_values($lineItems) as $i => $item) {
            $quote->lineItems()->create([
                'title' => $item['title'],
                'description' => $item['description'] ?? null,
                'amount' => $item['amount'],
                'sort_order' => $i,
            ]);
        }

        $lead->update(['status' => 'quoted']);

        User::where('email', $lead->email)->first()?->notify(new QuoteSent($quote));

        return $quote;
    }

    public function accept(?string $signatureName = null): void
    {
        $requiresSignature = $this->requiresSignature();

        $this->forceFill([
            'status' => 'accepted',
            'responded_at' => now(),
            'signature_name' => $requiresSignature ? $signatureName : null,
            'signed_at' => $requiresSignature ? now() : null,
        ])->save();

        $this->convertToProject();
    }

    public function decline(?string $reason = null): void
    {
        $this->forceFill([
            'status' => 'declined',
            'responded_at' => now(),
            'decline_reason' => $reason,
        ])->save();

        $this->lead->update(['status' => 'declined']);
    }

    /**
     * Turn an accepted quote into a project, seeding milestones from its line
     * items. If no account exists yet for the lead's email, sends a
     * registration invite instead and waits — conversion completes via
     * CreateNewUser once they register.
     */
    public function convertToProject(): ?Project
    {
        if ($this->lead->converted_to_project_id) {
            return $this->lead->project;
        }

        $user = User::where('email', $this->lead->email)->first();

        if (! $user) {
            if (! Invitation::valid()->where('email', $this->lead->email)->exists()) {
                $invitation = Invitation::createFor($this->lead->email, 'customer');
                Mail::to($this->lead->email)->send(new InvitationMail($invitation));
            }

            return null;
        }

        $project = Project::create([
            'user_id' => $user->id,
            'lead_id' => $this->lead->id,
            'quote_id' => $this->id,
            'name' => ($this->lead->company ?: $this->lead->name).' — Project',
            'status' => 'active',
            'is_pro_bono' => $this->lead->is_pro_bono,
        ]);

        foreach ($this->lineItems as $i => $item) {
            ProjectMilestone::create([
                'project_id' => $project->id,
                'title' => $item->title,
                'description' => $item->description,
                'amount' => $item->amount,
                'sort_order' => $i,
                'status' => 'pending',
            ]);
        }

        $this->lead->update([
            'status' => 'converted',
            'converted_to_project_id' => $project->id,
        ]);

        return $project;
    }
}
