<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class CashMutation extends Model
{
    public const IN = 'in';
    public const OUT = 'out';

    protected $fillable = [
        'direction', 'amount', 'method', 'description', 'source_type', 'source_id', 'user_id', 'occurred_at',
    ];

    protected function casts(): array
    {
        return ['amount' => 'integer', 'occurred_at' => 'datetime'];
    }

    public function source(): MorphTo
    {
        return $this->morphTo();
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
