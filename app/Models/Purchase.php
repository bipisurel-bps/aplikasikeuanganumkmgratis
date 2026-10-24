<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Purchase extends Model
{
    protected $fillable = [
        'reference_no', 'supplier_id', 'user_id', 'total', 'payment_method', 'purchased_on', 'note',
    ];

    protected function casts(): array
    {
        return ['purchased_on' => 'date', 'total' => 'integer'];
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(PurchaseItem::class);
    }

    public function cashMutations(): MorphMany
    {
        return $this->morphMany(CashMutation::class, 'source');
    }
}
