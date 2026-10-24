<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Sale extends Model
{
    protected $fillable = [
        'invoice_no', 'user_id', 'cash_drawer_id', 'subtotal', 'discount', 'total',
        'cogs', 'paid', 'change_amount', 'payment_method', 'sold_at',
    ];

    protected function casts(): array
    {
        return [
            'subtotal' => 'integer', 'discount' => 'integer', 'total' => 'integer',
            'cogs' => 'integer', 'paid' => 'integer', 'change_amount' => 'integer',
            'sold_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function cashDrawer(): BelongsTo
    {
        return $this->belongsTo(CashDrawer::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(SaleItem::class);
    }

    public function cashMutations(): MorphMany
    {
        return $this->morphMany(CashMutation::class, 'source');
    }

    public function getProfitAttribute(): int
    {
        return $this->total - $this->cogs;
    }
}
