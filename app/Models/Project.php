<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    protected $fillable = [
        'user_id',
        'lead_id',
        'quote_id',
        'name',
        'description',
        'status',
        'is_pro_bono',
    ];

    protected function casts(): array
    {
        return [
            'is_pro_bono' => 'boolean',
        ];
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<User, $this>
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<Lead, $this>
     */
    public function lead()
    {
        return $this->belongsTo(Lead::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<Quote, $this>
     */
    public function quote()
    {
        return $this->belongsTo(Quote::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<ProjectMilestone, $this>
     */
    public function milestones()
    {
        return $this->hasMany(ProjectMilestone::class)->orderBy('sort_order');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<ProjectMessage, $this>
     */
    public function messages()
    {
        return $this->hasMany(ProjectMessage::class)->orderBy('created_at');
    }

    public function totalAmount(): float
    {
        return (float) $this->milestones()->sum('amount');
    }

    public function paidAmount(): float
    {
        return (float) $this->milestones()->where('status', 'paid')->sum('amount');
    }
}
