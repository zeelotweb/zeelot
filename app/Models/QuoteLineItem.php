<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuoteLineItem extends Model
{
    protected $fillable = [
        'quote_id',
        'title',
        'description',
        'amount',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
        ];
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<Quote, $this>
     */
    public function quote()
    {
        return $this->belongsTo(Quote::class);
    }
}
